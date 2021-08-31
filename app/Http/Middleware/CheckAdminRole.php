<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CheckAdminRole
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    $user = auth()->user()->role;

    if ($user !== 'admin' && $user !== 'super-admin') {
      return response()->json(['error' => 'User not authorized for this action'], 401);
    }

    return $next($request);
  }
}
