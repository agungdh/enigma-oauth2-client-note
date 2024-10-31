<?php 

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Middleware\LoggedIn;
use Illuminate\Support\Facades\Http;

Route::get('/logout', function(Request $request) {
    $request->session()->invalidate();

    return redirect(config('app.oauth_url') . '/logout?redirect_url='.config('app.url'));
});

Route::get('/login', function(Request $request) {
    $request->session()->put('state', $state = Str::random(40));
    
    $query = http_build_query([
        'client_id' => config('app.oauth_client_id'),
        'redirect_uri' => config('app.url') . '/auth/callback',
        'response_type' => 'code',
        'scope' => '',
        'state' => $state,
    ]);

    return redirect(config('app.oauth_url') . '/oauth/authorize?'.$query);
});

Route::get('/auth/callback', function (Request $request) {
    $state = $request->session()->pull('state');
    
    throw_unless(
        strlen($state) > 0 && $state === $request->state,
        InvalidArgumentException::class,
        'Invalid state value.'
    );
 
    $response = Http::asForm()->post(config('app.oauth_url') . '/oauth/token', [
        'grant_type' => 'authorization_code',
        'client_id' => config('app.oauth_client_id'),
        'client_secret' => config('app.oauth_client_secret'),
        'redirect_uri' => config('app.url') . '/auth/callback',
        'code' => $request->code,
    ]);
 
    $request->session()->put($response->json());

    return redirect('/');
});