<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;

class LoggedIn
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('access_token')) {
            return redirect('/');
        }

        $response = $this->getUser($request);
        
        if ($response->failed()) {
            if($response->unauthorized()) {
                $responseRefresh = Http::asForm()->post(config('app.oauth_url') . '/oauth/token', [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => session('refresh_token'),
                    'client_id' => config('app.oauth_client_id'),
                    'client_secret' => config('app.oauth_client_secret'),
                    'scope' => '',
                ]);

                if ($responseRefresh->successful()) {
                    $request->session()->put($responseRefresh->json());

                    $response = $this->getUser($request);
                } else {
                    $request->session()->invalidate();

                    return redirect('/');
                }
            } else {
                $response->throw();
            }
        }

        $request->session()->put('user', $response->json());

        return $next($request);
    }

    private function getUser(Request $request) {
        $accessToken = $request->session()->get('access_token');

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$accessToken,
        ])->get(config('app.oauth_url') . '/api/user');

        return $response;
    }
}
