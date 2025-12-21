<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. FUNCTION: HITUNG DURASI
        DB::unprepared("
            DROP FUNCTION IF EXISTS hitung_durasi;
            CREATE FUNCTION hitung_durasi(p_checkin DATE, p_checkout DATE) 
            RETURNS INT
            DETERMINISTIC
            BEGIN
                DECLARE durasi INT;
                SET durasi = DATEDIFF(p_checkout, p_checkin);
                IF durasi < 1 THEN SET durasi = 1; END IF;
                RETURN durasi;
            END;
        ");

        // 2. FUNCTION: HITUNG TOTAL HARGA
        DB::unprepared("
            DROP FUNCTION IF EXISTS hitung_total_harga;
            CREATE FUNCTION hitung_total_harga(p_id_properti VARCHAR(5), p_durasi INT) 
            RETURNS DECIMAL(12,2)
            DETERMINISTIC
            BEGIN
                DECLARE v_harga DECIMAL(12,2);
                DECLARE v_total DECIMAL(12,2);
                
                SELECT harga INTO v_harga FROM properti WHERE id_properti = p_id_properti;
                
                IF v_harga IS NULL THEN SET v_harga = 0; END IF;
                
                SET v_total = v_harga * p_durasi;
                RETURN v_total;
            END;
        ");

        // 3. FUNCTION: CEK KETERSEDIAAN
        DB::unprepared("
            DROP FUNCTION IF EXISTS cek_ketersediaan;
            CREATE FUNCTION cek_ketersediaan(p_id_properti VARCHAR(5), p_checkin DATE, p_checkout DATE) 
            RETURNS VARCHAR(20)
            DETERMINISTIC
            BEGIN
                DECLARE v_status_properti VARCHAR(20);
                DECLARE count_bentrok INT;

                SELECT status INTO v_status_properti
                FROM properti
                WHERE id_properti = p_id_properti;

                IF v_status_properti = 'penuh' THEN
                    RETURN 'tidak_tersedia';
                END IF;
                
                SELECT COUNT(*) INTO count_bentrok
                FROM transaksi
                WHERE id_properti = p_id_properti
                AND status IN ('lunas', 'pending')
                AND (
                    (p_checkin BETWEEN checkin AND checkout) OR
                    (p_checkout BETWEEN checkin AND checkout) OR
                    (checkin BETWEEN p_checkin AND p_checkout) OR
                    (checkout BETWEEN p_checkin AND p_checkout)
                );
                  
                IF count_bentrok > 0 THEN
                    RETURN 'tidak_tersedia';
                ELSE
                    RETURN 'tersedia';
                END IF;
            END;
        ");

        // 4. FUNCTION: GENERATE ID CUSTOMER (U0001)
        DB::unprepared("
            DROP FUNCTION IF EXISTS generate_id_customer;
            CREATE FUNCTION generate_id_customer() 
            RETURNS VARCHAR(5)
            DETERMINISTIC
            BEGIN
                DECLARE last_id VARCHAR(5);
                DECLARE last_number INT;
                DECLARE new_id VARCHAR(5);
                
                -- Perbaikan: Menggunakan id_user bukan id
                SELECT MAX(id_user) INTO last_id
                FROM users
                WHERE id_user LIKE 'U%' AND LENGTH(id_user) = 5;
                
                IF last_id IS NULL THEN
                    SET new_id = 'U0001';
                ELSE
                    SET last_number = CAST(SUBSTRING(last_id, 2) AS UNSIGNED);
                    SET new_id = CONCAT('U', LPAD(last_number + 1, 4, '0'));
                    
                    IF last_number >= 9999 THEN
                        RETURN NULL; 
                    END IF;
                END IF;
                
                RETURN new_id;
            END;
        ");

        // 5. FUNCTION: GENERATE KODE PROPERTI (P0001)
        DB::unprepared("
            DROP FUNCTION IF EXISTS generate_kode_properti;
            CREATE FUNCTION generate_kode_properti() 
            RETURNS VARCHAR(5)
            DETERMINISTIC
            BEGIN
                DECLARE last_id VARCHAR(5);
                DECLARE last_number INT;
                DECLARE new_number INT;
                DECLARE new_id VARCHAR(5);
                
                SELECT MAX(id_properti) INTO last_id
                FROM properti
                WHERE id_properti LIKE 'P%'
                AND LENGTH(id_properti) = 5;
                
                IF last_id IS NULL THEN
                    SET new_id = 'P0001';
                ELSE
                    SET last_number = CAST(SUBSTRING(last_id, 2) AS UNSIGNED);
                    SET new_number = last_number + 1;
                    SET new_id = CONCAT('P', LPAD(new_number, 4, '0'));

                    IF new_number > 9999 THEN
                        RETURN NULL;
                    END IF;
                END IF;
                    
                RETURN new_id;
            END;
        ");

        // 6. FUNCTION: GENERATE KODE TRANSAKSI (TRX-YYMMDD-001)
        DB::unprepared("
            DROP FUNCTION IF EXISTS generate_kd_trx;
            CREATE FUNCTION generate_kd_trx() 
            RETURNS VARCHAR(20) -- Perbesar size agar muat
            DETERMINISTIC
            BEGIN
                DECLARE v_tanggal VARCHAR(8);
                DECLARE urutan INT;
                DECLARE kode VARCHAR(20);
                
                SET v_tanggal = DATE_FORMAT(CURDATE(), '%y%m%d');
                
                SELECT COUNT(*) + 1 INTO urutan
                FROM transaksi
                WHERE DATE(created_at) = CURDATE();
                
                SET kode = CONCAT('TRX-', v_tanggal, '-', LPAD(urutan, 3, '0'));
                
                RETURN kode;
            END;
        ");

        // 7. FUNCTION: STATUS BOOKING READABLE
        DB::unprepared("
            DROP FUNCTION IF EXISTS status_booking_customer;
            CREATE FUNCTION status_booking_customer(p_id_trans VARCHAR(20)) 
            RETURNS VARCHAR(100)
            DETERMINISTIC
            BEGIN
                DECLARE v_status VARCHAR(20);
                DECLARE v_checkin DATE;
                DECLARE v_checkout DATE;
                DECLARE result VARCHAR(100);
                
                SELECT status, checkin, checkout 
                INTO v_status, v_checkin, v_checkout
                FROM transaksi
                WHERE id_trans = p_id_trans;
                
                CASE v_status
                    WHEN 'pending' THEN
                        SET result = 'Menunggu Pembayaran';
                    WHEN 'lunas' THEN
                        IF CURDATE() < v_checkin THEN
                            SET result = 'Booking Dikonfirmasi (Belum Check-in)';
                        ELSEIF CURDATE() BETWEEN v_checkin AND v_checkout THEN
                            SET result = 'Sedang Berlangsung';
                        ELSE
                            SET result = 'Selesai';
                        END IF;
                    WHEN 'batal' THEN
                        SET result = 'Dibatalkan';
                    ELSE
                        SET result = 'Status Tidak Dikenali';
                END CASE;
                
                RETURN result;
            END;
            ");
            DB::unprepared("
            DROP FUNCTION IF EXISTS generate_id_admin;
            CREATE FUNCTION generate_id_admin() 
            
            RETURNS VARCHAR(5)
            DETERMINISTIC
            BEGIN
                DECLARE last_id VARCHAR(5);
                DECLARE last_number INT;
                DECLARE new_id VARCHAR(5);
                
                SELECT MAX(id_user) INTO last_id
                FROM users
                WHERE id_user LIKE 'A%' AND LENGTH(id_user) = 5;
                
                IF last_id IS NULL THEN
                    SET new_id = 'A0001';
                ELSE
                    SET last_number = CAST(SUBSTRING(last_id, 2) AS UNSIGNED);
                    SET new_id = CONCAT('A', LPAD(last_number + 1, 4, '0'));
                END IF;
                
                RETURN new_id;
            END;
            ");
    }

    public function down(): void
    {
        DB::unprepared("DROP FUNCTION IF EXISTS hitung_durasi");
        DB::unprepared("DROP FUNCTION IF EXISTS hitung_total_harga");
        DB::unprepared("DROP FUNCTION IF EXISTS cek_ketersediaan");
        DB::unprepared("DROP FUNCTION IF EXISTS generate_id_customer");
        DB::unprepared("DROP FUNCTION IF EXISTS generate_kode_properti");
        DB::unprepared("DROP FUNCTION IF EXISTS generate_kd_trx");
        DB::unprepared("DROP FUNCTION IF EXISTS status_booking_customer");
        DB::unprepared("DROP FUNCTION IF EXISTS generate_id_admin");
    }
};