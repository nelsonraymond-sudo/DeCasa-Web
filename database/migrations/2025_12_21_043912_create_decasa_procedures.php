<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. PROCEDURE: BATALKAN TRANSAKSI
        DB::unprepared("
            DROP PROCEDURE IF EXISTS cancel_booking;
            CREATE PROCEDURE cancel_booking(
                IN p_id_trans VARCHAR(20),
                IN p_alasan VARCHAR(255)
            )
            BEGIN
                DECLARE v_status VARCHAR(20);
                DECLARE v_id_properti VARCHAR(5);
                DECLARE v_checkin DATE;
                
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    SELECT 'ERROR: Terjadi kesalahan sistem' AS message;
                END;
                
                START TRANSACTION;
                
                SELECT status, id_properti, checkin 
                INTO v_status, v_id_properti, v_checkin
                FROM transaksi
                WHERE id_trans = p_id_trans;
                
                IF v_status IS NULL THEN
                    SELECT 'ERROR: Booking tidak ditemukan' AS message;
                    ROLLBACK;
                ELSEIF v_status = 'batal' THEN
                    SELECT 'ERROR: Booking sudah dibatalkan sebelumnya' AS message;
                    ROLLBACK;
                ELSEIF v_status = 'lunas' AND DATEDIFF(v_checkin, CURDATE()) < 2 THEN
                    SELECT 'ERROR: Tidak dapat membatalkan booking H-2 check-in' AS message;
                    ROLLBACK;
                ELSE
                    -- Perbaikan: Menggunakan tabel transaksi
                    UPDATE transaksi 
                    SET status = 'batal' 
                    WHERE id_trans = p_id_trans;
                    
                    UPDATE properti 
                    SET status = 'tersedia' 
                    WHERE id_properti = v_id_properti;
                    
                    COMMIT;
                    SELECT 'SUCCESS: Transaksi berhasil dibatalkan' AS message;
                END IF;
            END;
        ");

        // 2. PROCEDURE: BOOKING PROPERTI
        DB::unprepared("
            DROP PROCEDURE IF EXISTS create_booking;
            CREATE PROCEDURE create_booking(
                IN p_id_user VARCHAR(5),
                IN p_id_properti VARCHAR(5),
                IN p_checkin DATE,
                IN p_checkout DATE,
                IN p_id_metode VARCHAR(5)
            )
            proc_label: BEGIN  
                DECLARE v_durasi INT;
                DECLARE v_total DECIMAL(12,2);
                DECLARE v_availability VARCHAR(20);
                DECLARE v_id_trans VARCHAR(20);
                DECLARE v_harga DECIMAL(12,2);
                
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    SELECT 'ERROR: Gagal memproses booking' AS message;
                END;
                
                START TRANSACTION;

                -- 1. Cek Ketersediaan
                SET v_availability = cek_ketersediaan(p_id_properti, p_checkin, p_checkout);
                
                IF v_availability != 'tersedia' THEN
                    SELECT 'ERROR: Properti tidak tersedia pada tanggal tersebut' AS message;
                    ROLLBACK;
                    LEAVE proc_label;
                END IF;
                
                -- 2. Ambil Harga
                SELECT harga INTO v_harga FROM properti WHERE id_properti = p_id_properti;
                
                -- 3. Hitung Durasi & Total
                SET v_durasi = hitung_durasi(p_checkin, p_checkout);
                SET v_total = v_harga * v_durasi;
                
                -- 4. Generate ID Transaksi
                SET v_id_trans = generate_kd_trx();
                
                -- 5. Insert Data
                 INSERT INTO transaksi (
                    id_trans, id_user, id_properti, id_metode, 
                    tgl_trans, checkin, checkout, durasi, total_harga, status, created_at
                ) VALUES (
                    v_id_trans, p_id_user, p_id_properti, p_id_metode,
                    NOW(), p_checkin, p_checkout, v_durasi, v_total, 'pending', NOW()
                );

                UPDATE properti 
                SET status = 'penuh' 
                WHERE id_properti = p_id_properti;
                
                COMMIT;
                
                SELECT 
                CONCAT('SUCCESS: Booking berhasil. ID Transaksi: ', v_id_trans) AS message,
                v_id_trans AS id_transaksi,
                v_total AS total_pembayaran;
            END;
        ");

        // 3. PROCEDURE: KONFIRMASI PEMBAYARAN
        DB::unprepared("
            DROP PROCEDURE IF EXISTS confirm_payment;
            CREATE PROCEDURE confirm_payment(IN p_id_trans VARCHAR(20))
            BEGIN
                DECLARE v_status VARCHAR(20);
                
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    SELECT 'ERROR: Gagal konfirmasi pembayaran' AS message;
                END;
                
                START TRANSACTION;
                
                SELECT status INTO v_status
                FROM transaksi
                WHERE id_trans = p_id_trans;
                
                IF v_status IS NULL THEN
                    SELECT 'ERROR: Booking tidak ditemukan' AS message;
                    ROLLBACK;
                ELSEIF v_status = 'lunas' THEN
                    SELECT 'ERROR: Booking sudah lunas' AS message;
                    ROLLBACK;
                ELSEIF v_status = 'batal' THEN
                    SELECT 'ERROR: Booking sudah dibatalkan' AS message;
                    ROLLBACK;
                ELSE
                    UPDATE transaksi
                    SET status = 'lunas', updated_at = NOW() 
                    WHERE id_trans = p_id_trans;
                    
                    COMMIT;
                    SELECT 'SUCCESS: Pembayaran berhasil dikonfirmasi' AS message;
                END IF;
            END;
        ");

        // 4. PROCEDURE: REGISTER CUSTOMER
        DB::unprepared("
            DROP PROCEDURE IF EXISTS register_customer;
            CREATE PROCEDURE register_customer(
                IN p_nama VARCHAR(100),
                IN p_email VARCHAR(100),
                IN p_password VARCHAR(255),
                IN p_no_hp VARCHAR(20)
            )
            BEGIN
                DECLARE v_new_id VARCHAR(5);
                DECLARE v_exists INT;
                
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    SELECT 'ERROR: Gagal registrasi' AS message;
                END;
                
                START TRANSACTION;
                
                SELECT COUNT(*) INTO v_exists FROM users WHERE email = p_email;
                
                IF v_exists > 0 THEN
                    SELECT 'ERROR: Email sudah terdaftar' AS message;
                    ROLLBACK;
                ELSE
                    SET v_new_id = generate_id_customer();
                    
                    INSERT INTO users (id_user, nm_user, email, pass, role, no_hp, created_at)
                    VALUES (v_new_id, p_nama, p_email, p_password, 'customer', p_no_hp, now());
                    
                    COMMIT;
                    SELECT CONCAT('SUCCESS: Registrasi berhasil. ID: ', v_new_id) AS message;
                END IF;
            END;
        ");

        // 5. PROCEDURE: ADD PROPERTY 
        DB::unprepared("
            DROP PROCEDURE IF EXISTS add_property;
            
            CREATE PROCEDURE add_property(
                IN p_admin_id VARCHAR(5),
                IN p_kategori VARCHAR(5),
                IN p_nama VARCHAR(100),
                IN p_desc TEXT,
                IN p_alamat TEXT,
                IN p_harga DECIMAL(12,2)
            )
            BEGIN
                DECLARE v_id_properti VARCHAR(5);
                
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                START TRANSACTION;
                
                -- Panggil Function generate_kode_properti yang sudah dibuat di migration sebelumnya
                SET v_id_properti = generate_kode_properti();
                
                -- Insert Data
                INSERT INTO properti (
                    id_properti, id_user, id_kategori, nm_properti, 
                    deskripsi, alamat, harga, status, created_at, updated_at
                ) VALUES (
                    v_id_properti, p_admin_id, p_kategori, p_nama,
                    p_desc, p_alamat, p_harga, 'available', NOW(), NOW()
                );
                
                COMMIT;
                
                -- RETURN ID BARU DENGAN NAMA KOLOM 'id_properti' (PENTING BUAT CONTROLLER)
                SELECT v_id_properti AS id_properti, 'SUCCESS' as message;
            END;
        ");
    
        // 6. PROCEDURE: UPDATE PROFIL
        DB::unprepared("
            DROP PROCEDURE IF EXISTS update_profile;
            CREATE PROCEDURE update_profile(
                IN p_id_user VARCHAR(5),
                IN p_nama VARCHAR(100),
                IN p_email VARCHAR(100),
                IN p_no_hp VARCHAR(20)
            )
            BEGIN
                DECLARE v_email_check INT;
                
                SELECT COUNT(*) INTO v_email_check 
                FROM users 
                WHERE email = p_email AND id_user != p_id_user;
                
                IF v_email_check > 0 THEN
                    SELECT 'ERROR: Email sudah digunakan user lain' AS message;
                ELSE
                    UPDATE users
                    SET 
                        nm_user = COALESCE(p_nama, nm_user),
                        email = COALESCE(p_email, email),
                        no_hp = COALESCE(p_no_hp, no_hp),
                        updated_at = NOW()
                    WHERE id_user = p_id_user;
                    
                    SELECT 'SUCCESS: Profil diperbarui' AS message;
                END IF;
            END;
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS cancel_booking");
        DB::unprepared("DROP PROCEDURE IF EXISTS create_booking");
        DB::unprepared("DROP PROCEDURE IF EXISTS confirm_payment");
        DB::unprepared("DROP PROCEDURE IF EXISTS register_customer");
        DB::unprepared("DROP PROCEDURE IF EXISTS add_property");
        DB::unprepared("DROP PROCEDURE IF EXISTS update_profile");
    }
};