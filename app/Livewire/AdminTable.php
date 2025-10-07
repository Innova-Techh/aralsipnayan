<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class AdminTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $statusFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $totalAdmins = User::where('role', 'Admin')->count();
        $activeAdmins = User::where('role', 'Admin')->where('status', 'active')->count();
        $archivedAdmins = User::where('role', 'Admin')->where('status', 'archived')->count();
        $recentLogins = User::where('role', 'Admin')
            ->where('last_login_date', '>=', now()->subDay())
            ->count();

        $admins = User::query()
            ->where('role', 'Admin')
            ->with('adminProfile')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('username', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin-table', [
            'admins' => $admins,
            'totalAdmins' => $totalAdmins,
            'activeAdmins' => $activeAdmins,
            'archivedAdmins' => $archivedAdmins,
            'recentLogins' => $recentLogins,
        ]);
    }
}