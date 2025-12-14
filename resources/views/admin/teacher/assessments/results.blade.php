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
                <span class="text-gray-900">Results</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $assessment->title }} - Results</h1>
            <p class="text-gray-600 mt-1">View student performance and attempts</p>
        </div>
        <div>
            <a href="{{ route('teacher.assessments') }}" 
               class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                <span class="material-symbols-outlined mr-2">arrow_back</span>
                Back to List
            </a>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <h3 class="text-sm font-medium text-gray-500">Total Students</h3>
            <p class="text-2xl font-bold text-gray-900">{{ $students->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <h3 class="text-sm font-medium text-gray-500">Completed Attempts</h3>
            <p class="text-2xl font-bold text-gray-900">{{ $students->sum('completed_count') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            @php
                $avgScore = 0;
                $totalQuestions = 0;
                $count = $students->count();

                if ($count > 0) {
                    foreach($students as $student) {
                        if ($student->highest_score_raw !== '-') {
                            $parts = explode('/', $student->highest_score_raw);
                            if (count($parts) == 2) {
                                $avgScore += (float)$parts[0];
                                $totalQuestions = (float)$parts[1]; // Assuming total questions matches for all
                            }
                        }
                    }
                    $avgScore = $avgScore / $count;
                }
            @endphp
            <h3 class="text-sm font-medium text-gray-500">Average Score</h3>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($avgScore, 1) }}/{{ $totalQuestions }}</p>
        </div>
         <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <h3 class="text-sm font-medium text-gray-500">Top Score</h3>
             @php
                // Get the raw score of the student with the highest percentage
                $topStudent = $students->sortByDesc('highest_score')->first();
                $maxScoreRaw = $topStudent ? $topStudent->highest_score_raw : '-';
            @endphp
            <p class="text-2xl font-bold text-gray-900">{{ $maxScoreRaw }}</p>
        </div>
    </div>

    <!-- Student List Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        <div class="p-4 border-b border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="text-lg font-semibold text-gray-900">Student Attempts</h2>
            <div class="relative w-full md:w-64">
                <span class="material-symbols-outlined absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">search</span>
                <input type="text" id="searchInput" placeholder="Search student..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Attempt</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attempts</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Best Score</th>
                         <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="resultsTableBody">
                    @forelse($students as $student)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        @if($student->profile && $student->profile->profile_image)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $student->profile->profile_image) }}" alt="">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                                {{ $student->profile ? substr($student->profile->firstname, 0, 1) : 'S' }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            @if($student->profile)
                                                {{ $student->profile->firstname }} {{ $student->profile->lastname }}
                                            @else
                                                Unknown Student (ID: {{ $student->user_id }})
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $student->user->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($student->profile)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ $student->profile->section }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($student->latest_session)
                                    <div>{{ $student->latest_session->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs">{{ $student->latest_session->created_at->format('h:i A') }}</div>
                                @else
                                    -
                                @endif
                            </td>
                             <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="px-2 py-1 rounded bg-blue-50 text-blue-700 font-medium">
                                    {{ $student->attempts_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($student->completed_count > 0)
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $student->highest_score_raw }}
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <button onclick="openHistoryModal({{ $student->user_id }}, '{{ $student->profile->firstname ?? 'Student' }}')" 
                                        class="text-blue-600 hover:text-blue-900 hover:bg-blue-50 p-2 rounded-full transition-colors" title="View Attempt History">
                                    <span class="material-symbols-outlined">history</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No students have taken this assessment yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Attempt History Modal -->
<div id="historyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden" style="backdrop-filter: blur(2px);">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-xl rounded-xl bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-2">
                 <span class="material-symbols-outlined text-gray-500">history</span>
                 <h3 class="text-xl font-bold text-gray-900">Attempt History</h3>
            </div>
            <button onclick="closeHistoryModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="bg-yellow-50 rounded-lg p-6 mb-4">
             <div class="flex justify-between items-center mb-4">
                 <h4 class="text-lg font-semibold text-gray-800" id="modalStudentName">Student Name</h4>
             </div>
             
             <!-- Attempts Table -->
             <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                 <table class="min-w-full divide-y divide-gray-100">
                     <thead class="bg-gray-50">
                         <tr>
                             <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date Taken</th>
                             <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Score</th>
                             <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Percentage</th>
                             <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Time</th>
                             <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                         </tr>
                     </thead>
                     <tbody class="bg-white divide-y divide-gray-100" id="historyTableBody">
                         <!-- Content injected via JS -->
                         <tr>
                             <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                 <div class="flex justify-center items-center">
                                     <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500 mr-2"></div>
                                     Loading...
                                 </div>
                             </td>
                         </tr>
                     </tbody>
                 </table>
             </div>
        </div>
    </div>
</div>

<script>
    const assessmentId = {{ $assessment->id }};

    function openHistoryModal(studentId, studentName) {
        const modal = document.getElementById('historyModal');
        const modalName = document.getElementById('modalStudentName');
        const tableBody = document.getElementById('historyTableBody');
        
        modalName.textContent = studentName;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
        
        // Show loading state
        tableBody.innerHTML = `
            <tr>
                 <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                     <div class="flex justify-center items-center">
                         <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500 mr-2"></div>
                         Loading history...
                     </div>
                 </td>
            </tr>
        `;
        
        // Fetch data
        fetch(`/teacher/assessments/${assessmentId}/student/${studentId}/attempts`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            renderAttempts(data.attempts);
        })
        .catch(error => {
            console.error('Error:', error);
            tableBody.innerHTML = `<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Error loading data.</td></tr>`;
        });
    }

    function closeHistoryModal() {
        const modal = document.getElementById('historyModal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
    
    // Close modal on outside click
    document.getElementById('historyModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeHistoryModal();
        }
    });

    function renderAttempts(attempts) {
        const tableBody = document.getElementById('historyTableBody');
        
        if (attempts.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No attempts found.</td></tr>`;
            return;
        }
        
        tableBody.innerHTML = attempts.map(attempt => {
            // Determine badge color based on percentage
            const percentage = parseFloat(attempt.percentage);
            let badgeClass = 'bg-red-100 text-red-800';
            if (percentage >= 80) badgeClass = 'bg-green-100 text-green-800';
            else if (percentage >= 50) badgeClass = 'bg-yellow-100 text-yellow-800';
            
            return `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${attempt.date_taken.split('•')[0]}</div>
                        <div class="text-xs text-gray-500">${attempt.date_taken.split('•')[1]}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                        ${attempt.score}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${badgeClass}">
                            ${attempt.percentage}%
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${attempt.duration}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="${attempt.action_url}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-2 rounded-full inline-flex items-center transition-colors">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                        </a>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        const tableBody = document.getElementById('resultsTableBody');
        const rows = tableBody.getElementsByTagName('tr');

        for (let row of rows) {
            const studentName = row.cells[0]?.textContent?.toLowerCase() || '';
            const section = row.cells[1]?.textContent?.toLowerCase() || '';
            
            if (studentName.includes(searchText) || section.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
</script>
@endsection
