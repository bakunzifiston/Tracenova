<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserApproved
{
    /**
     * Allow access only if the user is approved or is a super admin.
     * Otherwise redirect to the pending-approval page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->canAccessApp()) {
            return $next($request);
        }

        return redirect()->route('pending-approval')->with('message', __('Your account is pending approval by an administrator.'));
    }
}
