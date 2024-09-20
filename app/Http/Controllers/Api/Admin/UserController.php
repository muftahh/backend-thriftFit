<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator; //untuk validasi

class UserController extends Controller
{
    public function index() {
        $users = User::when(request()->q, function($users) {
            $users = $users->where('name', 'like', '%'. request()->q .'%');
        })->latest()->paginate(5);

        return new UserResource(true, 'List Data User', $users);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'required|unique:users',
            'password'  => 'required|confirmed'
        ]);
        if ($validator->fails()) {
            return request()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        if ($user) {
            return new UserResource(true, 'Berhasil Menambahkan User', $user);
        }
        return new UserResource(false, 'Gagal Menambahkan User', null);
    }

    public function show ($id) {
        $user = User::whereId($id)->first();

        if ($user) {
            return new UserResource(true, 'Berhasil Menampilakn User', $user);
        }
        return new UserResource(false, 'Gagal Menampilkan User', null);
    }

    public function update(Request $request, User $user) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$user->id,
            'password' => 'confirmed'
        ]);
        if ($validator->fails()) {
            return request()->json($validator->errors(), 422);
        }

        if ($request->password == "") {
            $user->update([
                'name' => $request->name,
                'email' => $request->email
            ]);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        if ($user) {
            return new UserResource(true, 'Berhasil Mengupdate User', $user);
        }
        return new UserResource(false, 'Gagal Mengupdate User', null);
    }

    public function destroy(User $user) {
        if ($user->delete()) {
            return new UserResource(true, 'Berhasil Menghapus User', null);
        }
        return new UserResource(false, 'Gagal Menghapus User', null);
    }
}
