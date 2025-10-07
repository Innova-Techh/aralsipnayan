<div class="space-y-8">
    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Admins -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h4 class="text-sm font-medium text-gray-600">Total Admins</h4>
            <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $totalAdmins }}</p>
            <p class="text-xs text-gray-500 mt-1">All registered admins</p>
        </div>

        <!-- Active Admins -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h4 class="text-sm font-medium text-gray-600">Active Admins</h4>
            <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $activeAdmins }}</p>
            <p class="text-xs text-gray-500 mt-1">
                {{ $totalAdmins > 0 ? round(($activeAdmins / $totalAdmins) * 100, 1) : 0 }}% of total
            </p>
        </div>

        <!-- Archived Admins -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h4 class="text-sm font-medium text-gray-600">Archived Admins</h4>
            <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $archivedAdmins }}</p>
            <p class="text-xs text-gray-500 mt-1">
                {{ $totalAdmins > 0 ? round(($archivedAdmins / $totalAdmins) * 100, 1) : 0 }}% of total
            </p>
        </div>

        <!-- Recent Logins -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h4 class="text-sm font-medium text-gray-600">Recent Logins</h4>
            <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $recentLogins }}</p>
            <p class="text-xs text-gray-500 mt-1">Last 24 hours</p>
        </div>
    </div>

    <!-- Admin Directory -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-4 border-b border-gray-100">
            <div>
                <h2 class="font-semibold text-gray-900 text-lg">Admin Directory</h2>
                <p class="text-sm text-gray-500">Complete admin roster with profile information</p>
            </div>

            <!-- Search and Filter -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 mt-4 sm:mt-0">
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search admins..."
                        class="pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-64">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                </div>
                <select wire:model.live="statusFilter"
                    class="text-sm border border-gray-300 rounded-lg py-2 px-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="archived">Archived</option>
                </select>
                <select wire:model.live="perPage"
                    class="text-sm border border-gray-300 rounded-lg py-2 px-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="10">10 per page</option>
                    <option value="25">25 per page</option>
                    <option value="50">50 per page</option>
                </select>
            </div>
        </div>

        <div>
            <div class="mb-4">
                <input type="text" wire:model.debounce.300ms="search" placeholder="Search admins..."
                    class="border rounded px-4 py-2 w-full">
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($admins as $admin)
                            <tr>
                                <td class="px-6 py-4">{{ $admin->id }}</td>
                                <td class="px-6 py-4">{{ $admin->username }}</td>
                                <td class="px-6 py-4">{{ $admin->email }}</td>
                                <td class="px-6 py-4">
                                    @if($admin->adminProfile)
                                        {{ $admin->adminProfile->firstname }} {{ $admin->adminProfile->lastname }}
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 text-xs rounded {{ $admin->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($admin->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No admins found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="px-4 py-3 border-t">
                    {{ $admins->links() }}
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($admins->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $admins->links() }}
            </div>
        @endif
    </div>
</div>