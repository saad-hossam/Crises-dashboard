<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImportBatch;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            [
                'label' => __('dashboard.total_users'),
                'value' => User::count(),
                'icon'  => 'bi-people',
                'color' => 'primary',
                'note'  => null,
            ],
            [
                'label' => __('dashboard.total_roles'),
                'value' => Role::count(),
                'icon'  => 'bi-shield-lock',
                'color' => 'info',
                'note'  => null,
            ],
            [
                'label' => __('dashboard.total_permissions'),
                'value' => Permission::count(),
                'icon'  => 'bi-key',
                'color' => 'warning',
                'note'  => null,
            ],
            
        ];

        return view('admin.dashboard.index', compact('stats'));
    }
}