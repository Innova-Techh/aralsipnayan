@extends('teacher.layouts.app')

@section('title', 'Student Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Student Management</h1>
            <p class="text-gray-600 mt-1">View and manage your students' progress</p>
        </div>
        <div class="flex space-x-3">
            <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option>All Sections</option>
                <option>Section A</option>
                <option>Section B</option>
                <option>Section C</option>
            </select>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                <span class="material-symbols-outlined mr-2">person_add</span>
                Add Student
            </button>
        </div>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Active</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Sample Student 1 -->
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <img class="h-10 w-10 rounded-full" src="{{ asset('images/profile/avatar1.png') }}" alt="">
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Maria Santos</div>
                                    <div class="text-sm text-gray-500">maria.santos@email.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Section A</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-green-600 h-2.5 rounded-full" style="width: 95%"></div>
                                </div>
                                <span class="ml-2 text-sm text-gray-600">95%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2 hours ago</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="text-blue-600 hover:text-blue-900">View Details</a>
                        </td>
                    </tr>
                    <!-- Sample Student 2 -->
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <img class="h-10 w-10 rounded-full" src="{{ asset('images/profile/avatar2.png') }}" alt="">
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">John Doe</div>
                                    <div class="text-sm text-gray-500">john.doe@email.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Section B</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-yellow-600 h-2.5 rounded-full" style="width: 72%"></div>
                                </div>
                                <span class="ml-2 text-sm text-gray-600">72%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1 day ago</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="text-blue-600 hover:text-blue-900">View Details</a>
                        </td>
                    </tr>
                    <!-- Sample Student 3 -->
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <img class="h-10 w-10 rounded-full" src="{{ asset('images/profile/avatar3.png') }}" alt="">
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Sarah Wilson</div>
                                    <div class="text-sm text-gray-500">sarah.wilson@email.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Section A</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-green-600 h-2.5 rounded-full" style="width: 89%"></div>
                                </div>
                                <span class="ml-2 text-sm text-gray-600">89%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">4 hours ago</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="text-blue-600 hover:text-blue-900">View Details</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
