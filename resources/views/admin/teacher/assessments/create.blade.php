@extends('admin.teacher.layouts.app')

@section('title', 'Aralsipnayan')

@section('content')
<div>
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('teacher.assessments') }}" class="text-gray-600 hover:text-gray-800">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create New Assessment</h1>
                    <p class="text-gray-600 mt-1">Build a new assessment for your students</p>
                </div>
            </div>
        </div>

        <!-- Assessment Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <form>
                <!-- Basic Information -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Assessment Title</label>
                            <input type="text" placeholder="Enter assessment title" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option>Select a category</option>
                                <option>Number & Algebra</option>
                                <option>Measurement & Geometry</option>
                                <option>Data & Probability</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea placeholder="Brief description of the assessment" rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Assessment Settings -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Assessment Settings</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Number of Questions</label>
                            <input type="number" value="15" min="5" max="50"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Time Limit (minutes)</label>
                            <input type="number" value="30" min="10" max="120"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty Level</label>
                            <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option>Easy</option>
                                <option>Medium</option>
                                <option>Hard</option>
                                <option>Mixed</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section Assignment -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Assign to Sections</h2>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-3 text-sm text-gray-700">Section A (25 students)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-3 text-sm text-gray-700">Section B (28 students)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-3 text-sm text-gray-700">Section C (22 students)</span>
                        </label>
                    </div>
                </div>

                <!-- Schedule -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Schedule</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Available From</label>
                            <input type="datetime-local" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Available Until</label>
                            <input type="datetime-local" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('teacher.assessments') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Save as Draft
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Create Assessment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection