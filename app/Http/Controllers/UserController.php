<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponser;
    protected $admin_password = '$2y$10$NAkGjW8Rg2q3eJLuwyl6BeXFHgcFtjRwLlZVSTK1GfnGBmdap4T66';

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'admin_password' => 'nullable|string',
        ]);


        $isAdmin = Hash::check($request->admin_password, $this->admin_password);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'roles' => $isAdmin ? 'admin' : 'user',
        ]);

        return $this->showOne($user);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $user = User::where('email',$request->email)->firstOrFail();
        if(Hash::check($request->password, $user->password)){
            $token = $user->createToken('Personal Access Token');
            return $this->showOne(['token' => $token->plainTextToken]);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();
        return $this->showOne($token ? true : false);
    }

    public function profile(){
        $user = auth()->user();
        return $this->showOne($user);
    }
}
