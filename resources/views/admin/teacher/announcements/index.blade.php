@extends('admin.teacher.layouts.app')

@section('title', 'AralSipnayan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Announcements</h1>
            <p class="text-sm text-slate-500 mt-1">Manage and share important updates with your students</p>
        </div>
        <div class="flex justify-end">
            <button
                onclick="document.getElementById('create-modal').showModal()"
                class="inline-flex items-center gap-2 rounded-lg px-4 py-2 bg-indigo-500 text-white hover:bg-indigo-600 shadow-md hover:shadow-lg transition-all duration-200 text-sm font-medium">
                <svg class="w-4 h-4" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"/>
                </svg>

                <span>Create Announcement</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2 fill-current" viewBox="0 0 20 20">
                <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Cards -->
    <div class="grid grid-cols-12 gap-6">
        @forelse($announcements as $announcement)
            <div class="col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-md hover:shadow-xl rounded-lg border border-slate-200 transition-all duration-300 hover:-translate-y-1">
                <div class="flex flex-col h-full p-6">
                    <header>
                        <div class="flex items-center justify-between mb-4">
                            @php
                                $color = match($announcement->priority) {
                                    'high' => 'rose',
                                    'medium' => 'amber',
                                    'low' => 'sky',
                                    default => 'slate',
                                };
                            @endphp
                            <div class="w-12 h-12 rounded-full flex items-center justify-center bg-{{ $color }}-100 text-{{ $color }}-500 shadow-sm">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 16 16">
                                    <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 12c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm1-3H7V4h2v5z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-bold px-3 py-1 rounded-full bg-{{ $color }}-100 text-{{ $color }}-700 uppercase tracking-wide">{{ $announcement->priority }}</span>
                        </div>
                    </header>
                    <div class="grow">
                        <div class="mb-3">
                            <h2 class="text-xl text-slate-800 font-bold leading-tight">{{ $announcement->title }}</h2>
                        </div>
                        <div class="text-sm text-slate-600 mb-4 leading-relaxed">
                            {{ Str::limit($announcement->content, 120) }}
                        </div>
                        <div class="flex items-center text-xs text-slate-500 bg-slate-50 px-3 py-2 rounded-md">
                            <svg class="w-4 h-4 mr-1.5 fill-current" viewBox="0 0 16 16">
                                <path d="M8 8c2.2 0 4-1.8 4-4s-1.8-4-4-4-4 1.8-4 4 1.8 4 4 4zm0 2c-2.7 0-8 1.3-8 4v2h16v-2c0-2.7-5.3-4-8-4z"/>
                            </svg>
                            @if($announcement->assignments->where('section', '!=', null)->count() > 0)
                                <span class="font-medium">Section {{ $announcement->assignments->first()->section }}</span>
                            @else
                                <span class="font-medium">Individual Students</span>
                            @endif
                        </div>
                    </div>
                    <footer class="mt-5 pt-4 border-t border-slate-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-slate-500 text-xs">
                                <svg class="w-4 h-4 mr-1.5 fill-current" viewBox="0 0 16 16">
                                    <path d="M5 4a1 1 0 0 0 0 2h6a1 1 0 1 0 0-2H5zm0 4a1 1 0 0 0 0 2h6a1 1 0 1 0 0-2H5zm0 4a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2H5z"/>
                                    <path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
                                </svg>
                                <span class="font-medium">{{ $announcement->created_at->format('M d, Y') }}</span>
                            </div>
                            <form action="{{ route('teacher.announcements.destroy', $announcement->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex items-center justify-center w-8 h-8 rounded-lg text-rose-500 hover:text-white hover:bg-rose-500 transition-all duration-200">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                                        <path d="M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6z" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </footer>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 bg-white rounded-xl border-2 border-dashed border-slate-300">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 fill-current text-slate-400" viewBox="0 0 16 16">
                        <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 12c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm1-3H7V4h2v5z"/>
                    </svg>
                </div>
                <p class="text-slate-600 font-medium text-lg mb-2">No announcements yet</p>
                <p class="text-slate-500 text-sm">Create your first announcement to share updates with your students</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Backdrop -->
<style>
    dialog::backdrop {
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
    }
    
    dialog {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    
    dialog[open] {
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
</style>

<!-- Create Modal -->
<dialog id="create-modal" class="p-0 overflow-hidden w-full max-w-2xl">
    <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-6 py-5 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 16 16">
                        <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold">Create New Announcement</h3>
            </div>
            <button type="button" onclick="document.getElementById('create-modal').close()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition-all duration-200">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 16 16">
                    <path d="M12.72 3.28a1 1 0 00-1.44 0L8 6.56 4.72 3.28a1 1 0 00-1.44 1.44L6.56 8l-3.28 3.28a1 1 0 101.44 1.44L8 9.44l3.28 3.28a1 1 0 001.44-1.44L9.44 8l3.28-3.28a1 1 0 000-1.44z"/>
                </svg>
            </button>
        </div>
    </div>
    
    <form method="POST" action="{{ route('teacher.announcements.store') }}" class="p-6 bg-slate-50">
        @csrf
        
        <div class="mb-5">
            <label class="block text-sm font-semibold text-slate-700 mb-2" for="title">
                Title <span class="text-rose-500">*</span>
            </label>
            <input id="title" 
                   class="form-input w-full px-4 py-3 rounded-lg border-slate-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200" 
                   type="text" 
                   name="title" 
                   placeholder="Enter announcement title"
                   required />
        </div>
        
        <div class="mb-5">
            <label class="block text-sm font-semibold text-slate-700 mb-2" for="priority">
                Priority <span class="text-rose-500">*</span>
            </label>
            <select id="priority" 
                    class="form-select w-full px-4 py-3 rounded-lg border-slate-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200" 
                    name="priority" 
                    required>
                <option value="low">🔵 Low Priority</option>
                <option value="medium" selected>🟡 Medium Priority</option>
                <option value="high">🔴 High Priority</option>
            </select>
        </div>
        
        <div class="mb-5">
            <label class="block text-sm font-semibold text-slate-700 mb-2" for="content">
                Content <span class="text-rose-500">*</span>
            </label>
            <textarea id="content" 
                      class="form-textarea w-full px-4 py-3 rounded-lg border-slate-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200" 
                      rows="5" 
                      name="content" 
                      placeholder="Write your announcement message here..."
                      required></textarea>
        </div>

        <div class="mb-6" x-data="{ assignType: 'section' }">
            <label class="block text-sm font-semibold text-slate-700 mb-2">
                Assign To <span class="text-rose-500">*</span>
            </label>
            <div class="flex gap-4 mb-3">
                <label class="flex items-center cursor-pointer bg-white px-4 py-2 rounded-lg border-2 border-slate-300 hover:border-indigo-400 transition-all duration-200">
                    <input type="radio" name="assign_to" value="section" class="form-radio text-indigo-500 focus:ring-indigo-500" x-model="assignType" checked>
                    <span class="ml-2 font-medium text-slate-700">Section</span>
                </label>
                <!-- Add individual student logic later if needed -->
            </div>
            
            <div x-show="assignType === 'section'">
                <select name="section" 
                        class="form-select w-full px-4 py-3 rounded-lg border-slate-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition-all duration-200" 
                        required>
                    <option value="" disabled selected>Select a section</option>
                    @foreach($sections as $section)
                        <option value="{{ $section }}">{{ $section }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
            <button type="button" 
                    class="btn px-5 py-2.5 border-2 border-slate-300 hover:border-slate-400 text-slate-700 font-medium rounded-lg transition-all duration-200 hover:bg-slate-50" 
                    onclick="document.getElementById('create-modal').close()">
                Cancel
            </button>
            <button type="submit" 
                    class="btn px-6 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
                <svg class="w-4 h-4 fill-current inline-block mr-2" viewBox="0 0 16 16">
                    <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                </svg>
                Create Announcement
            </button>
        </div>
    </form>
</dialog>
@endsection
