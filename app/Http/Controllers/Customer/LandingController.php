<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class LandingController extends Controller
{
   public function index()
    {
        $kategori = DB::table('kategori')->get();
        
        $properti = DB::table('properti')
                    ->join('kategori', 'properti.id_kategori', '=', 'kategori.id_kategori')
                    ->leftJoin('foto', function($join) {
                        $join->on('properti.id_properti', '=', 'foto.id_properti')
                             ->whereRaw('foto.id_foto = (select min(id_foto) from foto where id_properti = properti.id_properti)');
                    })
                    ->select('properti.*', 'kategori.nm_kategori', 'foto.url_foto')
                    ->orderBy('properti.created_at', 'desc')
                    ->limit(6) 
                    ->get();

        $services = [
            ['icon' => 'bi-search', 'judul' => 'Looking For Rent', 'desc' => 'Find your dream property easily through our platform.'],
            ['icon' => 'bi-house', 'judul' => 'Rent a Place', 'desc' => 'Wide selection of properties to choose from.'],
            ['icon' => 'bi-headset', 'judul' => 'Service 24/7', 'desc' => 'Our support team is available around the clock to assist you.'],
           
        ];

        $cuaca = null;
    try {
        $apiKey = 'b9ef90e3f027f446d5f67152cde5e37d'; 
        $lat = '-7.7956'; // Jogja
        $lon = '110.3695';
        $response = Http::get("https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric&lang=id");
        $cuaca = $response->json();
    } catch (\Exception $e) {
        $cuaca = null;
    }

        $reviews = [
            ['nama' => 'Yoana Fallen', 'role' => 'Customer', 'isi' => 'Pelayanannya cepatnyoo, rumah pun sesuai foto!'],
            ['nama' => 'Surya Seafood', 'role' => 'Customer', 'isi' => 'Top Markotop banget harga standar, sukses terus.'],
            ['nama' => 'Allaudya Annida', 'role' => 'Customer', 'isi' => 'Cari kos dekat amikom jadi gampang banget.'],
        ];

        return view('customer.home', compact('kategori', 'properti', 'services', 'reviews', 'cuaca'));
    }

    public function search(Request $request)
{
    $keyword = $request->input('alamat');   
    $id_kategori = $request->input('kategori'); 

    $query = DB::table('properti')
        ->join('kategori', 'properti.id_kategori', '=', 'kategori.id_kategori')
        ->leftJoin('foto', function($join) {
            $join->on('properti.id_properti', '=', 'foto.id_properti')
                 ->whereRaw('foto.id_foto = (select min(id_foto) from foto where id_properti = properti.id_properti)');
        })
        ->select('properti.*', 'kategori.nm_kategori', 'foto.url_foto');

    if ($keyword) {
        $query->where(function($q) use ($keyword) {
            $q->where('properti.nm_properti', 'like', "%{$keyword}%")
              ->orWhere('properti.alamat', 'like', "%{$keyword}%");
        });
    }

    if ($id_kategori) {
        $query->where('properti.id_kategori', $id_kategori);
    }

    $properti = $query->paginate(10);

    $kategori = DB::table('kategori')->get();

    return view('customer.search', compact('properti', 'kategori', 'keyword', 'id_kategori'));
}

    public function show($id)
    {
        $properti = DB::table('properti')
                    ->join('kategori', 'properti.id_kategori', '=', 'kategori.id_kategori')
                    ->join('users', 'properti.id_user', '=', 'users.id_user')
                    ->where('properti.id_properti', $id) 
                    ->select(
                        'properti.*', 
                        'kategori.nm_kategori',
                        'users.nm_user as pemilik', 
                        'users.no_hp'             
                    )
                    ->first();

        if (!$properti) {
            abort(404); 
        }

        $fotos = DB::table('foto')
                    ->where('id_properti', $id)
                    ->get();

        $fasilitas = DB::table('detailfasilitas')
                    ->join('fasilitas', 'detailfasilitas.id_fasilitas', '=', 'fasilitas.id_fasilitas')
                    ->where('detailfasilitas.id_properti', $id)
                    ->get();

        return view('customer.detail', compact('properti', 'fotos', 'fasilitas'));
    }
}