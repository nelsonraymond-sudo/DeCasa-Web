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
        -- BAGIAN 1: FUNCTION CEK KETERSEDIAAN
        DROP FUNCTION IF EXISTS cek_ketersediaan;
        
        CREATE FUNCTION cek_ketersediaan(
            -- KITA PAKSA PARAMETER AGAR SAMA DENGAN TABEL
            p_id_properti VARCHAR(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci, 
            p_checkin DATE, 
            p_checkout DATE
        ) 
        RETURNS VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
        DETERMINISTIC
        BEGIN
            DECLARE v_status_properti VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
            DECLARE count_bentrok INT;

            -- Ambil status properti
            SELECT status INTO v_status_properti
            FROM properti
            WHERE id_properti = p_id_properti; -- Aman karena p_id_properti sudah diset general_ci

            -- Cek bentrok tanggal
            SELECT COUNT(*) INTO count_bentrok
            FROM transaksi
            WHERE id_properti = p_id_properti -- Aman
            AND status IN ('lunas', 'pending')
            AND status != 'batal'
            AND (
                (p_checkin < checkout AND p_checkout > checkin)
            );
            
            IF count_bentrok > 0 THEN
                RETURN 'penuh';
            ELSE
                RETURN 'tersedia'; 
            END IF;
        END;
    ");

        // 4. FUNCTION: GENERATE ID CUSTOMER
        DB::unprepared("
            DROP FUNCTION IF EXISTS generate_id_customer;
            CREATE FUNCTION generate_id_customer() 
            RETURNS VARCHAR(5)
            DETERMINISTIC
            BEGIN
                DECLARE last_id VARCHAR(5);
                DECLARE last_number INT;
                DECLARE new_id VARCHAR(5);
                
                SELECT MAX(id_user) INTO last_id
                FROM users
                WHERE id_user LIKE 'U%' AND LENGTH(id_user) = 5;
                
                IF last_id IS NULL THEN
                    SET new_id = 'U0001';
                ELSE
                    SET last_number = CAST(SUBSTRING(last_id, 2) AS UNSIGNED);
                    SET new_id = CONCAT('U', LPAD(last_number + 1, 4, '0'));
                END IF;
                
                RETURN new_id;
            END;
        ");

        // 5. FUNCTION: GENERATE KODE PROPERTI 
        DB::unprepared("
            DROP FUNCTION IF EXISTS generate_kode_properti;
            CREATE FUNCTION generate_kode_properti() 
            RETURNS VARCHAR(5)
            DETERMINISTIC
            BEGIN
                DECLARE last_id VARCHAR(5);
                DECLARE last_number INT;
                DECLARE new_id VARCHAR(5);
                
                SELECT MAX(id_properti) INTO last_id
                FROM properti
                WHERE id_properti LIKE 'P%' AND LENGTH(id_properti) = 5;
                
                IF last_id IS NULL THEN
                    SET new_id = 'P0001';
                ELSE
                    SET last_number = CAST(SUBSTRING(last_id, 2) AS UNSIGNED);
                    SET new_id = CONCAT('P', LPAD(last_number + 1, 4, '0'));
                END IF;
                    
                RETURN new_id;
            END;
        ");

        // 6. FUNCTION: GENERATE KODE TRANSAKSI
        DB::unprepared("
    DROP FUNCTION IF EXISTS generate_kd_trx;
    CREATE FUNCTION generate_kd_trx()
    RETURNS VARCHAR(20)
    DETERMINISTIC
    BEGIN
        DECLARE v_tanggal VARCHAR(8);
        DECLARE urutan INT;
        DECLARE kode VARCHAR(13);
        DECLARE max_id VARCHAR(13);

        -- 1. Ambil tanggal hari ini (Format: 251227)
        SET v_tanggal = DATE_FORMAT(CURDATE(), '%y%m%d');

        -- 2. Cari ID paling besar hari ini (Logic MAX, bukan COUNT)
        -- Kita pakai COLLATE agar tidak error 'Illegal mix of collations'
        SELECT MAX(id_trans) INTO max_id
        FROM transaksi
        WHERE id_trans COLLATE utf8mb4_general_ci LIKE CONCAT('TRX', v_tanggal, '-%') COLLATE utf8mb4_general_ci;

        -- 3. Cek Logic Urutan
        IF max_id IS NULL THEN
            -- Jika belum ada transaksi hari ini, mulai dari 1
            SET urutan = 1;
        ELSE
            -- Jika sudah ada, ambil angka di belakang (mulai karakter ke-11)
            -- Contoh: TRX251227-005 -> ambil '005' -> jadi 5 -> tambah 1 = 6
            SET urutan = CAST(SUBSTRING(max_id, 11) AS UNSIGNED) + 1;
        END IF;

        -- 4. Gabungkan (Padding 0 di depan angka)
        SET kode = CONCAT('TRX', v_tanggal, '-', LPAD(urutan, 3, '0'));

        RETURN kode;
    END
");
        // 7. FUNCTION: GENERATE ID ADMIN
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
        DB::unprepared("DROP FUNCTION IF EXISTS generate_id_admin");
    }
};