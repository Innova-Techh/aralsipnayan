@extends('admin.teacher.layouts.app')

@section('title', 'Aralsipnayan')

@section('content')
<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Assessment</h1>
            <p class="text-gray-600 mt-1">Update assessment details</p>
        </div>
        <a href="{{ route('teacher.assessments') }}" 
           class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors flex items-center">
            <span class="material-symbols-outlined mr-2">arrow_back</span>
            Back to Assessments
        </a>
    </div>

    <!-- Edit Assessment Form -->
    <div class="bg-white rounded-lg shadow p-6">
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <span class="material-symbols-outlined text-red-400 mr-2">error</span>
                    <div>
                        <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('teacher.assessments.update', $assessment) }}">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assessment Title</label>
                        <input type="text" name="title" placeholder="Enter assessment title" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror"
                               value="{{ old('title', $assessment->title) }}" required>
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('category') border-red-500 @enderror" required>
                            <option value="">Select a category</option>
                            <option value="Number & Algebra" {{ old('category', $assessment->category) == 'Number & Algebra' ? 'selected' : '' }}>Number & Algebra</option>
                            <option value="Measurement & Geometry" {{ old('category', $assessment->category) == 'Measurement & Geometry' ? 'selected' : '' }}>Measurement & Geometry</option>
                            <option value="Data & Probability" {{ old('category', $assessment->category) == 'Data & Probability' ? 'selected' : '' }}>Data & Probability</option>
                        </select>
                        @error('category')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" placeholder="Brief description of the assessment" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $assessment->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Assessment Settings -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Assessment Settings</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Number of Questions</label>
                        <input type="number" name="number_of_questions" value="{{ old('number_of_questions', $assessment->number_of_questions) }}" min="5" max="50"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('number_of_questions') border-red-500 @enderror" required>
                        @error('number_of_questions')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Time Limit (minutes)</label>
                        <input type="number" name="time_limit" value="{{ old('time_limit', $assessment->time_limit) }}" min="10" max="120"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('time_limit') border-red-500 @enderror" required>
                        @error('time_limit')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty Level</label>
                        <select name="difficulty" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('difficulty') border-red-500 @enderror" required>
                            <option value="Easy" {{ old('difficulty', $assessment->difficulty) == 'Easy' ? 'selected' : '' }}>Easy</option>
                            <option value="Medium" {{ old('difficulty', $assessment->difficulty) == 'Medium' ? 'selected' : '' }}>Medium</option>
                            <option value="Hard" {{ old('difficulty', $assessment->difficulty) == 'Hard' ? 'selected' : '' }}>Hard</option>
                            <option value="Mixed" {{ old('difficulty', $assessment->difficulty) == 'Mixed' ? 'selected' : '' }}>Mixed</option>
                        </select>
                        @error('difficulty')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Live Quiz Option -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Quiz Type</h2>
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_live_quiz" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" {{ old('is_live_quiz', $assessment->is_live_quiz) ? 'checked' : '' }}>
                        <span class="ml-3 text-sm text-gray-700">Live Quiz (Still in Development)</span>
                    </label>
                </div>
            </div>

            <!-- Schedule -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Schedule</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Available From</label>
                        <input type="datetime-local" name="available_from" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('available_from') border-red-500 @enderror"
                               value="{{ old('available_from', $assessment->available_from ? $assessment->available_from->format('Y-m-d\TH:i') : '') }}">
                        @error('available_from')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Available Until</label>
                        <input type="datetime-local" name="available_until" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('available_until') border-red-500 @enderror"
                               value="{{ old('available_until', $assessment->available_until ? $assessment->available_until->format('Y-m-d\TH:i') : '') }}">
                        @error('available_until')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
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
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Update Assessment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection