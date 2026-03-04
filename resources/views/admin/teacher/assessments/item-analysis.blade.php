@extends('admin.teacher.layouts.app')

@section('title', 'AralSipnayan')

@section('content')
<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <div class="flex items-center text-sm text-gray-500 mb-2">
                <a href="{{ route('teacher.assessments') }}" class="hover:text-blue-600 transition-colors">Quiz Management</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900">Item Analysis</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Item Analysis</h1>
            <p class="text-gray-600 mt-1">Detailed question-by-question performance analysis</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('teacher.assessments.item-analysis.export', $assessment->id) }}" 
               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center">
                <span class="material-symbols-outlined mr-2">download</span>
                Export to Excel
            </a>
            <a href="{{ route('teacher.assessments') }}" 
               class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                <span class="material-symbols-outlined mr-2">arrow_back</span>
                Back to List
            </a>
        </div>
    </div>

    <!-- Quiz Information -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Quiz Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500">Quiz Name</p>
                <p class="text-base font-semibold text-gray-900">{{ $assessment->title ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Section</p>
                <p class="text-base font-semibold text-gray-900">
                    @if(isset($sections) && count($sections) > 0)
                        {{ implode(', ', $sections) }}
                    @else
                        N/A
                    @endif
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Difficulty</p>
                <p class="text-base font-semibold text-gray-900">{{ $assessment->difficulty ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Learning Competency</p>
                <p class="text-base font-semibold text-gray-900">{{ $assessment->category ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Number of Examinees</p>
                <p class="text-base font-semibold text-gray-900">{{ $totalExaminees ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Item Analysis Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Question Analysis</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50">Item</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[300px]">Question</th>
                        @foreach($students ?? [] as $student)
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[120px]">
                                {{ $student['name'] }}
                            </th>
                        @endforeach
                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-blue-50">No. of Correct</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-blue-50">% Correct</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-blue-50">Remarks</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($itemAnalysis ?? [] as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-bold text-gray-900 sticky left-0 bg-white">
                                {{ $item['item_number'] }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900 max-w-md">
                                {{ Str::limit($item['question'] ?? 'N/A', 150) }}
                            </td>
                            @foreach($students ?? [] as $student)
                                <td class="px-3 py-4 text-center">
                                    @php
                                        $response = $item['student_responses'][$student['id']] ?? null;
                                    @endphp
                                    @if($response)
                                        @if($response['is_correct'])
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100">
                                                <span class="material-symbols-outlined text-green-600 text-lg">check</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100">
                                                <span class="material-symbols-outlined text-red-600 text-lg">close</span>
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-center bg-blue-50">
                                {{ $item['correct_responses'] ?? 0 }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center bg-blue-50">
                                @php
                                    $percentage = $item['percentage'] ?? 0;
                                    $badgeClass = 'bg-red-100 text-red-800';
                                    if ($percentage >= 90) {
                                        $badgeClass = 'bg-green-100 text-green-800';
                                    } elseif ($percentage >= 70) {
                                        $badgeClass = 'bg-blue-100 text-blue-800';
                                    } elseif ($percentage >= 50) {
                                        $badgeClass = 'bg-yellow-100 text-yellow-800';
                                    } elseif ($percentage >= 25) {
                                        $badgeClass = 'bg-orange-100 text-orange-800';
                                    }
                                @endphp
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">
                                    {{ number_format($percentage, 1) }}%
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center bg-blue-50">
                                @php
                                    $percentage = $item['percentage'] ?? 0;
                                    if ($percentage >= 90) {
                                        $remark = 'High Mastery';
                                        $remarkClass = 'text-green-600 bg-green-50';
                                    } elseif ($percentage >= 70) {
                                        $remark = 'Moderately Average Mastery';
                                        $remarkClass = 'text-blue-600 bg-blue-50';
                                    } elseif ($percentage >= 50) {
                                        $remark = 'Average Mastery';
                                        $remarkClass = 'text-yellow-600 bg-yellow-50';
                                    } elseif ($percentage >= 25) {
                                        $remark = 'Low Mastery';
                                        $remarkClass = 'text-orange-600 bg-orange-50';
                                    } else {
                                        $remark = 'No Mastery';
                                        $remarkClass = 'text-red-600 bg-red-50';
                                    }
                                @endphp
                                <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full {{ $remarkClass }}">
                                    {{ $remark }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 6 + count($students ?? []) }}" class="px-6 py-8 text-center text-gray-500">
                                <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">analytics</span>
                                <p class="text-sm">No data available for analysis</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
