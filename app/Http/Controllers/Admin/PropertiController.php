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
        $properti = DB::table('view_detail_properti')
                ->orderBy('id_properti', 'desc') 
                ->get();

    return view('admin.properti.index', compact('properti'));
}
    
    public function manage()
    {
        $properti = DB::table('properti')
            ->join('kategori', 'properti.id_kategori', '=', 'kategori.id_kategori')
            ->select('properti.*', 'kategori.nm_kategori')
            ->orderBy('id_properti', 'desc')
            ->get();

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
            'id_kategori' => 'required',
            'harga'       => 'required|numeric',
            'deskripsi'   => 'required',
            'alamat'      => 'required',
            'status'      => 'required',
            'fotos'       => 'required|array|min:1',
            'fotos.*'     => 'image|mimes:jpeg,png,jpg,gif|max:5000',
            'latitude'    => 'nullable',
            'longitude'   => 'nullable',
        ]);

        try {
            DB::beginTransaction(); 

            $adminId = Auth::user()->id_user;

            $query = DB::select('CALL add_property(?, ?, ?, ?, ?, ?)', [
                $adminId,
                $request->id_kategori,
                $request->nm_properti,
                $request->deskripsi,
                $request->alamat,
                $request->harga
            ]);

            $result = $query[0];

            if (isset($result->message) && str_contains($result->message, 'ERROR')) {
                throw new \Exception($result->message);
            }

            $idBaru = $result->id_properti;

            DB::table('properti')
            ->where('id_properti', $idBaru)
            ->update([
                'status' => $request->status,
                'latitude'   => $request->latitude,   
                'longitude'  => $request->longitude,
                'updated_at' => now() 
            ]);

            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $file) {
                    $path = $file->store('properti', 'public');
                    DB::table('foto')->insert([
                        'id_properti' => $idBaru,
                        'url_foto'    => $path,
                        'created_at'  => now()
                    ]);
                }
            }

            if ($request->has('fasilitas')) {
                foreach ($request->fasilitas as $idFasilitas) {
                    DB::table('detailfasilitas')->insert([
                        'id_properti'  => $idBaru,
                        'id_fasilitas' => $idFasilitas
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.properti.index')->with('success', 'Properties successfully added!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $properti = DB::table('properti')
            ->join('kategori', 'properti.id_kategori', '=', 'kategori.id_kategori')
            ->where('properti.id_properti', $id)
            ->first();

        if (!$properti) {
            return redirect()->route('admin.properti.index')->with('error', 'Properties not found.');
        }

        $fotos = DB::table('foto')->where('id_properti', $id)->get();
        
        $fasilitas = DB::table('detailfasilitas')
            ->join('fasilitas', 'detailfasilitas.id_fasilitas', '=', 'fasilitas.id_fasilitas')
            ->where('detailfasilitas.id_properti', $id)
            ->get();

        $properti->url_foto = $fotos->isNotEmpty() ? $fotos->first()->url_foto : '';

        return view('admin.properti.show', compact('properti', 'fotos', 'fasilitas'));
    }

    public function edit($id) {
        $properti = DB::table('properti')->where('id_properti', $id)->first();

        if (!$properti) {
            return redirect()->back()->with('error', 'Properties not found.');
        }

        $fasilitas = DB::table('fasilitas')->get();
        $kategori = DB::table('kategori')->get();
        
        $selectedFasilitas = DB::table('detailfasilitas') 
            ->where('id_properti', $id)
            ->pluck('id_fasilitas')
            ->toArray();

        $fotos = DB::table('foto')->where('id_properti', $id)->get();

        return view('admin.properti.edit', compact('properti', 'fasilitas', 'selectedFasilitas', 'kategori', 'fotos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nm_properti' => 'required',
            'id_kategori' => 'required',
            'harga'       => 'required|numeric',
            'deskripsi'   => 'required',
            'alamat'      => 'required',
            'status'      => 'required',
            'new_fotos.*' => 'image|mimes:jpeg,png,jpg,gif|max:5000',
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
        ]);

        try {
            DB::beginTransaction();

            DB::table('properti')->where('id_properti', $id)->update([
                'id_kategori' => $request->id_kategori,
                'nm_properti' => $request->nm_properti,
                'deskripsi'   => $request->deskripsi,
                'alamat'      => $request->alamat,
                'harga'       => $request->harga,
                'status'      => $request->status, 
                'updated_at'  => now()
            ]);

            DB::table('detailfasilitas')->where('id_properti', $id)->delete();
            
            if ($request->has('fasilitas')) {
                foreach ($request->fasilitas as $idFasilitas) {
                    DB::table('detailfasilitas')->insert([
                        'id_properti'  => $id,
                        'id_fasilitas' => $idFasilitas
                    ]);
                }
            }

            if ($request->has('delete_fotos')) {
                foreach ($request->delete_fotos as $urlFoto) {
                    if (Storage::disk('public')->exists($urlFoto)) {
                        Storage::disk('public')->delete($urlFoto);
                    }
                    DB::table('foto')
                        ->where('id_properti', $id)
                        ->where('url_foto', $urlFoto)
                        ->delete();
                }
            }

            if ($request->hasFile('new_fotos')) {
                foreach ($request->file('new_fotos') as $file) {
                    $path = $file->store('properti', 'public');
                    DB::table('foto')->insert([
                        'id_properti' => $id,
                        'url_foto'    => $path,
                        'created_at'  => now()
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.properti.manage')->with('success', 'Properties succesfully updated!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id) {
        try {
            DB::beginTransaction();

            $fotos = DB::table('foto')->where('id_properti', $id)->get();
            
            foreach($fotos as $f) {
                if(Storage::disk('public')->exists($f->url_foto)) {
                    Storage::disk('public')->delete($f->url_foto);
                }
            }
            
            DB::table('detailfasilitas')->where('id_properti', $id)->delete(); 
            DB::table('foto')->where('id_properti', $id)->delete(); 
            DB::table('properti')->where('id_properti', $id)->delete(); 
            
            DB::commit();
            return back()->with('success', 'Properties successfully deleted!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete: ' . $e->getMessage());
        }
    }
}