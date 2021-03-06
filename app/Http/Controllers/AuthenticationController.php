<?php

namespace App\Http\Controllers;

use App\Classes\AuthenticationManagement;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    private $authenticationManagement;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AuthenticationManagement $authenticationManagement)
    {
        //
        $this->authenticationManagement = $authenticationManagement;
    }

    public function register(Request $request) {
        $this->validate($request, [
            "email" => "required|email|unique:users",
            "telephone" => "string|size:13",
            "name" => "required|string"
        ]);

        return response()->created(
            "User successfully created",
            $this->authenticationManagement->register($request->all()),
            "user"
        );
    }

    public function login(Request $request) {
        $this->validate($request, [
            "email" => "required|email",
            "password" => "required|string"
        ]);

        return response()->fetch(
            "User successfully signed in",
            $this->authenticationManagement->login($request->all()),
            "user"
        );
    }
}
