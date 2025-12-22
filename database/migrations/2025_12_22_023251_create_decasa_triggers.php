<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. TRIGGER: GENERATE ID CUSTOMER & ADMIN OTOMATIS
        DB::unprepared("
            DROP TRIGGER IF EXISTS tg_generate_id_cust;
            CREATE TRIGGER tg_generate_id_cust BEFORE INSERT ON users
            FOR EACH ROW BEGIN
                -- Hanya generate untuk customer jika ID belum diisi
                IF NEW.role = 'customer' AND (NEW.id_user IS NULL OR NEW.id_user = '') THEN
                    SET NEW.id_user = generate_id_customer();
                END IF;
                
                -- Untuk admin, jika ID belum diisi
                IF NEW.role = 'admin' AND (NEW.id_user IS NULL OR NEW.id_user = '') THEN
                    SET NEW.id_user = CONCAT('A', LPAD(
                        (SELECT IFNULL(MAX(CAST(SUBSTRING(id_user, 2) AS UNSIGNED)), 0) + 1 
                         FROM users WHERE role = 'admin'), 4, '0'));
                END IF;
            END;
        ");

        // 2. TRIGGER: HITUNG DURASI & TOTAL HARGA OTOMATIS
        DB::unprepared("
            DROP TRIGGER IF EXISTS tg_durasi_total_harga;
            CREATE TRIGGER tg_durasi_total_harga BEFORE INSERT ON transaksi
            FOR EACH ROW BEGIN
                DECLARE v_harga_properti DECIMAL(12,2);
                DECLARE v_durasi INT;
                
                -- Generate ID Transaksi jika kosong
                IF NEW.id_trans IS NULL OR NEW.id_trans = '' THEN
                    SET NEW.id_trans = generate_kd_trx();
                END IF;
                
                -- Set Tanggal Transaksi default NOW()
                IF NEW.tgl_trans IS NULL THEN
                    SET NEW.tgl_trans = NOW();
                END IF;
            
                -- Ambil harga properti
                SELECT harga INTO v_harga_properti
                FROM properti
                WHERE id_properti = NEW.id_properti;
                
                -- Hitung durasi
                SET v_durasi = DATEDIFF(NEW.checkout, NEW.checkin);
                IF v_durasi < 1 THEN
                    SET v_durasi = 1;
                END IF;
                
                -- Set nilai ke row baru
                SET NEW.durasi = v_durasi;
                SET NEW.total_harga = v_harga_properti * v_durasi;
            END;
        ");

        // 3. TRIGGER: HITUNG ULANG SAAT UPDATE 
        DB::unprepared("
            DROP TRIGGER IF EXISTS tg_validasi_durasi_totalharga;
            CREATE TRIGGER tg_validasi_durasi_totalharga BEFORE UPDATE ON transaksi
            FOR EACH ROW BEGIN
                DECLARE v_harga_properti DECIMAL(12,2);
                
                -- Hanya jalankan jika tanggal berubah
                IF OLD.checkin != NEW.checkin OR OLD.checkout != NEW.checkout THEN
                    
                    SELECT harga INTO v_harga_properti
                    FROM properti
                    WHERE id_properti = NEW.id_properti;
                    
                    SET NEW.durasi = DATEDIFF(NEW.checkout, NEW.checkin);
                    
                    IF NEW.durasi < 1 THEN
                        SET NEW.durasi = 1;
                    END IF;
                    
                    SET NEW.total_harga = v_harga_properti * NEW.durasi;
                END IF;
            END;
        ");

        // 4. TRIGGER: VALIDASI TANGGAL BOOKING
        DB::unprepared("
            DROP TRIGGER IF EXISTS tg_validasi_tgl_book;
            CREATE TRIGGER tg_validasi_tgl_book BEFORE INSERT ON transaksi
            FOR EACH ROW BEGIN
                DECLARE tabrakan INT;
                
                -- Validasi 1: Checkout harus setelah checkin
                IF NEW.checkout <= NEW.checkin THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Tanggal checkout harus setelah tanggal checkin';
                END IF;
                
                -- Validasi 2: Checkin tidak boleh masa lalu
                IF NEW.checkin < CURDATE() THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Tanggal checkin tidak boleh di masa lalu';
                END IF;
                
                -- Validasi 3: Cek tabrakan jadwal
                SELECT COUNT(*) INTO tabrakan
                FROM transaksi
                WHERE id_properti = NEW.id_properti
                  AND status IN ('pending', 'lunas')
                  AND (
                      (NEW.checkin BETWEEN checkin AND checkout) OR
                      (NEW.checkout BETWEEN checkin AND checkout) OR
                      (checkin BETWEEN NEW.checkin AND NEW.checkout)
                  );
                
                IF tabrakan > 0 THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'GAGAL: Tanggal yang dipilih bertabrakan dengan booking lain';
                END IF;
            END;
        ");

        // 5. TRIGGER: UPDATE STATUS PROPERTI SAAT BOOKING MASUK
        DB::unprepared("
            DROP TRIGGER IF EXISTS tg_after_insert_transaksi;
            CREATE TRIGGER tg_after_insert_transaksi AFTER INSERT ON transaksi
            FOR EACH ROW BEGIN
                IF NEW.status IN ('pending', 'lunas') THEN
                    UPDATE properti 
                    SET status = 'penuh', updated_at = NOW()
                    WHERE id_properti = NEW.id_properti;
                END IF;
            END;
        ");

        // 6. TRIGGER: UPDATE STATUS PROPERTI SAAT STATUS TRANSAKSI BERUBAH
        DB::unprepared("
            DROP TRIGGER IF EXISTS tg_after_update_transaksi;
            CREATE TRIGGER tg_after_update_transaksi AFTER UPDATE ON transaksi
            FOR EACH ROW BEGIN
                DECLARE ada_transaksi_aktif INT;
                
                -- KASUS 1: Transaksi selesai atau dibatalkan
                IF (OLD.status IN ('pending', 'lunas') AND NEW.status IN ('batal', 'selesai')) THEN
                   
                   -- Cek apakah masih ada transaksi AKTIF LAIN untuk properti ini di masa depan/sekarang?
                   -- (Kita tidak mau set 'tersedia' jika ternyata masih ada booking lain yang nunggu)
                   SELECT COUNT(*) INTO ada_transaksi_aktif
                   FROM transaksi
                   WHERE id_properti = NEW.id_properti
                     AND status IN ('pending', 'lunas')
                     AND id_trans != NEW.id_trans;
                   
                   -- Jika benar-benar kosong, baru set tersedia
                   IF ada_transaksi_aktif = 0 THEN
                       UPDATE properti 
                       SET status = 'tersedia', updated_at = NOW()
                       WHERE id_properti = NEW.id_properti;
                   END IF;
                   
                -- KASUS 2: Transaksi batal diaktifkan kembali (Re-open)
                ELSEIF OLD.status = 'batal' AND NEW.status IN ('pending', 'lunas') THEN
                    UPDATE properti 
                    SET status = 'penuh', updated_at = NOW()
                    WHERE id_properti = NEW.id_properti;
                END IF;
            END;
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS tg_generate_id_cust");
        DB::unprepared("DROP TRIGGER IF EXISTS tg_durasi_total_harga");
        DB::unprepared("DROP TRIGGER IF EXISTS tg_validasi_durasi_totalharga");
        DB::unprepared("DROP TRIGGER IF EXISTS tg_validasi_tgl_book");
        DB::unprepared("DROP TRIGGER IF EXISTS tg_after_insert_transaksi");
        DB::unprepared("DROP TRIGGER IF EXISTS tg_after_update_transaksi");
    }
};