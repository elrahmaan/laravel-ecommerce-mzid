<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use PDF;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
    public function index()
    {
        $i = 1;
        $users = User::orderBy('role', 'asc')->get();
        return view('admin.user', compact('users', 'i'));
    }
    public function add()
    {
        return view('admin.user_create');
    }
    public function create(Request $request)
    {
        if ($request->file('image')) {
            $image = $request->file('image')->store('/images', 'public');
        }
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'image' => $image,
            'address' => $request->address,
            'city' => $request->city,
            'province' => $request->province,
            'pincode' => $request->pincode,
            'mobile' => $request->mobile
        ]);
        return redirect('/admin/user')->with('success', 'Data berhasil ditambahkan');
    }
    public function show($id)
    {
        $users = User::find($id);
        return view('admin.user_show', compact('users'));
    }
    public function edit($id)
    {
        $users = User::find($id);
        return view('admin.user_edit', compact('users'));
    }
    public function update($id, Request $request)
    {
        $users = User::find($id);
        $users->name = $request->name;
        $users->email = $request->email;
        $users->password = Hash::make($request->password);
        $users->role = $request->role;
        if ($users->image && file_exists(storage_path('app/public/' . $users->image))) {
            \Storage::delete('public' . $users->image);
        }
        $image = $request->file('image')->store('images', 'public');
        $users->image = $image;
        $users->address = $request->address;
        $users->city = $request->city;
        $users->province = $request->province;
        $users->pincode = $request->pincode;
        $users->mobile = $request->mobile;
        $users->save();
        return redirect('/admin/user')->with('success', 'Data berhasil diubah');
    }
    public function delete($id)
    {
        $users = User::find($id);
        $users->delete();
        return redirect('/admin/user')->with('danger', 'Data telah dihapus');;
    }
    public function cetak_pdf()
    {
        $users = User::orderBy('role', 'asc')->get();
        $pdf = PDF::loadview('admin.user_pdf', ['users' => $users]);
        return $pdf->stream();
    }
}
