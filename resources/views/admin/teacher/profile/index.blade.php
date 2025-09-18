@extends('admin.teacher.layouts.app')

@section('title', 'Aralsipnayan')

@section('content')
<div>
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Profile Settings</h1>
            <p class="text-gray-600 mt-1">Manage your teacher account settings and preferences</p>
        </div>

        <!-- Profile Information -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Profile Information</h2>
            <div class="flex items-center space-x-6 mb-6">
                <img class="h-20 w-20 rounded-full" src="{{ asset('images/profile/avatar1.png') }}" alt="Profile Picture">
                <div>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                        Change Photo
                    </button>
                    <p class="text-xs text-gray-500 mt-1">JPG, GIF or PNG. 1MB max.</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" value="{{ Auth::user()->username ?? 'Teacher Name' }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" value="{{ Auth::user()->email ?? 'teacher@school.edu' }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                    <input type="text" value="Mathematics" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Grade Level</label>
                    <input type="text" value="Grade 7" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Save Changes
                </button>
            </div>
        </div>

        <!-- Teaching Statistics -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Teaching Statistics</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">247</div>
                    <div class="text-sm text-gray-600">Total Students</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">156</div>
                    <div class="text-sm text-gray-600">Assessments Created</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600">3</div>
                    <div class="text-sm text-gray-600">Active Sections</div>
                </div>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Security Settings</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <input type="password" placeholder="Enter current password" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" placeholder="Enter new password" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input type="password" placeholder="Confirm new password" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Update Password
                </button>
            </div>
        </div>
    </div>
</div>
@endsection