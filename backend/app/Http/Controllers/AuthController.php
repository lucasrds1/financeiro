<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class AuthController extends Controller
{
    public function unauthorized()
    {
        return response()->json(['error' => 'Não autorizado'], 401);
    }

    public function register(Request $request)
    {
        //valida os campos
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()]);
        }
        //cria o usuario
        $user = $this->createUser($request->only(['name', 'email', 'password']));

        //faz login e pega o token
        $token = auth()->attempt([
            'email' => $request->input('email'),
            'password' => $request->input('password')
        ]);

        if (!$token) {
            return response()->json(['error' => 'Ocorreu um erro!']);
        }
        //retorna o token e os dados do usuario
        return response()->json(['token' => $token, 'user' => auth()->user()]);
    }

    public function login(Request $request)
    {
        //valida os campos
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()]);
        }
        //login e pega o token
        $token = auth()->attempt([
            'email' => $request->input('email'),
            'password' => $request->input('password')
        ]);

        if (!$token) {
            return response()->json(['error' => 'Credenciais inválidas!']);
        }

        return response()->json(['token' => $token, 'user' => auth()->user()]);
    }

    public function validateToken()
    {
        //valida o token bearer
        return response()->json(['user' => auth()->user()]);
    }

    private function createUser(array $data)
    {
        //função que cria o usuario utilizando o bcrypt
        $data['password'] = bcrypt($data['password']);

        return User::create($data);
    }
    public function logout(){

        return response()->json(['user'=> auth()->logout()]);
    }
}