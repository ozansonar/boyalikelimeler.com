<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userCount = User::count();
        $roleCount = Role::count();
        $users     = User::with('role')->get();

        return view('admin.dashboard', compact('userCount', 'roleCount', 'users'));
    }
}
