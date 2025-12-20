<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; 

class PropertiController extends Controller
{
    public function index()
    {
        $properti = DB::table('properti')->get();
        return view('admin.properti.index', compact('properti'));
    }
    
    public function dashboard()
    {
        try {
            $totalProperti = DB::table('properti')->count();
            $propertiTersedia = DB::table('properti')->where('status', 'tersedia')->count();
            $propertiTerisi = DB::table('properti')->where('status', '!=', 'tersedia')->count();
            try {
                $totalPendapatan = DB::table('view_laporan_decasa')->sum('harga');
            } catch (\Exception $e) {
                $totalPendapatan = 0;
            }
            return view('admin.dashboard', compact('totalProperti', 'propertiTersedia', 'propertiTerisi', 'totalPendapatan'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error Dashboard: ' . $e->getMessage());
        }
    }

    public function manage()
    {
        $properti = DB::table('properti')->orderBy('id_properti', 'desc')->get();
        return view('admin.properti.manage', compact('properti'));
    }

    public function create()
    {
        $fasilitas = DB::table('fasilitas')->get();
        $kategori = DB::table('kategori')->get(); 

        return view('admin.properti.create', compact('fasilitas', 'kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nm_properti' => 'required',
            'harga'       => 'required|numeric',
            'id_kategori' => 'required', 
            'foto'        => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'fasilitas'   => 'array' 
        ]);

        try {

            DB::beginTransaction();

            $pathFoto = null;
            if ($request->hasFile('foto')) {
                $pathFoto = $request->file('foto')->store('properti', 'public');
            }

            $id_properti = 'P' . mt_rand(100, 999); 

            DB::table('properti')->insert([
                'id_properti' => $id_properti,
                'nm_properti' => $request->nm_properti,
                'harga'       => $request->harga,
                'alamat'      => $request->alamat,
                'deskripsi'   => $request->deskripsi,
                'status'      => $request->status,
                'id_kategori' => $request->id_kategori, 
                'id_user'     => Auth::user()->id_user ?? 1, 
                'url_foto'    => $pathFoto,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            if ($request->has('fasilitas')) {
                foreach ($request->fasilitas as $idFasilitas) {
                    DB::table('detailfasilitas')->insert([
                        'id_properti'  => $id_properti,
                        'id_fasilitas' => $idFasilitas
                    ]);
                }
            }

            DB::commit(); 

            return redirect()->route('admin.properti.manage')->with('success', 'Properti berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack(); 
            if ($pathFoto) Storage::disk('public')->delete($pathFoto);
            
            return back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $properti = DB::table('properti')->where('id_properti', $id)->first();
        if (!$properti) return redirect()->route('admin.properti.manage');
        
        $fasilitas = DB::table('detailfasilitas')
            ->join('fasilitas', 'detailfasilitas.id_fasilitas', '=', 'fasilitas.id_fasilitas')
            ->where('detailfasilitas.id_properti', $id)
            ->get();
        return view('admin.properti.show', compact('properti', 'fasilitas'));
    }

    public function edit($id) {
        $properti = DB::table('properti')->where('id_properti', $id)->first();
        $fasilitas = DB::table('fasilitas')->get();
        $kategori = DB::table('kategori')->get(); 

        $selectedFasilitas = DB::table('detailfasilitas') 
            ->where('id_properti', $id)
            ->pluck('id_fasilitas')
            ->toArray();

        return view('admin.properti.edit', compact('properti', 'fasilitas', 'selectedFasilitas', 'kategori'));
    }

    public function update(Request $request, $id)
    {
        try {
            $data = [
                'nm_properti' => $request->nm_properti,
                'deskripsi'   => $request->deskripsi,
                'harga'       => $request->harga,
                'alamat'      => $request->alamat,
                'status'      => $request->status,
                'id_kategori' => $request->id_kategori, 
                'updated_at'  => now(),
            ];

            if ($request->hasFile('foto')) {
                $data['url_foto'] = $request->file('foto')->store('properti', 'public');
            }

            DB::table('properti')->where('id_properti', $id)->update($data);
            DB::table('detailfasilitas')->where('id_properti', $id)->delete(); 

            if ($request->has('fasilitas')) {
                foreach ($request->fasilitas as $idFasilitas) {
                    DB::table('detailfasilitas')->insert([
                        'id_properti'  => $id,
                        'id_fasilitas' => $idFasilitas
                    ]);
                }
            }

            return redirect()->route('admin.properti.manage')->with('success', 'Property Updated Successfully!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Update Failed: ' . $e->getMessage());
        }
    }

    public function destroy($id) {
        DB::table('detailfasilitas')->where('id_properti', $id)->delete(); 
        DB::table('properti')->where('id_properti', $id)->delete(); 
        return back()->with('success', 'Data Dihapus');
    }
}