@extends('admin.admin.layouts.app')

@section('title', 'AralSipnayan')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-8">

        <!-- Success Message -->
        @if(session('success'))
            <div
                class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-green-600 hover:text-green-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <!-- Bloom's Taxonomy Legend -->
        <div class="bg-white rounded-lg shadow-sm mb-6 p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-brain text-blue-500"></i>
                Bloom's Taxonomy Levels
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-3">
                <div class="flex items-center gap-2 p-3 rounded-lg bg-red-50 border border-red-200">
                    <div class="w-3 h-3 rounded-full bg-red-500"></div>
                    <span class="text-sm font-medium text-red-800">Remember</span>
                </div>
                <div class="flex items-center gap-2 p-3 rounded-lg bg-orange-50 border border-orange-200">
                    <div class="w-3 h-3 rounded-full bg-orange-500"></div>
                    <span class="text-sm font-medium text-orange-800">Understand</span>
                </div>
                <div class="flex items-center gap-2 p-3 rounded-lg bg-yellow-50 border border-yellow-200">
                    <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                    <span class="text-sm font-medium text-yellow-800">Apply</span>
                </div>
                <div class="flex items-center gap-2 p-3 rounded-lg bg-green-50 border border-green-200">
                    <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    <span class="text-sm font-medium text-green-800">Analyze</span>
                </div>
                <div class="flex items-center gap-2 p-3 rounded-lg bg-blue-50 border border-blue-200">
                    <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                    <span class="text-sm font-medium text-blue-800">Evaluate</span>
                </div>
                <div class="flex items-center gap-2 p-3 rounded-lg bg-purple-50 border border-purple-200">
                    <div class="w-3 h-3 rounded-full bg-purple-500"></div>
                    <span class="text-sm font-medium text-purple-800">Create</span>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <form id="filterForm" method="GET" action="{{ route('admin.management.questions') }}" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Categories</option>
                            <option value="number_algebra" {{ request('category') == 'number_algebra' ? 'selected' : '' }}>
                                Number & Algebra</option>
                            <option value="measurement_geometry" {{ request('category') == 'measurement_geometry' ? 'selected' : '' }}>Measurement & Geometry</option>
                            <option value="data_probability" {{ request('category') == 'data_probability' ? 'selected' : '' }}>Data & Probability</option>
                        </select>
                    </div>

                    <!-- Difficulty Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty</label>
                        <select name="difficulty"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Difficulties</option>
                            <option value="beginner" {{ request('difficulty') == 'beginner' ? 'selected' : '' }}>Beginner
                            </option>
                            <option value="intermediate" {{ request('difficulty') == 'intermediate' ? 'selected' : '' }}>
                                Intermediate</option>
                            <option value="advanced" {{ request('difficulty') == 'advanced' ? 'selected' : '' }}>Advanced
                            </option>
                        </select>
                    </div>

                    <!-- Type Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <select name="type"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Types</option>
                            <option value="multiple_choice" {{ request('type') == 'multiple_choice' ? 'selected' : '' }}>
                                Multiple Choice</option>
                            <option value="fill_blanks" {{ request('type') == 'fill_blanks' ? 'selected' : '' }}>Fill in the
                                Blanks</option>
                            <option value="true_false" {{ request('type') == 'true_false' ? 'selected' : '' }}>True/False
                            </option>
                        </select>
                    </div>

                    <!-- Bloom's Taxonomy Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bloom's Taxonomy</label>
                        <select name="blooms_level"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All Levels</option>
                            <option value="remember" {{ request('blooms_level') == 'remember' ? 'selected' : '' }}>Remember</option>
                            <option value="understand" {{ request('blooms_level') == 'understand' ? 'selected' : '' }}>Understand</option>
                            <option value="apply" {{ request('blooms_level') == 'apply' ? 'selected' : '' }}>Apply</option>
                            <option value="analyze" {{ request('blooms_level') == 'analyze' ? 'selected' : '' }}>Analyze</option>
                            <option value="evaluate" {{ request('blooms_level') == 'evaluate' ? 'selected' : '' }}>Evaluate</option>
                            <option value="create" {{ request('blooms_level') == 'create' ? 'selected' : '' }}>Create</option>
                        </select>
                    </div>

                    <!-- School Year Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">School Year</label>
                        <select name="school_year"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All School Years</option>
                            <option value="2023-2024" {{ request('school_year') == '2023-2024' ? 'selected' : '' }}>2023-2024
                            </option>
                            <option value="2024-2025" {{ request('school_year') == '2024-2025' ? 'selected' : '' }}>2024-2025
                            </option>
                            <option value="2025-2026" {{ request('school_year') == '2025-2026' ? 'selected' : '' }}>2025-2026
                            </option>
                        </select>
                    </div>

                    <!-- Apply Button -->
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2.5 px-4 rounded-lg flex items-center justify-center gap-2 transition-colors">
                            <i class="fas fa-filter"></i>
                            Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table Container -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">

            <!-- Table Header -->
            <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Questions Management</h2>
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <button type="button" id="exportDropdownButton"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2.5 px-5 rounded-lg flex items-center gap-2 transition-colors">
                            <i class="fas fa-file-export"></i>
                            Export
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div id="exportDropdownMenu"
                            class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-20">
                            <a href="{{ route('admin.management.questions.export.csv', request()->query()) }}"
                                class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                                Export as CSV
                            </a>
                            <a href="{{ route('admin.management.questions.export.excel', request()->query()) }}"
                                class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                                Export as Excel
                            </a>
                            <a href="{{ route('admin.management.questions.export.tiff', request()->query()) }}"
                                class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                                Export as TIFF
                            </a>
                        </div>
                    </div>
                    <div class="relative">
                        <button type="button" id="bulkDropdownButton"
                            class="bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-medium py-2.5 px-5 rounded-lg flex items-center gap-2 transition-colors">
                            <i class="fas fa-file-import"></i>
                            Add Bulk Question
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div id="bulkDropdownMenu"
                            class="hidden absolute right-0 mt-2 w-52 bg-white border border-gray-200 rounded-lg shadow-lg z-20">
                            <a href="{{ route('admin.management.questions.bulk.show', ['type' => 'csv']) }}"
                                class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                                Bulk via CSV
                            </a>
                            <a href="{{ route('admin.management.questions.bulk.show', ['type' => 'excel']) }}"
                                class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                                Bulk via Excel
                            </a>
                            <a href="{{ route('admin.management.questions.bulk.show', ['type' => 'tiff']) }}"
                                class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                                Bulk via TIFF
                            </a>
                        </div>
                    </div>
                    <button onclick="openModal('add')"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2.5 px-5 rounded-lg flex items-center gap-2 transition-colors">
                        <i class="fas fa-plus"></i>
                        Add Question
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Question ID</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Category</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Difficulty</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Bloom's Level</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                School Year</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Question Text</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Topic Tag</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($questions as $question)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-semibold text-gray-900">{{ $question['question_id'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ ucwords(str_replace('_', ' & ', $question['competency'])) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($question['difficulty_level']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $bloomsColors = [
                                            'remember' => 'bg-red-100 text-red-800 border-red-200',
                                            'understand' => 'bg-orange-100 text-orange-800 border-orange-200',
                                            'apply' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                            'analyze' => 'bg-green-100 text-green-800 border-green-200',
                                            'evaluate' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'create' => 'bg-purple-100 text-purple-800 border-purple-200'
                                        ];
                                        $bloomsLevel = $question['blooms_level'] ?? ($question['blooms_taxonomy'] ?? 'remember');
                                        $bloomsClass = $bloomsColors[$bloomsLevel] ?? $bloomsColors['remember'];
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ $bloomsClass }}">
                                        {{ ucfirst($bloomsLevel) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 border border-indigo-200">
                                        {{ $question['school_year'] ?? '2025-2026' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 max-w-md">
                                    {{ Str::limit($question['question_text'], 80) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $question['topic_tag'] ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <button onclick='viewQuestion(@json($question))'
                                            class="w-8 h-8 flex items-center justify-center rounded bg-teal-50 text-teal-700 hover:bg-teal-100 transition-colors"
                                            title="View">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>
                                        <button onclick='editQuestion(@json($question))'
                                            class="w-8 h-8 flex items-center justify-center rounded bg-amber-50 text-amber-700 hover:bg-amber-100 transition-colors"
                                            title="Edit">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        @if($question['question_source'] === 'custom')
                                            <form method="POST"
                                                action="{{ route('admin.management.questions.destroy', $question['question_id']) }}"
                                                class="inline"
                                                onsubmit="return confirm('Are you sure you want to delete this question?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="w-8 h-8 flex items-center justify-center rounded bg-red-50 text-red-700 hover:bg-red-100 transition-colors"
                                                    title="Delete">
                                                    <i class="fas fa-trash text-sm"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs italic text-gray-400 ml-2">Built-in</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <i class="fas fa-inbox text-5xl mb-4"></i>
                                        <p class="text-lg font-medium">No questions found</p>
                                        <p class="text-sm mt-1">Try adjusting your filters or add a new question</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Modern Pagination -->
@if($questions->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
            <!-- Results Info -->
            <div class="text-sm text-gray-600">
                @if($questions->total() > 0)
                    Showing <span class="font-semibold text-gray-800">{{ $questions->firstItem() }}</span> to 
                    <span class="font-semibold text-gray-800">{{ $questions->lastItem() }}</span> of 
                    <span class="font-semibold text-gray-800">{{ $questions->total() }}</span> results
                @else
                    <span class="text-gray-500">No results found</span>
                @endif
            </div>

            <!-- Pagination Controls -->
            <div class="flex items-center space-x-1">
                <!-- Previous Page Link -->
                @if($questions->onFirstPage())
                    <span class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-gray-50 rounded-lg border border-gray-200 cursor-not-allowed">
                        <i class="fas fa-chevron-left mr-2 text-xs"></i>
                        Previous
                    </span>
                @else
                    <a href="{{ $questions->previousPageUrl() }}" 
                       class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white rounded-lg border border-gray-300 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 ease-in-out group">
                        <i class="fas fa-chevron-left mr-2 text-xs group-hover:-translate-x-0.5 transition-transform"></i>
                        Previous
                    </a>
                @endif

                <!-- Page Numbers - Show maximum 4 pages including last page -->
                <div class="flex items-center space-x-1">
                    @php
                        $current = $questions->currentPage();
                        $last = $questions->lastPage();
                        
                        // Always show first page, current page, and last page
                        $pages = [1];
                        
                        // Add current page if not already included
                        if ($current != 1) {
                            $pages[] = $current;
                        }
                        
                        // Add last page if not already included and there's space
                        if ($last != 1 && $last != $current) {
                            $pages[] = $last;
                        }
                        
                        // Fill remaining slots with pages around current page
                        $remainingSlots = 4 - count($pages);
                        if ($remainingSlots > 0) {
                            $start = max(2, $current - 1);
                            $end = min($last - 1, $current + 1);
                            
                            for ($page = $start; $page <= $end && count($pages) < 4; $page++) {
                                if (!in_array($page, $pages)) {
                                    $pages[] = $page;
                                }
                            }
                        }
                        
                        sort($pages);
                    @endphp

                    @foreach($pages as $index => $page)

                        @if($page == $questions->currentPage())
                            <span class="px-3 py-2 text-sm font-semibold text-white bg-blue-500 rounded-lg border border-blue-500 shadow-sm">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $questions->url($page) }}" 
                               class="px-3 py-2 text-sm font-medium text-gray-700 bg-white rounded-lg border border-gray-300 hover:bg-gray-50 hover:border-gray-400 transition-colors">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                </div>

                <!-- Next Page Link -->
                @if($questions->hasMorePages())
                    <a href="{{ $questions->nextPageUrl() }}" 
                       class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white rounded-lg border border-gray-300 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 ease-in-out group">
                        Next
                        <i class="fas fa-chevron-right ml-2 text-xs group-hover:translate-x-0.5 transition-transform"></i>
                    </a>
                @else
                    <span class="flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-gray-50 rounded-lg border border-gray-200 cursor-not-allowed">
                        Next
                        <i class="fas fa-chevron-right ml-2 text-xs"></i>
                    </span>
                @endif
            </div>
        </div>
    </div>
@endif
            </div>
        </div>

        <!-- Modal -->
        <div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col">

                <!-- Modal Header -->
                <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
                    <h3 id="modalTitle" class="text-xl font-semibold text-gray-800">Add Question</h3>
                    <button 
                        onclick="closeModal()"
                        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors"
                    >
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Modal Form -->
                <form id="questionForm" method="POST" class="flex-1 overflow-y-auto">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <div class="px-6 py-6 space-y-6">

                        <!-- Row 1: Question ID & Category -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Question ID</label>
                                <input 
                                    type="text" 
                                    name="question_id" 
                                    id="qId" 
                                    readonly
                                    class="block w-full px-3 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm text-gray-600"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="competency" 
                                    id="qCategory" 
                                    required
                                    onchange="generateQuestionId()"
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                >
                                    <option value="number_algebra">Number & Algebra</option>
                                    <option value="measurement_geometry">Measurement & Geometry</option>
                                    <option value="data_probability">Data & Probability</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row 2: Difficulty & Type -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Difficulty <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="difficulty_level" 
                                    id="qDifficulty" 
                                    required
                                    onchange="generateQuestionId()"
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                >
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Question Type <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="question_type" 
                                    id="qType" 
                                    required
                                    onchange="updateChoiceFields()"
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                >
                                    <option value="multiple_choice">Multiple Choice</option>
                                    <option value="fill_blanks">Fill in the Blanks</option>
                                    <option value="true_false">True/False</option>
                                    {{-- <option value="drag_drop">Drag & Drop</option>
                                    <option value="connect_dots">Connect Dots</option> --}}
                                </select>
                            </div>
                        </div>

                        <!-- Row 3: Bloom's Level & School Year -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Bloom's Taxonomy Level <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="blooms_level" 
                                    id="qBlooms" 
                                    required
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                >
                                    <option value="remember">Remember</option>
                                    <option value="understand">Understand</option>
                                    <option value="apply">Apply</option>
                                    <option value="analyze">Analyze</option>
                                    <option value="evaluate">Evaluate</option>
                                    <option value="create">Create</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    School Year <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="school_year" 
                                    id="qSchoolYear" 
                                    required
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                >
                                    <option value="2023-2024">2023-2024</option>
                                    <option value="2024-2025" selected>2024-2025</option>
                                    <option value="2025-2026">2025-2026</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row 4: Topic & Points -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Topic Tag <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="topic_tag" 
                                    id="qTopic" 
                                    required
                                    placeholder="e.g. Area of Rectangle"
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Base Points <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    name="base_points" 
                                    id="qPoints" 
                                    required
                                    min="1"
                                    value="10"
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                >
                            </div>
                        </div>

                        <!-- Row 5: Max Time -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Max Allowed Time (seconds) <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                name="max_allowed_time" 
                                id="qTime" 
                                required
                                min="5"
                                value="60"
                                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            >
                        </div>

                        <!-- Row 6: Question Text -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Question Text <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                name="question_text" 
                                id="qText" 
                                required
                                rows="3"
                                placeholder="Enter the question..."
                                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm resize-none"
                            ></textarea>
                        </div>

                        <!-- Choices Section (Dynamic) -->
                        <div id="choicesSection"></div>

                        <!-- Row 7: Hint & Explanation -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hint</label>
                                <input 
                                    type="text" 
                                    name="hint_text" 
                                    id="qHint"
                                    placeholder="Optional hint for students"
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Explanation</label>
                                <input 
                                    type="text" 
                                    name="explanation" 
                                    id="qExplanation"
                                    placeholder="Explanation of the answer"
                                    class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-end gap-3">
                        <button 
                            type="button"
                            onclick="closeModal()"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit"
                            class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors"
                        >
                            Save Question
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- View Modal -->
        <div id="viewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col">

                <!-- Modal Header -->
                <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-800">Question Details</h3>
                    <button 
                        onclick="closeViewModal()"
                        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors"
                    >
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="flex-1 overflow-y-auto px-6 py-6">
                    <div class="space-y-6">

                        <!-- Question Info -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Question ID</label>
                                <p id="viewQuestionId" class="text-sm font-semibold text-gray-900"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Source</label>
                                <p id="viewQuestionSource" class="text-sm text-gray-900"></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Category</label>
                                <p id="viewCategory" class="text-sm text-gray-900"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Difficulty</label>
                                <p id="viewDifficulty" class="text-sm text-gray-900"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Bloom's Level</label>
                                <p id="viewBlooms" class="text-sm text-gray-900"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">School Year</label>
                                <p id="viewSchoolYear" class="text-sm text-gray-900"></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Topic Tag</label>
                                <p id="viewTopic" class="text-sm text-gray-900"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Base Points</label>
                                <p id="viewPoints" class="text-sm text-gray-900"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Max Time</label>
                                <p id="viewTime" class="text-sm text-gray-900"></p>
                            </div>
                        </div>

                        <!-- Question Text -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Question Text</label>
                            <div id="viewQuestionText" class="text-sm text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200"></div>
                        </div>

                        <!-- Choices (for multiple choice) -->
                        <div id="viewChoicesSection" class="hidden">
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-2">Answer Choices</label>
                            <div class="space-y-2">
                                <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <span class="font-semibold text-gray-700 min-w-[24px]">A:</span>
                                    <span id="viewChoiceA" class="text-sm text-gray-900 flex-1"></span>
                                </div>
                                <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <span class="font-semibold text-gray-700 min-w-[24px]">B:</span>
                                    <span id="viewChoiceB" class="text-sm text-gray-900 flex-1"></span>
                                </div>
                                <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <span class="font-semibold text-gray-700 min-w-[24px]">C:</span>
                                    <span id="viewChoiceC" class="text-sm text-gray-900 flex-1"></span>
                                </div>
                                <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <span class="font-semibold text-gray-700 min-w-[24px]">D:</span>
                                    <span id="viewChoiceD" class="text-sm text-gray-900 flex-1"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Correct Answer -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Correct Answer</label>
                            <div id="viewCorrectAnswer" class="text-sm font-semibold text-green-700 bg-green-50 p-3 rounded-lg border border-green-200"></div>
                        </div>

                        <!-- Hint & Explanation -->
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Hint</label>
                                <p id="viewHint" class="text-sm text-gray-600 italic"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Explanation</label>
                                <p id="viewExplanation" class="text-sm text-gray-600"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-end gap-3">
                    <button 
                        onclick="closeViewModal()"
                        class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
@endsection

@push('scripts')
    <script>
        function displayErrors(errors, prefix = '') {
        // Clear all previous error messages
        document.querySelectorAll('[id$="_error"]').forEach(el => {
            el.textContent = '';
        });

        Object.keys(errors).forEach(key => {
            // Generate all possible element IDs that might match
            const variants = [];

            if (prefix) {
                variants.push(`${prefix}_${key}_error`); // edit_status_error
                variants.push(`${prefix}${key.charAt(0).toUpperCase() + key.slice(1)}_error`); // editStatus_error
            }

            variants.push(`${key}_error`); // status_error
            variants.push(`${key.charAt(0).toUpperCase() + key.slice(1)}_error`); // Status_error (edge camel case)

            // Find any matching error element
            const errorElement = variants
                .map(id => document.getElementById(id))
                .find(el => el !== null);

            if (errorElement) {
                errorElement.textContent = errors[key][0];
            } else {
                console.warn(`⚠️ No element found for error field: ${key} (${variants.join(', ')})`);
            }
        });
        }
        let editingId = null;
        let questionCounter = {{ $nextQuestionNumber }};
        let currentViewQuestion = null;

        function openModal(mode = 'add') {
            document.getElementById('modal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = mode === 'add' ? 'Add Question' : 'Edit Question';

            if (mode === 'add') {
                document.getElementById('questionForm').reset();
                document.getElementById('formMethod').value = 'POST';
                document.getElementById('questionForm').action = '{{ route("admin.management.questions.store") }}';
                editingId = null;
                generateQuestionId();
                updateChoiceFields();
            }
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
            editingId = null;
        }

        function closeViewModal() {
            document.getElementById('viewModal').classList.add('hidden');
            currentViewQuestion = null;
        }


        function generateQuestionId() {
            if (editingId) return;

            const category = document.getElementById('qCategory').value;
            const difficulty = document.getElementById('qDifficulty').value;

            const categoryPrefix = {
                'number_algebra': 'NA',
                'measurement_geometry': 'MG',
                'data_probability': 'DP'
            }[category] || 'NA';

            const difficultyPrefix = {
                'beginner': 'B',
                'intermediate': 'I',
                'advanced': 'A'
            }[difficulty] || 'B';

            const id = `${categoryPrefix}-${difficultyPrefix}-${String(questionCounter).padStart(3, '0')}`;
            document.getElementById('qId').value = id;
        }

        function updateChoiceFields() {
            const type = document.getElementById('qType').value;
            const choicesSection = document.getElementById('choicesSection');

            if (type === 'multiple_choice') {
                choicesSection.innerHTML = `
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Answer Choices <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="flex items-center gap-2 bg-white p-3 rounded-lg border border-gray-200">
                                <input type="radio" name="correct_answer" value="A" id="correctA" required class="w-4 h-4 text-blue-500 focus:ring-blue-500">
                                <label for="correctA" class="text-sm font-medium text-gray-700 min-w-[20px]">A:</label>
                                <input type="text" name="choice_a" placeholder="Choice A" required class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="flex items-center gap-2 bg-white p-3 rounded-lg border border-gray-200">
                                <input type="radio" name="correct_answer" value="B" id="correctB" class="w-4 h-4 text-blue-500 focus:ring-blue-500">
                                <label for="correctB" class="text-sm font-medium text-gray-700 min-w-[20px]">B:</label>
                                <input type="text" name="choice_b" placeholder="Choice B" required class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="flex items-center gap-2 bg-white p-3 rounded-lg border border-gray-200">
                                <input type="radio" name="correct_answer" value="C" id="correctC" class="w-4 h-4 text-blue-500 focus:ring-blue-500">
                                <label for="correctC" class="text-sm font-medium text-gray-700 min-w-[20px]">C:</label>
                                <input type="text" name="choice_c" placeholder="Choice C" required class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="flex items-center gap-2 bg-white p-3 rounded-lg border border-gray-200">
                                <input type="radio" name="correct_answer" value="D" id="correctD" class="w-4 h-4 text-blue-500 focus:ring-blue-500">
                                <label for="correctD" class="text-sm font-medium text-gray-700 min-w-[20px]">D:</label>
                                <input type="text" name="choice_d" placeholder="Choice D" required class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Select the radio button next to the correct answer</p>
                    </div>
                `;
            } else if (type === 'true_false') {
                choicesSection.innerHTML = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Correct Answer <span class="text-red-500">*</span>
                        </label>
                        <select name="correct_answer" required class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="True">True</option>
                            <option value="False">False</option>
                        </select>
                    </div>
                `;
            } else if (type === 'fill_blanks') {
                choicesSection.innerHTML = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Correct Answer <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="correct_answer" 
                            required 
                            placeholder="Enter the correct answer"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                        >
                        <p class="text-xs text-gray-500 mt-2">For multiple acceptable answers, separate with commas</p>
                    </div>
                `;
            } else {
                choicesSection.innerHTML = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Correct Answer (JSON format) <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="correct_answer" 
                            required 
                            rows="3"
                            placeholder='{"items": ["item1", "item2"]}'
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-mono resize-none"
                        ></textarea>
                        <p class="text-xs text-gray-500 mt-2">Provide the answer structure in JSON format</p>
                    </div>
                `;
            }
        }

        function editQuestion(question) {
            editingId = question.question_id;
            openModal('edit');

            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('questionForm').action = `/admin/management/questions/${question.question_id}`;
            document.getElementById('qId').value = question.question_id || '';
            document.getElementById('qCategory').value = (question.competency || 'number_algebra');
            document.getElementById('qDifficulty').value = (question.difficulty_level || 'beginner');
            document.getElementById('qType').value = (question.question_type || 'multiple_choice');
            document.getElementById('qBlooms').value = (question.blooms_level || 'remember');
            document.getElementById('qSchoolYear').value = (question.school_year || '2024-2025');
            document.getElementById('qTopic').value = question.topic_tag || '';
            document.getElementById('qPoints').value = (question.base_points != null ? question.base_points : 10);
            document.getElementById('qTime').value = (question.max_allowed_time != null ? question.max_allowed_time : 60);
            document.getElementById('qText').value = question.question_text || '';
            document.getElementById('qHint').value = question.hint_text || '';
            document.getElementById('qExplanation').value = question.explanation || '';

            updateChoiceFields();

            setTimeout(() => {
                if (question.question_type === 'multiple_choice') {
                    const choiceAInput = document.querySelector('input[name="choice_a"]');
                    const choiceBInput = document.querySelector('input[name="choice_b"]');
                    const choiceCInput = document.querySelector('input[name="choice_c"]');
                    const choiceDInput = document.querySelector('input[name="choice_d"]');

                    if (choiceAInput) choiceAInput.value = question.choice_a || '';
                    if (choiceBInput) choiceBInput.value = question.choice_b || '';
                    if (choiceCInput) choiceCInput.value = question.choice_c || '';
                    if (choiceDInput) choiceDInput.value = question.choice_d || '';

                    const correctRadio = document.querySelector(`input[name="correct_answer"][value="${question.correct_answer}"]`);
                    if (correctRadio) correctRadio.checked = true;
                } else {
                    const correctInput = document.querySelector('input[name="correct_answer"], select[name="correct_answer"], textarea[name="correct_answer"]');
                    if (correctInput) correctInput.value = question.correct_answer || '';
                }
            }, 100);
        }

        function viewQuestion(question) {
            currentViewQuestion = question;

            // Populate basic info
            document.getElementById('viewQuestionId').textContent = question.question_id || 'N/A';
            document.getElementById('viewQuestionSource').textContent = question.question_source === 'custom' ? 'Custom' : 'Built-in';
            document.getElementById('viewCategory').textContent = ucwords((question.competency || '').replace(/_/g, ' & ') || 'N/A');
            document.getElementById('viewDifficulty').textContent = ucfirst(question.difficulty_level || 'N/A');
            document.getElementById('viewBlooms').textContent = ucfirst(question.blooms_level || 'N/A');
            document.getElementById('viewSchoolYear').textContent = question.school_year || '2024-2025';
            document.getElementById('viewTopic').textContent = question.topic_tag || 'N/A';
            document.getElementById('viewPoints').textContent = ((question.base_points != null ? question.base_points : 10)) + ' points';
            document.getElementById('viewTime').textContent = ((question.max_allowed_time != null ? question.max_allowed_time : 60)) + ' seconds';
            document.getElementById('viewQuestionText').textContent = question.question_text || 'N/A';

            // Handle choices for multiple choice
            const choicesSection = document.getElementById('viewChoicesSection');
            if (question.question_type === 'multiple_choice') {
                choicesSection.classList.remove('hidden');
                document.getElementById('viewChoiceA').textContent = question.choice_a || 'N/A';
                document.getElementById('viewChoiceB').textContent = question.choice_b || 'N/A';
                document.getElementById('viewChoiceC').textContent = question.choice_c || 'N/A';
                document.getElementById('viewChoiceD').textContent = question.choice_d || 'N/A';
            } else {
                choicesSection.classList.add('hidden');
            }

            // Correct answer
            document.getElementById('viewCorrectAnswer').textContent = question.correct_answer || 'N/A';

            // Hint and explanation
            document.getElementById('viewHint').textContent = question.hint_text || 'No hint provided';
            document.getElementById('viewExplanation').textContent = question.explanation || 'No explanation provided';

            // Show modal
            document.getElementById('viewModal').classList.remove('hidden');
        }

        function ucfirst(str) {
            return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
        }

        function ucwords(str) {
            return str ? str.replace(/\b\w/g, l => l.toUpperCase()) : '';
        }

        // Close modals on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
                closeViewModal();
            }
        });

        // Export dropdown
        const exportDropdownButton = document.getElementById('exportDropdownButton');
        const exportDropdownMenu = document.getElementById('exportDropdownMenu');
        const bulkDropdownButton = document.getElementById('bulkDropdownButton');
        const bulkDropdownMenu = document.getElementById('bulkDropdownMenu');

        if (exportDropdownButton && exportDropdownMenu) {
            exportDropdownButton.addEventListener('click', (e) => {
                e.stopPropagation();
                exportDropdownMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', () => {
                exportDropdownMenu.classList.add('hidden');
            });
        }

        if (bulkDropdownButton && bulkDropdownMenu) {
            bulkDropdownButton.addEventListener('click', (e) => {
                e.stopPropagation();
                bulkDropdownMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', () => {
                bulkDropdownMenu.classList.add('hidden');
            });
        }

        // Initialize on page load
        updateChoiceFields();
    </script>
@endpush
