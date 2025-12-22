<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FasilitasController extends Controller
{
    public function index()
    {
        $fasilitas = DB::table('fasilitas')->orderBy('id_fasilitas', 'desc')->get();
        return view('admin.fasilitas.index', compact('fasilitas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nm_fasilitas' => 'required|string|max:100'
        ]);

        $last = DB::table('fasilitas')->orderBy('id_fasilitas', 'desc')->first();
        
        if (!$last) {
            $newID = 'F0001';
        } else {
            $num = (int) substr($last->id_fasilitas, 1);
            $newID = 'F' . str_pad($num + 1, 4, '0', STR_PAD_LEFT);
        }

        DB::table('fasilitas')->insert([
            'id_fasilitas' => $newID,
            'nm_fasilitas' => $request->nm_fasilitas,
        ]);

        return back()->with('success', 'Fasilitas berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nm_fasilitas' => 'required|string|max:100'
        ]);

        DB::table('fasilitas')
            ->where('id_fasilitas', $id)
            ->update(['nm_fasilitas' => $request->nm_fasilitas]);

        return back()->with('success', 'Fasilitas berhasil diperbarui!');
    }

    public function destroy($id)
    {
        DB::table('detailfasilitas')->where('id_fasilitas', $id)->delete();
        
        DB::table('fasilitas')->where('id_fasilitas', $id)->delete();

        return back()->with('success', 'Fasilitas berhasil dihapus!');
    }
}