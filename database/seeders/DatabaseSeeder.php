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
        // 1. DATA ADMIN 
        DB::table('users')->insert([
            [
                'id_user'     => 'A0001',
                'nm_user'     => 'DeCasa',
                'email'       => 'admin@decasa.com',
                'pass'        => Hash::make('admin'), 
                'role'        => 'admin',
                'no_hp'       => '08990991531', 
                'created_at'  => now(), 
                'updated_at'  => now()
            ]
        ]);

        // 2. DATA KATEGORI 
        DB::table('kategori')->insert([
            ['id_kategori' => 'K0001', 'nm_kategori' => 'House',     'created_at'=> now(), 'updated_at'=> now()],
            ['id_kategori' => 'K0002', 'nm_kategori' => 'Appartment', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_kategori' => 'K0003', 'nm_kategori' => 'Villa',     'created_at'=> now(), 'updated_at'=> now()],
            ['id_kategori' => 'K0004', 'nm_kategori' => 'Costs',       'created_at'=> now(), 'updated_at'=> now()],
        ]);

        // 3. DATA FASILITAS 
        DB::table('fasilitas')->insert([
            ['id_fasilitas' => 'F0001', 'nm_fasilitas' => 'Kolam Renang',     'created_at'=> now(), 'updated_at'=> now()],
            ['id_fasilitas' => 'F0002', 'nm_fasilitas' => 'WiFi Super Cepat', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_fasilitas' => 'F0003', 'nm_fasilitas' => 'AC Dingin',        'created_at'=> now(), 'updated_at'=> now()],
            ['id_fasilitas' => 'F0004', 'nm_fasilitas' => 'Garasi Luas',      'created_at'=> now(), 'updated_at'=> now()],
            ['id_fasilitas' => 'F0005', 'nm_fasilitas' => 'Keamanan 24 Jam',  'created_at'=> now(), 'updated_at'=> now()],
        ]);

        // 4. DATA PAYMENT 
        DB::table('payment')->insert([
            ['id_metode' => 'PY001', 'nama_bank' => 'BCA',     'no_rekening' => '1234567890', 'atas_nama' => 'PT DeCasa Properti', 'created_at'=> now(), 'updated_at'=> now()],
            ['id_metode' => 'PY002', 'nama_bank' => 'Mandiri', 'no_rekening' => '0987654321', 'atas_nama' => 'PT DeCasa Properti', 'created_at'=> now(), 'updated_at'=> now()],
        ]);
    }
}