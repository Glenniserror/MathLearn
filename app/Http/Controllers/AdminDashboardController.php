<?php

namespace App\Http\Controllers;

use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $pendingTeachers = User::where('role', 'teacher')
            ->where('approval_status', 'pending')
            ->latest()
            ->get();

        return view('dashboard.admin_dashboard', compact('pendingTeachers'));
    }
}
