<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {	   
	    $roles = explode('|', $roles);
	    $has_role = false;
	    
	    foreach($roles as $role) {
		    if(!$has_role) {
			    $has_role = $request->user()->hasRole($role);
			}
	    }
	    
        if (!$request->user() || !$has_role) {
            abort(403, 'Unauthorized'); // You can customize the response or redirect here
        }
        
        return $next($request);
    }
}
