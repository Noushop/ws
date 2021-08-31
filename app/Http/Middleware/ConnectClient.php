<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ConnectClient
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
    $user = auth()->user();

    if (!$user) {
      return response()->json(['error' => 'Not Authorized token'], 498);
    }

    return $next($request);
  }
}
