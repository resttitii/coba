<?php
//menghubungkan serta mengatur model dan view agar dapat saling terhubung
namespace App\Http\Controllers;

use App\Models\Keluhan;
use App\Models\Tanggapan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppController extends Controller
{
    public function beranda() //controller beranda => semua aktor
    {
        $data = [
            'title' => 'Beranda',
            'id_page' => 1,
        ];
        return view('beranda', $data);
    }

    public function konsultasi() //controller konsultasi => semua aktor
    {
        $data = [
            'title' => 'Konsultasi',
            'id_page' => 2,
            'keluhan' => Keluhan::with('user')->get(),
        ];

        return view('konsultasi', $data);
    }

    public function toko() //controller toko => semua aktor
    {
        $data = [
            'title' => 'Toko',
            'id_page' => 3,
        ];

        return view('toko', $data);
    }

    public function profile() //controller profile => semua aktor
    {
        $data = [
            'title' => 'Profile',
            'id_page' => 4,
        ];

        return view('profile', $data);
    }

    public function upgrade_role() //controller upgrade role => hanya pelanggan => 3 aktor lainnya tidak memiliki akses untuk upgrade role
    {
        $data = [
            'title' => 'Upgrade Role',
            'id_page' => 5,
        ];

        return view('upgrade_role', $data);
    }

    public function edit_profile($user_id) //controller edit profile => semua aktor kecuali admin dapat melakukan edit profile
    {
        $data = [
            'title' => 'Edit Profile',
            'id_page' => 6,
            'user' => User::find($user_id), //dari id user
        ];

        return view('edit_profile', $data);
    }

    public function update_profile(Request $req, $user_id) //controller update profile
    {
        $user = User::find($user_id);

        //data user yang dapat diedit => nama, username, alamat, dan nomer hp => pass bisa dilakukan dengan reset pass
        if ($req->filled(['name', 'username', 'alamat', 'no_hp'])) {

            $user->name = $req->name;
            $user->username = $req->username;
            $user->alamat = $req->alamat;
            $user->no_hp = $req->no_hp;

            $user->save();

            return redirect()->route('profile')->with('success', 'Berhasil merubah data!'); //pop up => berhasil menyimpan data yang telah diedit
        } else {
            return redirect()->back()->with('warning', 'Pastikan kolom tidak kosong!'); //pop up => gagal melakukan update karen kolom profile ada yang kosong
        }
    }

    public function data_pelanggan() //controller data pelanggan => admin
    {
        $data = [
            'title' => 'Data Pelanggan',
            'id_page' => 7,
            'users' => User::where('role', '=', 'pelanggan')->get(),
        ];

        return view('users', $data);
    }

    public function data_peternak() //controller data peternak => admin
    {
        $data = [
            'title' => 'Data Peternak',
            'id_page' => 8,
            'users' => User::where('role', '=', 'peternak')->get(),
        ];

        return view('users', $data);
    }

    public function data_dokter() //controller data dokter => admin
    {
        $data = [
            'title' => 'Data Dokter',
            'id_page' => 9,
            'users' => User::where('role', '=', 'dokter')->get(),
        ];

        return view('users', $data);
    }

    public function change_role() //controller change role => admin yang doing this activity
    {
        $data = [
            'title' => 'Change Role',
            'id_page' => 10,
            'users' => User::where('role', '!=', 'admin')->get()
        ];

        return view('changerole', $data); //role yang dapat dichange semua role kecuali role admin tidak dapat diubah
    }


    public function update_role($user_id, Request $request) //controller update role
    {
        if ($request->filled('role')) {
            $user = User::find($user_id);
            $user->role = $request->role;

            $user->save();

            return redirect()->back()->with('success', 'Role user berhasil diupdate!'); //role yang direquest user berhasil dihapus
        } else {
            return redirect()->back()->with('warning', 'Inputan tidak boleh kosong!'); //role yang direquest user gagal diupdate karena kesalahan admin yang belum klik pilihan role request
        }
    }

    public function hapus_user($user_id) //controller hapus user => admin
    {
        $user = User::find($user_id);

        $user->delete();

        return redirect()->back()->with('info', 'Satu user berhasil dihapus!'); //akun user berhasil dihapus
    }

    public function tambah_keluhan() //controller tambah keluhan => role pelanggan dan peternak
    {
        $data = [
            'title' => 'Tambah Keluhan',
            'id_page' => 11,
        ];

        return view('manipulasi_keluhan', $data);
    }

    public function buat_keluhan(Request $req)//controller buat keluhan => validasi dari kolo keluhan
    {
        $this->validate($req, [
            'question' => 'required'
        ], [
            'question.required' => 'Kolom input keluhan tidak boleh kosong!' //kolom keluhan tidak boleh kosong
        ]);
        $keluhan = new Keluhan();

        $keluhan->user_id = auth()->user()->user_id;
        $keluhan->question = $req->question;

        $keluhan->save();

        return redirect()->route('konsultasi')->with('success', 'Berhasil mengirim keluhan!'); //pop up bahwa keluhan yang diinputkan berhasil dikirim
    }

    public function hapus_keluhan($keluhan_id)//controller hapus keluhan
    {
        $keluhan = Keluhan::find($keluhan_id);

        $keluhan->delete();

        return back()->with('info', 'Sukses! Keluhan anda berhasil dihapus!'); //pop up bahwa keluhan yang diinputkan berhasil dihapus
    }

    public function tanggapan($keluhan_id) //controller tanggapan 
    {
        $keluhan = Keluhan::find($keluhan_id);
        $data = [
            'title' => 'Tanggapan',
            'id_page' => 12,
            'keluhan' => $keluhan,
            'tanggapan' => Tanggapan::with('user')->where('keluhan_id', $keluhan->keluhan_id)->get()
        ];

        return view('tanggapan', $data);
    }

    public function tambah_tanggapan($keluhan_id) //controller tambah tanggapan => semua role kecuali admin tidak dapat melakukan tanggapan
    {
        $data = [
            'title' => 'Tambah Tanggapan',
            'id_page' => 13,
            'keluhan' => Keluhan::find($keluhan_id),
        ];

        return view('manipulasi_tanggapan', $data);
    }

    public function buat_tanggapan(Request $req) //controller buat tanggapan => validasi dari tambah tanggapan
    {
        $this->validate($req, [
            'comment' => 'required'
        ], [
            'comment.required' => 'Kolom input tanggapan tidak boleh kosong!' //kolom inputnyaa tidak boleh kosong
        ]);
        $tanggapan = new Tanggapan();

        $tanggapan->user_id = auth()->user()->user_id;
        $tanggapan->keluhan_id = $req->keluhan_id;
        $tanggapan->comment = $req->comment;

        $tanggapan->save();

        return redirect('/tanggapan/' . $req->keluhan_id)->with('success', 'Berhasil menanggapi keluhan!'); //pop up berhasil menanggapi keluhan
    }

    public function hapus_tanggapan($tanggapan_id) //controller mengapus tanggapan
    {
        $tanggapan = Tanggapan::find($tanggapan_id);

        $tanggapan->delete();

        return back()->with('info', 'Sukses! Tanggapan anda berhasil dihapus!'); //berhasil menghapus tanggapan
    }

    public function konsultasi_lanjutan() //controller konsultasi lanjutan => oleh peternak dan pelanggan => ke dokter
    {
        $data = [
            'title' => 'Konsultasi Lanjutan',
            'id_page' => 14,
        ];

        return view('konsultasi_lanjutan', $data);
    }

    public function ditanggapi() //controller ditanggapi 
    {
        $data = [
            'title' => 'Telah Ditanggapi',
            'id_page' => 15,
            'keluhan' => Keluhan::with('user')->whereHas('tanggapan')->get(), //halaman keluhan yang sudah mendapatkan tanggapan
        ];

        return view('konsultasi', $data);
    }


    public function belum_ditanggapi() //controller belum ditanggapi
    {
        $data = [
            'title' => 'Belum Ditanggapi',
            'id_page' => 16,
            'keluhan' => Keluhan::with('user')->whereDoesntHave('tanggapan')->get(), //halaman keluhan yang belum mempunyai tanggapan
        ];

        return view('konsultasi', $data);
    }

    public function edit_tanggapan($tanggapan_id, $keluhan_id) //controller edit tanggapan => only doctor
    {
        $data = [
            'title' => 'Edit Tanggapan',
            'id_page' => 17,
            'tanggapan' => Tanggapan::find($tanggapan_id),
            'keluhan' => Keluhan::find($keluhan_id),
        ];

        return view('manipulasi_tanggapan', $data);
    }

    public function update_tanggapan(Request $req, $tanggapan_id) //controller update tanggapan => only doctor 
    {
        $tanggapan = Tanggapan::find($tanggapan_id);
        $old_comment = $tanggapan->comment;

        if ($old_comment != $req->comment) {
            $tanggapan->comment = $req->comment;

            $tanggapan->save();

            return redirect('/tanggapan/' . $req->keluhan_id)->with('info', 'Berhasil memperbarui tanggapan!'); //tanggapan yang diedit berhasil diperbarui
        }

        return redirect('/tanggapan/' . $req->keluhan_id);
    }
    
}
