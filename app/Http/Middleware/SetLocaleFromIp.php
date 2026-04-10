<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Models\Visitor;

class SetLocaleFromIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } else {
            $ip = $request->ip();
            if ($ip == '127.0.0.1' || $ip == '::1') {
                $ip = '103.23.45.67'; // Ex: Indonesian IP for testing
            }
            $position = Location::get($ip);
            
            if ($position) {
                $locale = ($position->countryCode === 'ID') ? 'id' : 'en';
                App::setLocale($locale);
                Session::put('locale', $locale);

                if (!Session::has('visited_today')) {
                    Visitor::create([
                        'ip_address' => $request->ip(),
                        'country' => $position->countryName,
                        'city' => $position->cityName,
                        'region' => $position->regionName,
                    ]);
                    Session::put('visited_today', true);
                }
            } else {
                App::setLocale('en'); 
            }
        }

        return $next($request);
    }
}
