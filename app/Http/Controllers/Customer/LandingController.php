<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LandingController extends Controller
{
   public function index()
    {
        // 1. DATA SELECT LIST UNTUK SEARCH
        $kategori = DB::table('kategori')->get();
        
        // 2. DATA PROPERTI (DINAMIS DARI ADMIN)
        // Ambil 6 properti terbaru untuk ditampilkan di section "Properties"
        $properti = DB::table('properti')
                    ->join('kategori', 'properti.id_kategori', '=', 'kategori.id_kategori')
                    ->leftJoin('foto', function($join) {
                        $join->on('properti.id_properti', '=', 'foto.id_properti')
                             ->whereRaw('foto.id_foto = (select min(id_foto) from foto where id_properti = properti.id_properti)');
                    })
                    ->select('properti.*', 'kategori.nm_kategori', 'foto.url_foto')
                    ->orderBy('properti.created_at', 'desc')
                    ->limit(6) // Batasi 6 agar tidak terlalu panjang
                    ->get();

        // 3. DATA LAYANAN (SERVICES) - Bisa statis atau dari DB
        // Kita buat array di sini agar view tetap bersih
        $services = [
            ['icon' => 'bi-search', 'judul' => 'Looking For Rent', 'desc' => 'Find your dream property easily through our platform.'],
            ['icon' => 'bi-house', 'judul' => 'Rent a Place', 'desc' => 'Wide selection of properties to choose from.'],
            ['icon' => 'bi-headset', 'judul' => 'Service 24/7', 'desc' => 'Our support team is available around the clock to assist you.'],
           
        ];

        // 4. DATA TESTIMONI (OUR CUSTOMER)
        $reviews = [
            ['nama' => 'Yoana Fallen', 'role' => 'Customer', 'isi' => 'Pelayanannya cepatnyoo, rumah pun sesuai foto!'],
            ['nama' => 'Surya Seafood', 'role' => 'Customer', 'isi' => 'Top Markotop banget harga standar, sukses terus.'],
            ['nama' => 'Allaudya Annida', 'role' => 'Customer', 'isi' => 'Cari kos dekat amikom jadi gampang banget.'],
        ];

        return view('customer.home', compact('kategori', 'properti', 'services', 'reviews'));
    }

    public function search(Request $request)
{
    // 1. Ambil Input dari Form Home
    $keyword = $request->input('alamat');   // Input text
    $id_kategori = $request->input('kategori'); // Input Select Option

    // 2. Query Dasar (Join Tabel)
    $query = DB::table('properti')
        ->join('kategori', 'properti.id_kategori', '=', 'kategori.id_kategori')
        ->leftJoin('foto', function($join) {
            $join->on('properti.id_properti', '=', 'foto.id_properti')
                 ->whereRaw('foto.id_foto = (select min(id_foto) from foto where id_properti = properti.id_properti)');
        })
        ->select('properti.*', 'kategori.nm_kategori', 'foto.url_foto');

    // 3. Filter Logika
    if ($keyword) {
        $query->where(function($q) use ($keyword) {
            $q->where('properti.nm_properti', 'like', "%{$keyword}%")
              ->orWhere('properti.alamat', 'like', "%{$keyword}%");
        });
    }

    if ($id_kategori) {
        $query->where('properti.id_kategori', $id_kategori);
    }

    // 4. Eksekusi Query dengan Pagination (9 data per halaman)
    $properti = $query->paginate(9);

    // 5. Ambil Data Kategori (untuk dropdown filter di halaman search)
    $kategori = DB::table('kategori')->get();

    // 6. Kirim ke View (pastikan nama valuenya 'keyword' bukan 'lokasi' agar di view konsisten)
    return view('customer.search', compact('properti', 'kategori', 'keyword', 'id_kategori'));
}

    // === PERBAIKAN UTAMA DISINI ===
    public function show($id)
    {
        // 1. Ambil Data Properti (Tanpa Foto Dulu)
        $properti = DB::table('properti')
                    ->join('kategori', 'properti.id_kategori', '=', 'kategori.id_kategori')
                    ->join('users', 'properti.id_user', '=', 'users.id_user')
                    ->where('properti.id_properti', $id) // Sesuaikan dengan PK anda
                    ->select(
                        'properti.*', 
                        'kategori.nm_kategori',
                        'users.nm_user as pemilik', // Ambil nama pemilik
                        'users.no_hp'              // Ambil no hp pemilik
                    )
                    ->first();

        // Cek jika data tidak ditemukan
        if (!$properti) {
            abort(404); 
        }

        // 2. Ambil Foto dari tabel 'foto_properti'
        // Karena di tabel properti tidak ada foto, kita wajib ambil dari sini
        $fotos = DB::table('foto')
                    ->where('id_properti', $id)
                    ->get();

        // 3. Ambil Fasilitas
        $fasilitas = DB::table('detailfasilitas')
                    ->join('fasilitas', 'detailfasilitas.id_fasilitas', '=', 'fasilitas.id_fasilitas')
                    ->where('detailfasilitas.id_properti', $id)
                    ->get();

        return view('customer.detail', compact('properti', 'fotos', 'fasilitas'));
    }
}