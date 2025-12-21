<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. VIEW: PROPERTI TERSEDIA
        DB::statement("
            CREATE OR REPLACE VIEW view_properti_tersedia AS
            SELECT 
                p.id_properti, 
                p.nm_properti AS nama_property, 
                p.deskripsi,
                p.alamat, 
                p.harga,
                k.nm_kategori AS nama_kategori,
                u.nm_user AS owner,
                p.created_at
            FROM properti p
            JOIN kategori k ON p.id_kategori = k.id_kategori
            JOIN users u ON p.id_user = u.id_user
            WHERE p.status = 'tersedia'
        ");

        // 2. VIEW: BOOKING HISTORY
        DB::statement("
            CREATE OR REPLACE VIEW view_booking_history AS
            SELECT 
                t.id_trans AS id_transaksi,
                t.id_user,
                u.nm_user AS nama_customer,
                p.nm_properti AS nama_properti,
                t.checkin,
                t.checkout,
                t.durasi,
                t.total_harga,
                t.status,
                t.created_at AS tanggal_book
            FROM transaksi t
            JOIN properti p ON t.id_properti = p.id_properti
            JOIN users u ON t.id_user = u.id_user
        ");

        // 3. VIEW: PENDING PAYMENTS
        DB::statement("
            CREATE OR REPLACE VIEW view_pending_payments AS
            SELECT 
                t.id_trans AS id_transaksi,
                u.nm_user AS nama_customer,
                u.email AS email,
                t.total_harga,
                t.created_at,
                DATEDIFF(NOW(), t.created_at) AS days_elapsed
            FROM transaksi t
            JOIN users u ON t.id_user = u.id_user
            WHERE t.status = 'pending'
        ");

        // 4. VIEW: MONTHLY REVENUE
        DB::statement("
            CREATE OR REPLACE VIEW view_laporan_decasa AS
            SELECT 
                DATE_FORMAT(t.updated_at, '%Y-%m') AS period,
                COUNT(t.id_trans) AS total_transaksi,
                SUM(t.total_harga) AS total_revenue
            FROM transaksi t
            WHERE t.status = 'lunas'
            GROUP BY period
            ORDER BY period DESC
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS view_properti_tersedia");
        DB::statement("DROP VIEW IF EXISTS view_booking_history");
        DB::statement("DROP VIEW IF EXISTS view_pending_payments");
        DB::statement("DROP VIEW IF EXISTS view_laporan_decasa");
    }
};