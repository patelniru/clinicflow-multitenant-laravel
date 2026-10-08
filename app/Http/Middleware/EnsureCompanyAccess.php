<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class EnsureCompanyAccess {
    public function handle(Request $request, Closure $next): Response {
        $user=$request->user();
        if (!$user || $user->isSuperAdmin()) return $next($request);
        foreach (['patient','appointment','consultant'] as $key) {
            $model=$request->route($key);
            if ($model) abort_unless((int)$model->company_id === (int)$user->company_id,403);
        }
        return $next($request);
    }
}
