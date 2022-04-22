<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function create() {
        return view('tokens.create');
    }

    public function store(Request $request) {
        ['name' => $name] = $request->validate(['name' => 'required|string']);

        $token = $request->user()->createToken($name);
 
        return view('tokens.show', compact('token'));
    }
}
