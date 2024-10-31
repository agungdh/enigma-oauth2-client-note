<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function(Request $request) {
    $request->session()->put('state', $state = Str::random(40));
    
    $query = http_build_query([
        'client_id' => '1',
        'redirect_uri' => 'http://192.168.0.112:84/auth/callback',
        'response_type' => 'code',
        'scope' => '',
        'state' => $state,
    ]);

    return redirect('http://192.168.0.112/oauth/authorize?'.$query);
});

Route::get('/auth/callback', function (Request $request) {
    $state = $request->session()->pull('state');
    
    throw_unless(
        strlen($state) > 0 && $state === $request->state,
        InvalidArgumentException::class,
        'Invalid state value.'
    );
 
    $response = Http::asForm()->post('http://192.168.0.112/oauth/token', [
        'grant_type' => 'authorization_code',
        'client_id' => '1',
        'client_secret' => 'e4mUrTAVzUXUzphkt7cuxgLz8nvnf63KPyvQB7Ey',
        'redirect_uri' => 'http://192.168.0.112:84/auth/callback',
        'code' => $request->code,
    ]);
 
    $request->session()->put($response->json());

    return redirect('/auth/user');
});

Route::get('/auth/user', function (Request $request) {
    $accessToken = $request->session()->get('access_token');

    $response = Http::withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer '.$accessToken,
    ])->get('http://192.168.0.112/api/user');
     
    return $response->json();
});