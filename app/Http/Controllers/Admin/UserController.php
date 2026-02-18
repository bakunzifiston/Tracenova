<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * List all users for approval management (super admin only).
     */
    public function index(): View
    {
        $users = User::withCount('apps')
            ->orderByRaw('is_approved = 0 DESC')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Approve a user so they can access the app and manage their projects.
     */
    public function approve(User $user): RedirectResponse
    {
        $user->update(['is_approved' => true]);

        return redirect()->route('admin.users.index')
            ->with('success', __('User :name has been approved.', ['name' => $user->name]));
    }

    /**
     * Revoke approval (user will see pending-approval again when they log in).
     */
    public function reject(User $user): RedirectResponse
    {
        $user->update(['is_approved' => false]);

        return redirect()->route('admin.users.index')
            ->with('success', __('Approval revoked for :name.', ['name' => $user->name]));
    }
}
