@extends('admin.teacher.layouts.app')

@section('title', 'Aralsipnayan')

@section('content')
<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Analytics Dashboard</h1>
        <p class="text-gray-600 mt-1">Track student performance and assessment insights</p>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <span class="material-symbols-outlined text-blue-600">trending_up</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Average Accuracy</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['avg_accuracy'] ?? 0 }}%</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <span class="material-symbols-outlined text-green-600">assignment_turned_in</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Completion Rate</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['completion_rate'] ?? 0 }}%</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <span class="material-symbols-outlined text-yellow-600">assessment</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Assessments</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_assessments'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <span class="material-symbols-outlined text-purple-600">people</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Students</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_students'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Performance Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Trends</h3>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <p class="text-gray-500">Chart placeholder</p>
            </div>
        </div>

        <!-- Category Performance -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance by Category</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Number & Algebra</span>
                        <span class="text-gray-900 font-medium">87%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: 87%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Measurement & Geometry</span>
                        <span class="text-gray-900 font-medium">82%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: 82%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Data & Probability</span>
                        <span class="text-gray-900 font-medium">79%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-yellow-600 h-2 rounded-full" style="width: 79%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Assessments -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Assessment Results</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assessment Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Students Attempted</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Of Questions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Latest Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentAssessments as $assessment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <div>
                                <div class="font-semibold">{{ $assessment->assessment_name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <div>
                                <div class="italic">Sampaguita</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $assessment->total_completed }} attempts
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ number_format($assessment->total_questions_answered) }} questions
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $assessment->formatted_date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('teacher.analytics.assessment.details', $assessment->assessment_id) }}" 
                               class="text-indigo-600 hover:text-indigo-900 inline-flex items-center">
                                <span class="material-symbols-outlined text-sm mr-1">quiz</span>
                                View Questions
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">assessment</span>
                                <p>No completed assessments found</p>
                                <p class="text-sm">Assessment results will appear here once students complete their tests</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection