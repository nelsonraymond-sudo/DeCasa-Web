<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; 

class PropertiController extends Controller
{
    // Menampilkan list semua properti (Tersedia, Terisi, Maintenance)
    public function index()
    {
        // Kita join manual di sini karena view_properti_tersedia tidak menampilkan properti yang 'terisi'
        $properti = DB::table('properti')
            ->join('kategori', 'properti.id_kategori', '=', 'kategori.id_kategori')
            ->select('properti.*', 'kategori.nm_kategori as nama_kategori') // Ambil nama kategori
            ->orderBy('properti.created_at', 'desc')
            ->get();

        return view('admin.properti.index', compact('properti'));
    }

    public function manage()
    {
        // Sama seperti index, admin butuh lihat semua status
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
        // 1. Validasi Input
        $request->validate([
            'nm_properti' => 'required',
            'id_kategori' => 'required',
            'harga'       => 'required|numeric',
            'deskripsi'   => 'required',
            'alamat'      => 'required',
            'status'      => 'required',
            // Validasi Array Foto
            'fotos'       => 'required|array|min:1', 
            'fotos.*'     => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB per foto
            // Validasi Array Fasilitas (Opsional, tapi bagus dicek)
            'fasilitas'   => 'nullable|array'
        ]);

        try {
            DB::beginTransaction(); // Mulai Transaksi Database

            // 2. Simpan Data Utama Properti (Panggil Stored Procedure)
            $adminId = Auth::user()->id_user;
            
            // Param: p_admin_id, p_kategori, p_nama, p_desc, p_alamat, p_harga
            // Note: Status default dari form Anda kirim juga, tapi jika Procedure belum support status, 
            // kita bisa update manual setelah insert. Asumsi procedure Anda return ID properti baru.
            $query = DB::select('CALL add_property(?, ?, ?, ?, ?, ?)', [
                $adminId,
                $request->id_kategori,
                $request->nm_properti,
                $request->deskripsi,
                $request->alamat,
                $request->harga
            ]);

            $result = $query[0];

            // Cek jika procedure mengembalikan error message
            if (isset($result->message) && str_contains($result->message, 'ERROR')) {
                DB::rollBack();
                return back()->with('error', $result->message)->withInput();
            }

            // Ambil ID Properti Baru
            $idBaru = $result->id_properti;

            // Update Status (Karena add_property mungkin defaultnya 'Tersedia')
            if ($request->has('status')) {
                DB::table('properti')
                    ->where('id_properti', $idBaru)
                    ->update(['status' => $request->status]);
            }

            // 3. Loop Simpan Foto-foto ke Tabel 'foto'
            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $file) {
                    // Upload ke folder 'public/properti'
                    $path = $file->store('properti', 'public');

                    // Insert ke tabel database
                    DB::table('foto')->insert([
                        'id_properti' => $idBaru,
                        'url_foto'    => $path,
                        'created_at'  => now()
                    ]);
                }
            }

            // 4. Loop Simpan Fasilitas ke Tabel 'detailfasilitas'
            if ($request->has('fasilitas')) {
                foreach ($request->fasilitas as $idFasilitas) {
                    DB::table('detailfasilitas')->insert([
                        'id_properti'  => $idBaru,
                        'id_fasilitas' => $idFasilitas
                    ]);
                }
            }

