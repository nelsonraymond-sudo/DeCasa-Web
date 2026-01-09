<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. TRIGGER: GENERATE ID CUSTOMER & ADMIN 
        DB::unprepared("
            DROP TRIGGER IF EXISTS tg_generate_id_cust;
            CREATE TRIGGER tg_generate_id_cust BEFORE INSERT ON users
            FOR EACH ROW BEGIN
                IF NEW.role = 'customer' AND (NEW.id_user IS NULL OR NEW.id_user = '') THEN
                    SET NEW.id_user = generate_id_customer();
                END IF;
                
                IF NEW.role = 'admin' AND (NEW.id_user IS NULL OR NEW.id_user = '') THEN
                    SET NEW.id_user = CONCAT('A', LPAD(
                        (SELECT IFNULL(MAX(CAST(SUBSTRING(id_user, 2) AS UNSIGNED)), 0) + 1 
                         FROM users WHERE role = 'admin'), 4, '0'));
                END IF;
            END;
        ");

        // 2. TRIGGER: HITUNG DURASI & TOTAL HARGA 
        DB::unprepared("
            DROP TRIGGER IF EXISTS tg_durasi_total_harga;
            CREATE TRIGGER tg_durasi_total_harga BEFORE INSERT ON transaksi
            FOR EACH ROW BEGIN
                DECLARE v_harga_properti DECIMAL(12,2);
                DECLARE v_durasi INT;
                
                IF NEW.id_trans IS NULL OR NEW.id_trans = '' THEN
                    SET NEW.id_trans = generate_kd_trx();
                END IF;
                
                IF NEW.tgl_trans IS NULL THEN
                    SET NEW.tgl_trans = NOW();
                END IF;
            
                SELECT harga INTO v_harga_properti
                FROM properti
                WHERE id_properti = NEW.id_properti;
                
                SET v_durasi = DATEDIFF(NEW.checkout, NEW.checkin);
                IF v_durasi < 1 THEN
                    SET v_durasi = 1;
                END IF;
                
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
                
                IF NEW.checkout <= NEW.checkin THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Tanggal checkout harus setelah tanggal checkin';
                END IF;
                
                IF NEW.checkin < CURDATE() THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Tanggal checkin tidak boleh di masa lalu';
                END IF;
                
                SELECT COUNT(*) INTO tabrakan
                FROM transaksi
                WHERE id_properti = NEW.id_properti
                AND status IN ('pending', 'lunas')
                AND (
                    NEW.checkin < checkout AND NEW.checkout > checkin
                );
                
                IF tabrakan > 0 THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'GAGAL: Tanggal yang dipilih bertabrakan dengan booking lain';
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
    }
};