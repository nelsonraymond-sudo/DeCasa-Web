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
                IN p_id_trans VARCHAR(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
                IN p_alasan VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
            )
            BEGIN
                DECLARE v_status VARCHAR(20);
                DECLARE v_checkin DATE;
                
                START TRANSACTION;
                
                SELECT status, checkin INTO v_status, v_checkin
                FROM transaksi
                WHERE id_trans = p_id_trans;
                
                IF v_status IS NULL THEN
                    SELECT 'ERROR: Booking not found' AS message;
                    ROLLBACK;
                ELSEIF v_status = 'batal' THEN
                    SELECT 'ERROR: Booking has been canceled previously.' AS message;
                    ROLLBACK;
                ELSEIF v_status = 'lunas' AND DATEDIFF(v_checkin, CURDATE()) < 2 THEN
                    SELECT 'ERROR: Cannot cancel booking 2 days before check-in' AS message;
                    ROLLBACK;
                ELSE

                    UPDATE transaksi 
                    SET status = 'batal', 
                        updated_at = NOW() 
                    WHERE id_trans = p_id_trans;
                    
                    COMMIT;
                    SELECT 'SUCCESS: Transaction was successfully canceled.' AS message;
                END IF;
            END;
        ");
        // 2. PROCEDURE: BOOKING PROPERTI
        DB::unprepared("
            DROP PROCEDURE IF EXISTS create_booking;
            
            CREATE PROCEDURE create_booking(
                IN p_id_user VARCHAR(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
                IN p_id_properti VARCHAR(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
                IN p_checkin DATE,
                IN p_checkout DATE,
                IN p_id_metode VARCHAR(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
            )
            proc_label: BEGIN  
                DECLARE v_durasi INT;
                DECLARE v_total DECIMAL(12,2);
                DECLARE v_availability VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
                DECLARE v_id_trans VARCHAR(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
                DECLARE v_harga DECIMAL(12,2);
                
                DECLARE EXIT HANDLER FOR SQLEXCEPTION 
                BEGIN
                    ROLLBACK;
                    SELECT 'ERROR: There was a database error during booking.' AS message;
                END;

                START TRANSACTION;

                SET v_availability = cek_ketersediaan(p_id_properti, p_checkin, p_checkout);
                
                IF v_availability != 'tersedia' THEN
                    ROLLBACK;
                    SELECT 'ERROR: Property unavailable (Full on selected dates)' AS message;
                    LEAVE proc_label;
                END IF;
                
                SELECT harga INTO v_harga 
                FROM properti 
                WHERE id_properti = p_id_properti;
                
                SET v_durasi = hitung_durasi(p_checkin, p_checkout);
                SET v_total = v_harga * v_durasi;
                
                SET v_id_trans = generate_kd_trx();
                
                INSERT INTO transaksi (
                    id_trans, id_user, id_properti, id_metode, 
                    tgl_trans, checkin, checkout, durasi, total_harga, status, created_at
                ) VALUES (
                    v_id_trans, p_id_user, p_id_properti, p_id_metode,
                    NOW(), p_checkin, p_checkout, v_durasi, v_total, 'pending', NOW()
                );
                
                COMMIT;
                
                SELECT 
                CONCAT('SUCCESS: Booking success. ID Transaction: ', v_id_trans) AS message,
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
                    SELECT 'ERROR: Failed payment confirmation' AS message;
                END;
                
                START TRANSACTION;
                
                SELECT status INTO v_status
                FROM transaksi
                WHERE id_trans = p_id_trans;
                
                IF v_status IS NULL THEN
                    SELECT 'ERROR: Booking not found' AS message;
                    ROLLBACK;
                ELSEIF v_status = 'lunas' THEN
                    SELECT 'ERROR: Booking has been paid.' AS message;
                    ROLLBACK;
                ELSEIF v_status = 'batal' THEN
                    SELECT 'ERROR: Booking has been canceled.' AS message;
                    ROLLBACK;
                ELSE
                    UPDATE transaksi
                    SET status = 'lunas', updated_at = NOW() 
                    WHERE id_trans = p_id_trans;
                    
                    COMMIT;
                    SELECT 'SUCCESS: Payment successfully confirmed' AS message;
                END IF;
            END;
        ");

        // 4. PROCEDURE: REGISTER CUSTOMER
        DB::unprepared("
            DROP PROCEDURE IF EXISTS register_customer;
            CREATE PROCEDURE register_customer(
                IN p_nama VARCHAR(100),
                IN p_email VARCHAR(100),
                IN p_pass VARCHAR(255),
                IN p_no_hp VARCHAR(20)
            )
            BEGIN
                DECLARE v_new_id VARCHAR(5);
                DECLARE v_exists INT;
                
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    SELECT 'ERROR: Failed registration' AS message;
                END;
                
                START TRANSACTION;
                
                SELECT COUNT(*) INTO v_exists FROM users WHERE email = p_email;
                
                IF v_exists > 0 THEN
                    SELECT 'ERROR: Email has been registered' AS message;
                    ROLLBACK;
                ELSE
                    SET v_new_id = generate_id_customer();
                    
                    INSERT INTO users (id_user, nm_user, email, pass, role, no_hp, created_at)
                    VALUES (v_new_id, p_nama, p_email, p_pass, 'customer', p_no_hp, now());
                    
                    COMMIT;
                    SELECT CONCAT('SUCCESS: Registration success. ID: ', v_new_id) AS message;
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

                    RESIGNAL;
                END;
                
                SET v_id_properti = generate_kode_properti();
                
                INSERT INTO properti (
                    id_properti, id_user, id_kategori, nm_properti, 
                    deskripsi, alamat, harga, status, created_at, updated_at
                ) VALUES (
                    v_id_properti, p_admin_id, p_kategori, p_nama,
                    p_desc, p_alamat, p_harga, 'available', NOW(), NOW()
                );
                
                SELECT v_id_properti AS id_properti, 'SUCCESS' as message;
            END;
        ");
    
        // 6. PROCEDURE: UPDATE PROFIL
        DB::unprepared("
            DROP PROCEDURE IF EXISTS update_profile;
        
        CREATE PROCEDURE update_profile(
            IN p_id_user VARCHAR(5) CHARSET utf8mb4 COLLATE utf8mb4_general_ci,
            IN p_nama VARCHAR(100) CHARSET utf8mb4 COLLATE utf8mb4_general_ci,
            IN p_email VARCHAR(100) CHARSET utf8mb4 COLLATE utf8mb4_general_ci,
            IN p_no_hp VARCHAR(20) CHARSET utf8mb4 COLLATE utf8mb4_general_ci
        )
        BEGIN
            DECLARE v_email_check INT;

            -- Cek email (sekarang aman karena parameter sudah didefinisikan sbg general_ci)
            SELECT COUNT(*) INTO v_email_check
            FROM users
            WHERE email = p_email 
            AND id_user != p_id_user;

            IF v_email_check > 0 THEN
                SELECT 'ERROR: The email address is already being used by another user.' AS message;
            ELSE
                UPDATE users
                SET 
                    nm_user = COALESCE(p_nama, nm_user),
                    email = COALESCE(p_email, email),
                    no_hp = COALESCE(p_no_hp, no_hp),
                    updated_at = NOW()
                WHERE id_user = p_id_user;

                SELECT 'SUCCESS: Profil updated' AS message;
            END IF;
        END
    ");

        // 7. PROCEDURE: SEARCH PROPERTI
        DB::unprepared("
            DROP PROCEDURE IF EXISTS search_property;
            
            CREATE PROCEDURE search_property(
                IN p_alamat VARCHAR(100),
                IN p_kategori VARCHAR(5),
                IN p_range_harga VARCHAR(10) -- 'low', 'medium', 'high', or NULL
            )
            BEGIN
                SET p_alamat = IF(p_alamat = '', NULL, p_alamat);
                SET p_kategori = IF(p_kategori = '', NULL, p_kategori);
                SET p_range_harga = IF(p_range_harga = '', NULL, p_range_harga);

                SELECT * FROM view_detail_properti
                WHERE 
                    (p_alamat IS NULL OR alamat LIKE CONCAT('%', p_alamat, '%') OR nm_properti LIKE CONCAT('%', p_alamat, '%'))
                    
                    AND 
                    (p_kategori IS NULL OR nm_kategori = p_kategori OR id_kategori = p_kategori)
                    
                    AND 
                    (
                        p_range_harga IS NULL 
                        OR (p_range_harga = 'low' AND harga < 5000000)
                        OR (p_range_harga = 'medium' AND harga BETWEEN 5000000 AND 10000000)
                        OR (p_range_harga = 'high' AND harga > 10000000)
                    );
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
        DB::unprepared("DROP PROCEDURE IF EXISTS search_property");
    }
};