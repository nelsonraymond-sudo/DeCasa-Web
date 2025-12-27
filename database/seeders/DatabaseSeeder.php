<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon; 

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. DATA USER 
        DB::table('users')->insert([
            [
                'id_user'   => 'A0001',
                'nm_user'   => 'DeCasa',
                'email'     => 'admin@decasa.com',
                'pass'      => Hash::make('admin123'),
                'role'      => 'admin',
                'no_hp'     => '08990991531',
                'created_at'=> now(), 'updated_at'=> now()
            ],
            [
                'id_user'   => 'U0001',
                'nm_user'   => 'Sulton',
                'email'     => 'sulton@gmail.com',
                'pass'      => Hash::make('user123'),
                'role'      => 'customer',
                'no_hp'     => '081233334444',
                'created_at'=> now(), 'updated_at'=> now()
            ],
            [
                'id_user'   => 'U0002',
                'nm_user'   => 'Fallen',
                'email'     => 'fallen@gmail.com',
                'pass'      => Hash::make('user123'),
                'role'      => 'customer',
                'no_hp'     => '081255556666',
                'created_at'=> now(), 'updated_at'=> now()
            ]
        ]);
        // 2. DATA KATEGORI
        DB::table('kategori')->insert([
            ['id_kategori' => 'K0001', 'nm_kategori' => 'Rumah', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_kategori' => 'K0002', 'nm_kategori' => 'Apartemen', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_kategori' => 'K0003', 'nm_kategori' => 'Villa',   'created_at'=> now(), 'updated_at'=> now()],
            ['id_kategori' => 'K0004', 'nm_kategori' => 'Kos',   'created_at'=> now(), 'updated_at'=> now()],
        ]);
        // 3. DATA FASILITAS
        DB::table('fasilitas')->insert([
            ['id_fasilitas' => 'F0001', 'nm_fasilitas' => 'Kolam Renang', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_fasilitas' => 'F0002', 'nm_fasilitas' => 'WiFi Super Cepat', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_fasilitas' => 'F0003', 'nm_fasilitas' => 'AC Dingin', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_fasilitas' => 'F0004', 'nm_fasilitas' => 'Garasi Luas', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_fasilitas' => 'F0005', 'nm_fasilitas' => 'Keamanan 24 Jam', 'created_at'=> now(), 'updated_at'=> now()],
        ]);

        // 4. DATA PAYMENT 

        DB::table('payment')->insert([
            ['id_metode' => 'PY001', 'nama_bank' => 'BCA', 'no_rekening' => '1234567890', 'atas_nama' => 'PT DeCasa Properti', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_metode' => 'PY002', 'nama_bank' => 'Mandiri', 'no_rekening' => '0987654321', 'atas_nama' => 'PT DeCasa Properti', 'created_at'=> now(), 'updated_at'=> now()],
        ]);


        // 5. DATA PROPERTI

        DB::table('properti')->insert([
            [
                'id_properti' => 'P0001',
                'id_user'     => 'A0001', 
                'id_kategori' => 'K0001', 
                'nm_properti' => 'Rumah Asri di Palagan',
                'deskripsi'   => 'Rumah cantik dengan taman luas, cocok untuk keluarga muda. Dekat dengan pusat perbelanjaan.',
                'alamat'      => 'Jl. Palagan Tentara Pelajar No.45, Yogyakarta',
                'harga'       => 1500000.00, 
                'status'      => 'available',
                'created_at'  => now(), 'updated_at'=> now()
            ],
            [
                'id_properti' => 'P0002',
                'id_user'     => 'A0001',
                'id_kategori' => 'K0002', 
                'nm_properti' => 'Apartemen Malioboro View',
                'deskripsi'   => 'Unit apartemen lantai tinggi dengan pemandangan kota Yogyakarta. Full furnished.',
                'alamat'      => 'Jl. Malioboro No.10, Yogyakarta',
                'harga'       => 2500000.00,
                'status'      => 'available',
                'created_at'  => now(), 'updated_at'=> now()
            ],
            [
                'id_properti' => 'P0003',
                'id_user'     => 'A0001',
                'id_kategori' => 'K0003', 
                'nm_properti' => 'Villa Kaliurang',
                'deskripsi'   => 'Villa sejuk di puncak dengan 4 kamar tidur, cocok untuk gathering kantor.',
                'alamat'      => 'Jl. Kaliurang No.99, Yogyakarta',
                'harga'       => 5000000.00,
                'status'      => 'full', 
                'created_at'  => now(), 'updated_at'=> now()
            ]
        ]);

        // 6. DATA FOTO (Dummy)

        DB::table('foto')->insert([
            ['id_properti' => 'P0001', 'url_foto' => 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_properti' => 'P0002', 'url_foto' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_properti' => 'P0003', 'url_foto' => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b91d', 'created_at'=> now(), 'updated_at'=> now()],
        ]);

        // 7. DETAIL FASILITAS
  
        DB::table('detailfasilitas')->insert([
            ['id_detail' => 'DF001', 'id_properti' => 'P0001', 'id_fasilitas' => 'F0002', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_detail' => 'DF002', 'id_properti' => 'P0001', 'id_fasilitas' => 'F0004', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_detail' => 'DF003', 'id_properti' => 'P0002', 'id_fasilitas' => 'F0001', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_detail' => 'DF004', 'id_properti' => 'P0002', 'id_fasilitas' => 'F0002', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_detail' => 'DF005', 'id_properti' => 'P0002', 'id_fasilitas' => 'F0003', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_detail' => 'DF006', 'id_properti' => 'P0002', 'id_fasilitas' => 'F0005', 'created_at'=> now(), 'updated_at'=> now()],
        ]);

        // 8. DATA TRANSAKSI (Dummy)
        DB::table('transaksi')->insert([
            [
                'id_trans'    => 'TR001',
                'id_user'     => 'U0001', 
                'id_properti' => 'P0001', 
                'id_metode'   => 'PY001', 
                'tgl_trans'   => Carbon::now(), 
                'checkin'     => Carbon::now()->addDays(1)->format('Y-m-d'), 
                'checkout'    => Carbon::now()->addDays(3)->format('Y-m-d'),
                'durasi'      => 2,
                'total_harga' => 3000000.00, 
                'status'      => 'lunas',
                'created_at'  => now(), 'updated_at'=> now()
            ],
            [
                'id_trans'    => 'TR002',
                'id_user'     => 'U0002', 
                'id_properti' => 'P0002', 
                'id_metode'   => 'PY002', 
                'tgl_trans'   => Carbon::now(), 
                'checkin'     => Carbon::now()->addDays(1)->format('Y-m-d'), 
                'checkout'    => Carbon::now()->addDays(2)->format('Y-m-d'),
                'durasi'      => 1,
                'total_harga' => 2500000.00,
                'status'      => 'lunas', 
                'created_at'  => now(), 'updated_at'=> now()
            ]
        ]);
    }
}