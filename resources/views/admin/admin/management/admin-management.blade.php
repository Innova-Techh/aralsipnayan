@extends('admin.admin.layouts.app')

@section('title', 'Admin Management')

@section('content')
    <div class="p-6 space-y-8">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Admin Management</h1>
                <p class="text-gray-600 text-sm">Manage system administrators</p>
            </div>

            <div class="flex space-x-3 mt-4 sm:mt-0">
                <button
                    class="flex items-center gap-2 px-4 py-2 bg-blue-900 text-white rounded-lg text-sm font-medium hover:bg-blue-800 transition">
                    <i class="fas fa-plus"></i> Add Admin
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Admins -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h4 class="text-sm font-medium text-gray-600">Total Admins</h4>
                <p class="text-2xl font-semibold text-gray-900 mt-1">8</p>
                <p class="text-xs text-gray-500 mt-1">+1 from last month</p>
            </div>

            <!-- Active Admins -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h4 class="text-sm font-medium text-gray-600">Active Admins</h4>
                <p class="text-2xl font-semibold text-gray-900 mt-1">7</p>
                <p class="text-xs text-gray-500 mt-1">87.5% of total</p>
            </div>

            <!-- Archived Admins -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h4 class="text-sm font-medium text-gray-600">Archived Admins</h4>
                <p class="text-2xl font-semibold text-gray-900 mt-1">1</p>
                <p class="text-xs text-gray-500 mt-1">12.5% of total</p>
            </div>

            <!-- Recent Logins -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h4 class="text-sm font-medium text-gray-600">Recent Logins</h4>
                <p class="text-2xl font-semibold text-gray-900 mt-1">5</p>
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
                        <input type="text" placeholder="Search admins..."
                            class="pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-64">
                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                    </div>
                    <select
                        class="text-sm border border-gray-300 rounded-lg py-2 px-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option>All Status</option>
                        <option>Active</option>
                        <option>Archived</option>
                    </select>
                    <button class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-gray-600">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-gray-700">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left py-3 px-6 font-semibold">Admin</th>
                            <th class="text-left py-3 px-6 font-semibold">Contact</th>
                            <th class="text-left py-3 px-6 font-semibold">Username</th>
                            <th class="text-left py-3 px-6 font-semibold">Status</th>
                            <th class="text-left py-3 px-6 font-semibold">Join Date</th>
                            <th class="text-left py-3 px-6 font-semibold">Last Activity</th>
                            <th class="py-3 px-6"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-6 flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 text-xs font-bold">
                                    JD</div>
                                <div>
                                    <p class="font-medium text-gray-900">John Doe</p>
                                    <p class="text-xs text-gray-500">ID: 1</p>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <p>john.doe@aralsip.com</p>
                                <p class="text-xs text-gray-500">+63 912 345 6789</p>
                            </td>
                            <td class="py-4 px-6">johndoe</td>
                            <td class="py-4 px-6">
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                            </td>
                            <td class="py-4 px-6">2023-09-01</td>
                            <td class="py-4 px-6">2024-01-15</td>
                            <td class="py-4 px-6 text-right">
                                <button class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                            </td>
                        </tr>

                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-6 flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 text-xs font-bold">
                                    JS</div>
                                <div>
                                    <p class="font-medium text-gray-900">Jane Smith</p>
                                    <p class="text-xs text-gray-500">ID: 2</p>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <p>jane.smith@aralsip.com</p>
                                <p class="text-xs text-gray-500">+63 923 456 7890</p>
                            </td>
                            <td class="py-4 px-6">janesmith</td>
                            <td class="py-4 px-6">
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                            </td>
                            <td class="py-4 px-6">2023-10-15</td>
                            <td class="py-4 px-6">2024-01-14</td>
                            <td class="py-4 px-6 text-right">
                                <button class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                            </td>
                        </tr>

                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-6 flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 text-xs font-bold">
                                    MJ</div>
                                <div>
                                    <p class="font-medium text-gray-900">Mike Johnson</p>
                                    <p class="text-xs text-gray-500">ID: 3</p>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <p>mike.johnson@aralsip.com</p>
                                <p class="text-xs text-gray-500">+63 934 567 8901</p>
                            </td>
                            <td class="py-4 px-6">mikejohnson</td>
                            <td class="py-4 px-6">
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Archived</span>
                            </td>
                            <td class="py-4 px-6">2023-08-20</td>
                            <td class="py-4 px-6">2024-01-10</td>
                            <td class="py-4 px-6 text-right">
                                <button class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection