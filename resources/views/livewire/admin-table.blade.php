<div class="space-y-6">
    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
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
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-xl font-semibold text-gray-900">Admin Directory</h2>
            <p class="text-sm text-gray-500 mt-1">Complete admin roster with profile information</p>
        </div>

        <!-- Search and Filters -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
            <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                <!-- Search Input -->
                <div class="relative flex-1 w-full">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search admins..."
                        class="pl-10 pr-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full">
                </div>

                <!-- Status Filter -->
                <select wire:model.live="statusFilter"
                    class="text-sm border border-gray-300 rounded-lg py-2.5 px-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="archived">Archived</option>
                </select>

                <!-- Filter Icon Button -->
                <button class="px-3 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-filter text-gray-600"></i>
                </button>

                <!-- Sort Icon Button -->
                <button class="px-3 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-sort text-gray-600"></i>
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Admin
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Username
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Join Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Last Activity
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($admins as $admin)
                        <tr class="hover:bg-gray-50 transition">
                            <!-- Admin Column -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        @if($admin->adminProfile && $admin->adminProfile->profile_url)
                                            <img class="h-10 w-10 rounded-full object-cover"
                                                src="{{ asset($admin->adminProfile->profile_url) }}"
                                                alt="{{ $admin->username }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-gray-500 font-medium text-sm">
                                                    {{ strtoupper(substr($admin->username ?? 'A', 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            @if($admin->adminProfile)
                                                {{ $admin->adminProfile->firstname }} {{ $admin->adminProfile->lastname }}
                                            @else
                                                {{ $admin->username }}
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            ID: {{ $admin->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>


                            <!-- Username Column -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $admin->username }}</div>
                            </td>

                            <!-- Status Column -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($admin->status === 'active')
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Archived
                                    </span>
                                @endif
                            </td>

                            <!-- Join Date Column -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $admin->created_at->format('Y-m-d') }}
                            </td>

                            <!-- Last Activity Column -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($admin->last_login_date)
                                    {{ $admin->last_login_date->format('Y-m-d') }}
                                @else
                                    Never
                                @endif
                            </td>

                            <!-- Actions Column -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <button class="text-gray-400 hover:text-gray-600 transition" title="More options">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <i class="fas fa-users text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-lg font-medium text-gray-600">No admins found</p>
                                    <p class="text-sm text-gray-400">Try adjusting your search criteria</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($admins->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $admins->links() }}
            </div>
        @endif
    </div>


</div>