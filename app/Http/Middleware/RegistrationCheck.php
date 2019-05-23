<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Session;

class RegistrationCheck
{
    public function handle($request, Closure $next, $guard = 'admin')
    {
        $serrializeData = file_get_contents('config/softwareConfig.txt');
        $jsonEncodeData = unserialize($serrializeData);
        $regData        = json_decode($jsonEncodeData);
        if ($regData->status != 1) {
            return redirect()->route('adminInstallation');
        }

        return $next($request);
    }
}
