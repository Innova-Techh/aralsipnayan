@extends('admin.teacher.layouts.app')

@section('title', 'Assessment Management')

@section('content')
<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Assessment Management</h1>
            <p class="text-gray-600 mt-1">Create and manage assessments for your students</p>
        </div>
        <a href="{{ route('teacher.assessments.create') }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
            <span class="material-symbols-outlined mr-2">add</span>
            New Assessment
        </a>
    </div>

    <!-- Assessment Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Sample Assessment 1 -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Algebra Basics Quiz</h3>
                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Active</span>
            </div>
            <div class="space-y-2 text-sm text-gray-600 mb-4">
                <p><span class="font-medium">Category:</span> Number & Algebra</p>
                <p><span class="font-medium">Questions:</span> 15</p>
                <p><span class="font-medium">Assigned to:</span> Section A, B</p>
                <p><span class="font-medium">Responses:</span> 42/50</p>
            </div>
            <div class="flex space-x-2">
                <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">View Results</button>
                <button class="text-gray-600 hover:text-gray-800 text-sm font-medium">Edit</button>
            </div>
        </div>

        <!-- Sample Assessment 2 -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Geometry Assessment</h3>
                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Draft</span>
            </div>
            <div class="space-y-2 text-sm text-gray-600 mb-4">
                <p><span class="font-medium">Category:</span> Measurement & Geometry</p>
                <p><span class="font-medium">Questions:</span> 20</p>
                <p><span class="font-medium">Assigned to:</span> Not assigned</p>
                <p><span class="font-medium">Responses:</span> 0/0</p>
            </div>
            <div class="flex space-x-2">
                <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Assign</button>
                <button class="text-gray-600 hover:text-gray-800 text-sm font-medium">Edit</button>
            </div>
        </div>

        <!-- Sample Assessment 3 -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Statistics Quiz</h3>
                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Completed</span>
            </div>
            <div class="space-y-2 text-sm text-gray-600 mb-4">
                <p><span class="font-medium">Category:</span> Data & Probability</p>
                <p><span class="font-medium">Questions:</span> 12</p>
                <p><span class="font-medium">Assigned to:</span> Section C</p>
                <p><span class="font-medium">Responses:</span> 25/25</p>
            </div>
            <div class="flex space-x-2">
                <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">View Results</button>
                <button class="text-gray-600 hover:text-gray-800 text-sm font-medium">Archive</button>
            </div>
        </div>
    </div>
</div>
@endsection