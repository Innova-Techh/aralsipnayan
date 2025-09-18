@extends('admin.teacher.layouts.app')

@section('title', 'Aralsipnayan')

@section('content')
<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Section Management</h1>
            <p class="text-gray-600 mt-1">Organize and manage your class sections</p>
        </div>
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
            <span class="material-symbols-outlined mr-2">add</span>
            Create Section
        </button>
    </div>

    <!-- Sections Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Section A -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Section A</h3>
                    <p class="text-sm text-gray-500">Grade 7 Mathematics</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button class="text-gray-400 hover:text-gray-600">
                        <span class="material-symbols-outlined">edit</span>
                    </button>
                    <button class="text-gray-400 hover:text-red-600">
                        <span class="material-symbols-outlined">delete</span>
                    </button>
                </div>
            </div>
            
            <div class="space-y-3 mb-4">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Students:</span>
                    <span class="text-sm font-medium text-gray-900">25</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Active Assessments:</span>
                    <span class="text-sm font-medium text-gray-900">3</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Avg Performance:</span>
                    <span class="text-sm font-medium text-green-600">89%</span>
                </div>
            </div>

            <div class="flex space-x-2">
                <button class="flex-1 bg-blue-50 text-blue-600 py-2 px-3 rounded text-sm font-medium hover:bg-blue-100 transition-colors">
                    View Students
                </button>
                <button class="flex-1 bg-gray-50 text-gray-600 py-2 px-3 rounded text-sm font-medium hover:bg-gray-100 transition-colors">
                    Assign Quiz
                </button>
            </div>
        </div>

        <!-- Section B -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Section B</h3>
                    <p class="text-sm text-gray-500">Grade 7 Mathematics</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button class="text-gray-400 hover:text-gray-600">
                        <span class="material-symbols-outlined">edit</span>
                    </button>
                    <button class="text-gray-400 hover:text-red-600">
                        <span class="material-symbols-outlined">delete</span>
                    </button>
                </div>
            </div>
            
            <div class="space-y-3 mb-4">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Students:</span>
                    <span class="text-sm font-medium text-gray-900">28</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Active Assessments:</span>
                    <span class="text-sm font-medium text-gray-900">2</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Avg Performance:</span>
                    <span class="text-sm font-medium text-yellow-600">76%</span>
                </div>
            </div>

            <div class="flex space-x-2">
                <button class="flex-1 bg-blue-50 text-blue-600 py-2 px-3 rounded text-sm font-medium hover:bg-blue-100 transition-colors">
                    View Students
                </button>
                <button class="flex-1 bg-gray-50 text-gray-600 py-2 px-3 rounded text-sm font-medium hover:bg-gray-100 transition-colors">
                    Assign Quiz
                </button>
            </div>
        </div>

        <!-- Section C -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Section C</h3>
                    <p class="text-sm text-gray-500">Grade 7 Mathematics</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button class="text-gray-400 hover:text-gray-600">
                        <span class="material-symbols-outlined">edit</span>
                    </button>
                    <button class="text-gray-400 hover:text-red-600">
                        <span class="material-symbols-outlined">delete</span>
                    </button>
                </div>
            </div>
            
            <div class="space-y-3 mb-4">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Students:</span>
                    <span class="text-sm font-medium text-gray-900">22</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Active Assessments:</span>
                    <span class="text-sm font-medium text-gray-900">1</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Avg Performance:</span>
                    <span class="text-sm font-medium text-green-600">92%</span>
                </div>
            </div>

            <div class="flex space-x-2">
                <button class="flex-1 bg-blue-50 text-blue-600 py-2 px-3 rounded text-sm font-medium hover:bg-blue-100 transition-colors">
                    View Students
                </button>
                <button class="flex-1 bg-gray-50 text-gray-600 py-2 px-3 rounded text-sm font-medium hover:bg-gray-100 transition-colors">
                    Assign Quiz
                </button>
            </div>
        </div>
    </div>

    <!-- Section Details Modal/Cards could go here -->
</div>
@endsection