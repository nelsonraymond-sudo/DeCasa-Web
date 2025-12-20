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
        // 1. DATA USER (Admin & Customer)
        DB::table('users')->insert([
            [
                'id_user'   => 'A001',
                'nm_user'   => 'Admin',
                'email'     => 'admin@decasa.com',
                'pass'      => Hash::make('admin123'),
                'role'      => 'admin',
                'no_hp'     => '081299998888',
                'created_at'=> now(), 'updated_at'=> now()
            ],
            [
                'id_user'   => 'U001',
                'nm_user'   => 'Sulton',
                'email'     => 'sulton@gmail.com',
                'pass'      => Hash::make('user123'),
                'role'      => 'customer',
                'no_hp'     => '081233334444',
                'created_at'=> now(), 'updated_at'=> now()
            ],
            [
                'id_user'   => 'U002',
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
            ['id_kategori' => 'K001', 'nm_kategori' => 'Rumah Minimalis', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_kategori' => 'K002', 'nm_kategori' => 'Apartemen Mewah', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_kategori' => 'K003', 'nm_kategori' => 'Villa Liburan',   'created_at'=> now(), 'updated_at'=> now()],
        ]);
        // 3. DATA FASILITAS
        DB::table('fasilitas')->insert([
            ['id_fasilitas' => 'F001', 'nm_fasilitas' => 'Kolam Renang', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_fasilitas' => 'F002', 'nm_fasilitas' => 'WiFi Super Cepat', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_fasilitas' => 'F003', 'nm_fasilitas' => 'AC Dingin', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_fasilitas' => 'F004', 'nm_fasilitas' => 'Garasi Luas', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_fasilitas' => 'F005', 'nm_fasilitas' => 'Keamanan 24 Jam', 'created_at'=> now(), 'updated_at'=> now()],
        ]);

        // 4. DATA PAYMENT (Metode Pembayaran)

        DB::table('payment')->insert([
            ['id_metode' => 'PY01', 'nama_bank' => 'BCA', 'no_rekening' => '1234567890', 'atas_nama' => 'PT DeCasa Properti', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_metode' => 'PY02', 'nama_bank' => 'Mandiri', 'no_rekening' => '0987654321', 'atas_nama' => 'PT DeCasa Properti', 'created_at'=> now(), 'updated_at'=> now()],
        ]);


        // 5. DATA PROPERTI

        DB::table('properti')->insert([
            [
                'id_properti' => 'P001',
                'id_user'     => 'A001', 
                'id_kategori' => 'K001', 
                'nm_properti' => 'Rumah Asri di Kemang',
                'deskripsi'   => 'Rumah cantik dengan taman luas, cocok untuk keluarga muda. Dekat dengan pusat perbelanjaan.',
                'alamat'      => 'Jl. Kemang Raya No. 10, Jakarta Selatan',
                'harga'       => 1500000.00, 
                'status'      => 'tersedia',
                'created_at'  => now(), 'updated_at'=> now()
            ],
            [
                'id_properti' => 'P002',
                'id_user'     => 'A001',
                'id_kategori' => 'K002', 
                'nm_properti' => 'Apartemen Sudirman View',
                'deskripsi'   => 'Unit apartemen lantai tinggi dengan pemandangan kota yang menakjubkan. Full furnished.',
                'alamat'      => 'Jl. Jendral Sudirman Kav 50, Jakarta Pusat',
                'harga'       => 2500000.00,
                'status'      => 'tersedia',
                'created_at'  => now(), 'updated_at'=> now()
            ],
            [
                'id_properti' => 'P003',
                'id_user'     => 'A001',
                'id_kategori' => 'K003', 
                'nm_properti' => 'Villa Puncak Pass',
                'deskripsi'   => 'Villa sejuk di puncak dengan 4 kamar tidur, cocok untuk gathering kantor.',
                'alamat'      => 'Jl. Raya Puncak KM 80, Bogor',
                'harga'       => 5000000.00,
                'status'      => 'penuh', 
                'created_at'  => now(), 'updated_at'=> now()
            ]
        ]);

        // 6. DATA FOTO (Dummy)

        DB::table('foto')->insert([
            ['id_properti' => 'P001', 'url_foto' => 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_properti' => 'P002', 'url_foto' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_properti' => 'P003', 'url_foto' => 'https://images.unsplash.com/photo-1580587771525-78b9dba3b91d', 'created_at'=> now(), 'updated_at'=> now()],
        ]);

        // 7. DETAIL FASILITAS
  
        DB::table('detailfasilitas')->insert([
            ['id_detail' => 'DF01', 'id_properti' => 'P001', 'id_fasilitas' => 'F002', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_detail' => 'DF02', 'id_properti' => 'P001', 'id_fasilitas' => 'F004', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_detail' => 'DF03', 'id_properti' => 'P002', 'id_fasilitas' => 'F001', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_detail' => 'DF04', 'id_properti' => 'P002', 'id_fasilitas' => 'F002', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_detail' => 'DF05', 'id_properti' => 'P002', 'id_fasilitas' => 'F003', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_detail' => 'DF06', 'id_properti' => 'P002', 'id_fasilitas' => 'F005', 'created_at'=> now(), 'updated_at'=> now()],
        ]);

        // 8. DATA TRANSAKSI (Dummy)
        DB::table('transaksi')->insert([
            [
                'id_trans'    => 'TR01',
                'id_user'     => 'U001', 
                'id_properti' => 'P001', 
                'id_metode'   => 'PY01', 
                'tgl_trans'   => Carbon::now()->subDays(5), 
                'checkin'     => Carbon::now()->subDays(3)->format('Y-m-d'),
                'checkout'    => Carbon::now()->subDays(1)->format('Y-m-d'),
                'durasi'      => 2,
                'total_harga' => 3000000.00, 
                'status'      => 'selesai',
                'created_at'  => now(), 'updated_at'=> now()
            ],
            [
                'id_trans'    => 'TR02',
                'id_user'     => 'U002', 
                'id_properti' => 'P002', 
                'id_metode'   => 'PY02', 
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