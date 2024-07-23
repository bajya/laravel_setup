<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // if (Auth::user() &&  Auth::user()->is_admin == 'Yes') {

                $user = Auth::user();

                if ($user->role == 'Kitchen') {
                    return redirect()->route('kitchenDashboard');
                }
                else{
                    return $next($request);
                }
           
        // }
        // elseif(Auth::user()->role == 'Vendor'){
        //         return $next($request);
        // }
        return redirect('/');
    }

    // public function handle(Request $request, Closure $next)
    // {
    //     if (Auth::check()) {
    //         $user = Auth::user();
 
    //         if ($user->is_admin == 'Yes') {
    //             return redirect()->route('admins');
    //         }

    //         if ($user->role == 'Kitchen') {
                
    //             return redirect()->route('kitchenDashboard');
    //         }

    //         if ($user->role == 'Vendor') {
            
    //             return $next($request);
    //         }
    //     }

    //     return redirect('/');
    // }

}
