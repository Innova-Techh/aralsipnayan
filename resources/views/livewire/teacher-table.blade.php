<div class="space-y-6">
    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Teachers -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-gray-600">Total Teachers</h4>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $totalTeachers }}</p>
                    <p class="text-xs text-gray-500 mt-1">+3 from last month</p>
                </div>
                <div class="text-blue-500">
                    <i class="fas fa-graduation-cap text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Teachers -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-gray-600">Active Teachers</h4>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $activeTeachers }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $totalTeachers > 0 ? round(($activeTeachers / $totalTeachers) * 100, 1) : 0 }}% of total
                    </p>
                </div>
                <div class="text-blue-500">
                    <i class="fas fa-user-check text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Schools -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-gray-600">Schools</h4>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $schools }}</p>
                    <p class="text-xs text-gray-500 mt-1">Partner schools</p>
                </div>
                <div class="text-blue-500">
                    <i class="fas fa-school text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Sections Managed -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-gray-600">Sections Managed</h4>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $sectionsManaged }}</p>
                    <p class="text-xs text-gray-500 mt-1">Across all grades</p>
                </div>
                <div class="text-blue-500">
                    <i class="fas fa-chalkboard-teacher text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Teacher Directory -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-xl font-semibold text-gray-900">Teacher Directory</h2>
            <p class="text-sm text-gray-500 mt-1">Complete teacher roster with profile information</p>
        </div>

        <!-- Search and Filters -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
            <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                <!-- Search Input -->
                <div class="relative flex-1 w-full">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search teachers..."
                        class="pl-10 pr-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full">
                </div>

                <!-- Grade Filter -->
                <select wire:model.live="gradeFilter"
                    class="text-sm border border-gray-300 rounded-lg py-2.5 px-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="">All Grades</option>
                    <option value="Grade 1">Grade 1</option>
                    <option value="Grade 2">Grade 2</option>
                    <option value="Grade 3">Grade 3</option>
                    <option value="Grade 4">Grade 4</option>
                    <option value="Grade 5">Grade 5</option>
                    <option value="Grade 6">Grade 6</option>
                    <option value="Grade 7">Grade 7</option>
                    <option value="Grade 8">Grade 8</option>
                </select>

                <!-- Status Filter -->
                <select wire:model.live="statusFilter"
                    class="text-sm border border-gray-300 rounded-lg py-2.5 px-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="archived">Archived</option>
                </select>

                <!-- Filter Icon Button -->
                <button class="px-3 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-filter text-gray-600"></i>
                </button>

                <!-- Sort Icon Button -->
                <button class="px-3 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-sort text-gray-600"></i>
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Teacher
                        </th>
                       
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            School & Grade
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Sections
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Last Activity
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($teachers as $teacher)
                        <tr class="hover:bg-gray-50 transition">
                            <!-- Teacher Column -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        @if($teacher->teacherProfile && $teacher->teacherProfile->profile_url)
                                            @php
                                                $teacherProfileUrl = $teacher->teacherProfile->profile_url;
                                                $teacherAvatar = \Illuminate\Support\Str::startsWith($teacherProfileUrl, ['http://', 'https://'])
                                                    ? $teacherProfileUrl
                                                    : asset(ltrim($teacherProfileUrl, '/'));
                                            @endphp
                                            <img class="h-10 w-10 rounded-full object-cover"
                                                src="{{ $teacherAvatar }}"
                                                alt="{{ $teacher->username }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-gray-500 font-medium text-sm">
                                                    {{ strtoupper(substr($teacher->username ?? 'T', 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            @if($teacher->teacherProfile)
                                                {{ $teacher->teacherProfile->firstname }}
                                                {{ $teacher->teacherProfile->lastname }}
                                            @else
                                                {{ $teacher->username }}
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </td>


                            <!-- School & Grade Column -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($teacher->teacherProfile && $teacher->teacherProfile->school_name)
                                        {{ $teacher->teacherProfile->school_name }}
                                    @else
                                        -
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">
                                    @if($teacher->teacherProfile && $teacher->teacherProfile->grade_level_focus)
                                        {{ $teacher->teacherProfile->grade_level_focus }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>

                            <!-- Sections Column -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex gap-1 flex-wrap">
                                    @if(!empty($teacher->sections))
                                        @foreach(array_slice($teacher->sections, 0, 3) as $section)
                                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded">
                                                {{ str_replace('Section ', '', $section) }}
                                            </span>
                                        @endforeach
                                        @if(count($teacher->sections) > 3)
                                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded">
                                                +{{ count($teacher->sections) - 3 }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400">No sections</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Status Column -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($teacher->status === 'active')
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Archived
                                    </span>
                                @endif
                            </td>

                            <!-- Last Activity Column -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($teacher->last_login_date)
                                    {{ $teacher->last_login_date->format('Y-m-d') }}
                                @else
                                    Never
                                @endif
                            </td>

                            <!-- Actions Column -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <button class="text-gray-400 hover:text-gray-600 transition" title="More options">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <i class="fas fa-chalkboard-teacher text-4xl mb-3 text-gray-300"></i>
                                    <p class="text-lg font-medium text-gray-600">No teachers found</p>
                                    <p class="text-sm text-gray-400">Try adjusting your search criteria</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($teachers->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $teachers->links() }}
            </div>
        @endif
    </div>

</div>