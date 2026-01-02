<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $pelanggan = DB::select("
            SELECT 
                u.id_user,
                u.nm_user,
                u.email,
                u.no_hp,
                u.created_at as tgl_daftar,
                (
                    SELECT COUNT(*) 
                    FROM view_booking_history vh 
                    WHERE vh.id_user = u.id_user
                ) as total_booking,
                (
                    SELECT COALESCE(SUM(vh.total_harga), 0) 
                    FROM view_booking_history vh 
                    WHERE vh.id_user = u.id_user 
                    AND vh.status IN ('lunas', 'Lunas', 'selesai', 'Selesai')
                ) as total_spent
            FROM users u
            WHERE u.role = 'customer'
            ORDER BY total_spent DESC
        ");

        foreach ($pelanggan as &$p) {
            $p->total_spent_formatted = 'Rp ' . number_format($p->total_spent, 0, ',', '.');
        }

        return view('admin.customer.index', compact('pelanggan'));
    }
}