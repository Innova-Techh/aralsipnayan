@extends('layouts.user_layout')

@section('title', 'My Courses')

@push('styles')
<style>
    /* Custom styles for tabs and components */
    .tab-list {
        display: flex;
        background: white;
        border-radius: 0.5rem;
        padding: 0.25rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }
    
    .tab-trigger {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #6b7280;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }
    
    .tab-trigger.active {
        background: #3b82f6;
        color: white;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }
    
    .tab-trigger:hover:not(.active) {
        background: #f3f4f6;
        color: #374151;
    }
    
    .lesson-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        transition: all 0.2s;
        border: 1px solid #e5e7eb;
    }
    
    .lesson-card:hover {
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .lesson-card.completed {
        border-color: #10b981;
        background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
    }
    
    .lesson-card.in-progress {
        border-color: #f59e0b;
        background: linear-gradient(135deg, #fffbeb 0%, #ffffff 100%);
    }
    
    .progress-bar {
        width: 100%;
        height: 4px;
        background: #e5e7eb;
        border-radius: 2px;
        overflow: hidden;
    }
    
    .progress-fill {
        height: 100%;
        background: #3b82f6;
        transition: width 0.3s;
    }
    
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .badge-completed {
        background: #dcfce7;
        color: #166534;
    }
    
    .badge-progress {
        background: #fef3c7;
        color: #92400e;
    }
    
    .badge-available {
        background: #eff6ff;
        color: #1e40af;
    }
    
    .search-input {
        position: relative;
    }
    
    .search-input input {
        padding-left: 2.5rem;
    }
    
    .search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        width: 1rem;
        height: 1rem;
    }
    
    .gradient-bg {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0e7ff 100%);
        min-height: calc(100vh - 4rem);
    }
    
    .category-tag {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        background: #f3f4f6;
        color: #6b7280;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="gradient-bg -mx-4 -my-8 px-4 py-8 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Courses</h1>
            <p class="text-gray-600">Explore available lessons and track your learning progress</p>
        </div>

        <!-- Tabs and Filters -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <!-- Tab Navigation -->
            <div class="tab-list w-full md:w-auto">
                <button class="tab-trigger active" data-tab="available">Available Lessons</button>
                <button class="tab-trigger" data-tab="in-progress">In Progress</button>
                <button class="tab-trigger" data-tab="completed">Completed</button>
            </div>

            <!-- Search and Filter -->
            <div class="flex flex-col md:flex-row gap-3">
                <div class="search-input relative">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input 
                        type="text" 
                        id="searchInput"
                        placeholder="Search lessons..." 
                        class="w-full md:w-64 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                </div>

                <select id="categoryFilter" class="w-full md:w-48 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Categories</option>
                    <option value="algebra">Algebra</option>
                    <option value="geometry">Geometry</option>
                    <option value="calculus">Calculus</option>
                    <option value="statistics">Statistics</option>
                    <option value="trigonometry">Trigonometry</option>
                </select>
            </div>
        </div>

        <!-- Tab Content -->
        <div id="tabContent">
            <!-- Available Lessons Tab -->
            <div id="available-tab" class="tab-content active">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($lessons as $lesson)
                        <div class="lesson-card {{ $lesson->progressStatus }}" data-category="{{ $lesson->category }}" data-title="{{ strtolower($lesson->title) }}" data-description="{{ strtolower($lesson->description) }}">
                            <!-- Lesson Image/Thumbnail -->
                            <div class="w-full h-32 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg mb-4 flex items-center justify-center">
                                @if($lesson->category === 'algebra')
                                    <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                @elseif($lesson->category === 'geometry')
                                    <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                    </svg>
                                @elseif($lesson->category === 'calculus')
                                    <svg class="w-12 h-12 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                @else
                                    <svg class="w-12 h-12 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                @endif
                            </div>

                            <!-- Category Tag -->
                            <div class="category-tag">{{ ucfirst($lesson->category) }}</div>

                            <!-- Lesson Title -->
                            <h3 class="font-semibold text-lg text-gray-900 mb-2">{{ $lesson->title }}</h3>

                            <!-- Lesson Description -->
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $lesson->description }}</p>

                            <!-- Progress Bar -->
                            <div class="progress-bar mb-4">
                                <div class="progress-fill" style="width: {{ $lesson->progressPercentage }}%"></div>
                            </div>

                            <!-- Lesson Stats -->
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $lesson->duration }} min
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $lesson->questions_count }} questions
                                </div>
                            </div>

                            <!-- Status Badge -->
                            @if($lesson->progressStatus === 'completed')
                                <div class="badge badge-completed mb-4">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Completed
                                </div>
                            @elseif($lesson->progressStatus === 'in-progress')
                                <div class="badge badge-progress mb-4">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    In Progress
                                </div>
                            @else
                                <div class="badge badge-available mb-4">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Available
                                </div>
                            @endif

                            <!-- Action Button -->
                            <a href="{{ route('courses.show', $lesson->id) }}" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors duration-200 text-center block font-medium">
                                @if($lesson->progressStatus === 'completed')
                                    Review Lesson
                                @elseif($lesson->progressStatus === 'in-progress')
                                    Continue Learning
                                @else
                                    Start Learning
                                @endif
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- In Progress Tab (hidden by default) -->
            <div id="in-progress-tab" class="tab-content hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <!-- Content will be filtered by JavaScript -->
                </div>
            </div>

            <!-- Completed Tab (hidden by default) -->
            <div id="completed-tab" class="tab-content hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <!-- Content will be filtered by JavaScript -->
                </div>
            </div>
        </div>

        <!-- Empty States -->
        <div id="no-results" class="hidden text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No lessons found</h3>
            <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabs = document.querySelectorAll('.tab-trigger');
    const tabContents = document.querySelectorAll('.tab-content');
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const noResults = document.getElementById('no-results');

    // Initialize
    let activeTab = 'available';
    let searchQuery = '';
    let categoryQuery = 'all';

    // Tab switching
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabValue = this.getAttribute('data-tab');
            
            // Update active tab
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Update active tab content
            tabContents.forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('active');
            });
            
            document.getElementById(tabValue + '-tab').classList.remove('hidden');
            document.getElementById(tabValue + '-tab').classList.add('active');
            
            activeTab = tabValue;
            filterLessons();
        });
    });

    // Search functionality
    searchInput.addEventListener('input', function() {
        searchQuery = this.value.toLowerCase();
        filterLessons();
    });

    // Category filter functionality
    categoryFilter.addEventListener('change', function() {
        categoryQuery = this.value;
        filterLessons();
    });

    function filterLessons() {
        const allLessons = document.querySelectorAll('.lesson-card');
        const activeTabContent = document.getElementById(activeTab + '-tab');
        const activeTabGrid = activeTabContent.querySelector('.grid');
        
        // Clear the active tab's grid
        activeTabGrid.innerHTML = '';
        
        let visibleCount = 0;
        
        allLessons.forEach(lesson => {
            const title = lesson.getAttribute('data-title');
            const description = lesson.getAttribute('data-description');
            const category = lesson.getAttribute('data-category');
            const lessonStatus = lesson.classList.contains('completed') ? 'completed' : 
                               lesson.classList.contains('in-progress') ? 'in-progress' : 'available';
            
            // Check if lesson matches current tab
            const matchesTab = (activeTab === 'available') || 
                             (activeTab === 'in-progress' && lessonStatus === 'in-progress') ||
                             (activeTab === 'completed' && lessonStatus === 'completed');
            
            // Check if lesson matches search
            const matchesSearch = searchQuery === '' || 
                                title.includes(searchQuery) || 
                                description.includes(searchQuery);
            
            // Check if lesson matches category
            const matchesCategory = categoryQuery === 'all' || category === categoryQuery;
            
            if (matchesTab && matchesSearch && matchesCategory) {
                // Clone the lesson card and add to active tab
                const clonedLesson = lesson.cloneNode(true);
                activeTabGrid.appendChild(clonedLesson);
                visibleCount++;
            }
        });
        
        // Show/hide no results message
        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
            activeTabContent.querySelector('.grid').classList.add('hidden');
        } else {
            noResults.classList.add('hidden');
            activeTabContent.querySelector('.grid').classList.remove('hidden');
        }
    }

    // Initial filter
    filterLessons();
});
</script>
@endpush