            DB::commit(); // Simpan permanen jika semua lancar
            return redirect()->route('admin.properti.index')->with('success', 'Properti berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua perubahan DB jika ada error
            
            // Hapus file foto yang terlanjur ke-upload (Clean up)
            if ($request->hasFile('fotos')) {
                 // Logic hapus file opsional disini
            }

            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        // 1. Ambil Data Properti
        $properti = DB::table('properti')
            ->join('kategori', 'properti.id_kategori', '=', 'kategori.id_kategori')
            ->where('properti.id_properti', $id)
            ->first();

        // 2. Ambil Foto-foto dari tabel child
        $fotos = DB::table('foto')->where('id_properti', $id)->get(); 

        // 3. Ambil Fasilitas
        $fasilitas = DB::table('detailfasilitas')
            ->join('fasilitas', 'detailfasilitas.id_fasilitas', '=', 'fasilitas.id_fasilitas')
            ->where('detailfasilitas.id_properti', $id)
            ->get();

        // --- PERBAIKAN ERROR "Undefined Property" DISINI ---
        // Kita cek apakah ada foto di database?
        if ($fotos->isNotEmpty()) {
            // Jika ada, ambil foto pertama sebagai cover utama
            $properti->url_foto = $fotos->first()->url_foto;
        } else {
            // Jika tidak ada foto sama sekali, set string kosong agar View tidak error
            $properti->url_foto = ''; 
        }

        return view('admin.properti.show', compact('properti', 'fotos', 'fasilitas'));
    }
    public function edit($id) {
        $properti = DB::table('properti')->where('id_properti', $id)->first();
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
        // 1. Validasi Input
        $request->validate([
            'nm_properti' => 'required',
            'id_kategori' => 'required',
            'harga'       => 'required|numeric',
            'deskripsi'   => 'required',
            'alamat'      => 'required',
            'status'      => 'required',
            // Validasi foto baru (jika ada)
            'new_fotos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction(); // Mulai Transaksi

            // 2. Update Data Utama Properti
            // Kita pakai Query Builder update biasa agar lebih stabil
            DB::table('properti')
                ->where('id_properti', $id)
                ->update([
                    'id_kategori' => $request->id_kategori,
                    'nm_properti' => $request->nm_properti,
                    'deskripsi'   => $request->deskripsi,
                    'alamat'      => $request->alamat,
                    'harga'       => $request->harga,
                    'status'      => $request->status,
                    'updated_at'  => now()
                ]);

            // 3. Update Fasilitas (Teknik: Hapus Semua Lama -> Insert Baru)
            // Hapus fasilitas lama milik properti ini
            DB::table('detailfasilitas')->where('id_properti', $id)->delete();
            
            // Insert fasilitas baru yang dicentang
            if ($request->has('fasilitas')) {
                foreach ($request->fasilitas as $idFasilitas) {
                    DB::table('detailfasilitas')->insert([
                        'id_properti'  => $id,
                        'id_fasilitas' => $idFasilitas
                        // id detail akan otomatis auto-increment
                    ]);
                }
            }

            // 4. Hapus Foto yang dicentang (Delete)
            if ($request->has('delete_fotos')) {
                foreach ($request->delete_fotos as $urlFoto) {
                    // Hapus file fisik dari storage
                    if (Storage::disk('public')->exists($urlFoto)) {
                        Storage::disk('public')->delete($urlFoto);
                    }
                    // Hapus record dari database
                    DB::table('foto')
                        ->where('id_properti', $id)
                        ->where('url_foto', $urlFoto)
                        ->delete();
                }
            }

            // 5. Tambah Foto Baru (Upload)
            if ($request->hasFile('new_fotos')) {
                foreach ($request->file('new_fotos') as $file) {
                    // Upload ke folder
                    $path = $file->store('properti', 'public');

                    // Insert ke database
                    DB::table('foto')->insert([
                        'id_properti' => $id,
                        'url_foto'    => $path,
                        'created_at'  => now()
                    ]);
                }
            }

            DB::commit(); // Simpan Permanen

            // Redirect kembali ke halaman manage
            return redirect()->route('admin.properti.manage')
                             ->with('success', 'Data properti berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan jika ada error
            return back()->with('error', 'Gagal update: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id) {
        // Hapus foto fisik dulu (opsional tapi disarankan)
        $fotos = DB::table('foto')->where('id_properti', $id)->get();
        foreach($fotos as $f) {
            if(Storage::disk('public')->exists($f->url_foto)) {
                Storage::disk('public')->delete($f->url_foto);
            }
        }
        
        // Hapus data di DB
        DB::table('detailfasilitas')->where('id_properti', $id)->delete(); 
        DB::table('foto')->where('id_properti', $id)->delete(); 
        DB::table('properti')->where('id_properti', $id)->delete(); 
        
        return back()->with('success', 'Data Dihapus');
    }

    // (Helper function biar script di atas gak error saat di copy, aslinya isi code ini dimasukkan ke method store/update)
    private function storeLogic($request) { /* Isi logic store dr jawaban sebelumnya */ }
    private function updateLogic($request, $id) { /* Isi logic update dr jawaban sebelumnya */ }
}