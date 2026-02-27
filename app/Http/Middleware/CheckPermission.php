<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Separar permissões múltiplas (ex: "manage_books|view_books")
        $permissions = explode('|', $permission);

        foreach ($permissions as $perm) {
            if ($user->hasPermission($perm)) {
                return $next($request);
            }
        }

        abort(403, 'Não tem permissão para aceder a esta página.');
    }
}
