@extends('layouts.user_layout')

@section('title', 'AralSipnayan')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="max-w-6xl mx-auto px-6 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-blue-900 mb-2">My Achievements</h1>
            <p class="text-gray-600">
                You've earned {{ $earnedCount }} out of {{ $totalCount }} possible achievements!
            </p>
        </div>

        <!-- Progress -->
        @php
            $progress = $totalCount > 0 ? round(($earnedCount / $totalCount) * 100) : 0;
        @endphp
        <div class="bg-white shadow border border-blue-200 p-6 rounded-lg mb-6">
            <div class="flex items-center mb-2">
                <svg class="h-6 w-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
                <h2 class="text-xl font-semibold">Achievement Progress</h2>
            </div>
            <p class="text-gray-500 mb-2">Keep learning to unlock more achievements and show off your math skills!</p>
            <div class="bg-gray-200 rounded-full h-4 mb-2">
                <div class="h-4 rounded-full bg-gradient-to-r from-blue-500 to-blue-700 transition-all duration-500" style="width: {{ $progress }}%"></div>
            </div>
            <div class="text-sm text-gray-600 flex justify-between">
                <span>{{ $earnedCount }} earned</span>
                <span>{{ $progress }}% complete</span>
                <span>{{ $totalCount }} total</span>
            </div>
        </div>

        <!-- Tabs and Sort -->
        <div class="flex flex-col sm:flex-row justify-between gap-4 mb-6">
            <!-- Tabs -->
            <div class="flex space-x-1 bg-white rounded-lg p-1 shadow-sm">
                <a href="{{ route('achievements.index', ['filter' => 'all', 'sort' => $sort]) }}" 
                   class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $filter === 'all' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-900' }}">
                    All
                </a>
                <a href="{{ route('achievements.index', ['filter' => 'earned', 'sort' => $sort]) }}" 
                   class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $filter === 'earned' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-900' }}">
                    Earned
                </a>
                <a href="{{ route('achievements.index', ['filter' => 'locked', 'sort' => $sort]) }}" 
                   class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $filter === 'locked' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-900' }}">
                    Locked
                </a>
            </div>

            <!-- Sort Dropdown -->
            <div class="relative">
                <select name="sort" onchange="window.location.href='{{ route('achievements.index', ['filter' => $filter]) }}&sort=' + this.value" 
                        class="appearance-none bg-white border border-gray-300 rounded-lg px-4 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="default" {{ $sort === 'default' ? 'selected' : '' }}>Default</option>
                    <option value="rarity" {{ $sort === 'rarity' ? 'selected' : '' }}>Rarity</option>
                    <option value="date" {{ $sort === 'date' ? 'selected' : '' }}>Date Earned</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Achievements Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @forelse($filteredAchievements as $achievement)
                <div class="bg-white border rounded-lg p-4 text-center shadow-sm hover:shadow-md transition-all duration-200 {{ $achievement['is_earned'] ? '' : 'opacity-60' }} {{ $achievement['rarity'] === 'Legendary' ? 'border-yellow-400' : '' }}">
                    <div class="mb-3">
                        @if ($achievement['icon'] === 'book')
                            <svg class="w-12 h-12 mx-auto text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/>
                            </svg>
                        @elseif ($achievement['icon'] === 'lightning')
                            <svg class="w-12 h-12 mx-auto text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        @elseif ($achievement['icon'] === 'rocket')
                            <svg class="w-12 h-12 mx-auto text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                            </svg>
                        @elseif ($achievement['icon'] === 'star')
                            <svg class="w-12 h-12 mx-auto text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        @elseif ($achievement['icon'] === 'brain')
                            <svg class="w-12 h-12 mx-auto text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        @elseif ($achievement['icon'] === 'medal')
                            <svg class="w-12 h-12 mx-auto text-pink-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        @elseif ($achievement['icon'] === 'crown')
                            <svg class="w-12 h-12 mx-auto text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        @elseif ($achievement['icon'] === 'flame')
                            <svg class="w-12 h-12 mx-auto text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2c-1.1 0-2 .9-2 2v6c0 1.1.9 2 2 2s2-.9 2-2V4c0-1.1-.9-2-2-2zm0 10c-1.1 0-2 .9-2 2v6c0 1.1.9 2 2 2s2-.9 2-2v-6c0-1.1-.9-2-2-2z"/>
                            </svg>
                        @elseif ($achievement['icon'] === 'trophy')
                            <svg class="w-12 h-12 mx-auto text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        @endif
                    </div>
                    <div class="font-semibold text-gray-900 mb-1">{{ $achievement['title'] }}</div>
                    <div class="text-xs text-gray-500 mb-2">{{ $achievement['description'] }}</div>
                    
                    <!-- Rarity Badge -->
                    @if($achievement['rarity'] === 'Common')
                        <span class="inline-block px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Common</span>
                    @elseif($achievement['rarity'] === 'Uncommon')
                        <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Uncommon</span>
                    @elseif($achievement['rarity'] === 'Rare')
                        <span class="inline-block px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Rare</span>
                    @elseif($achievement['rarity'] === 'Epic')
                        <span class="inline-block px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">Epic</span>
                    @elseif($achievement['rarity'] === 'Legendary')
                        <span class="inline-block px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Legendary</span>
                    @endif
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold mb-2">No achievements found</h3>
                    <p class="text-gray-500 mb-6">Try changing your filters.</p>
                    <a href="{{ route('achievements.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded">View All Achievements</a>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Toast for New Badge -->
@if(session('new_badge'))
    <div id="toast" class="fixed bottom-4 right-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 p-4 rounded shadow-lg flex items-center space-x-3">
        <svg class="h-6 w-6 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
        <div>
            <div class="font-semibold">New Achievement Unlocked!</div>
            <div class="text-sm">{{ session('new_badge')['name'] }} — {{ session('new_badge')['description'] }}</div>
        </div>
    </div>
@endif

<!-- Confetti JS -->
@if(session('new_badge'))
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
    <script>
        confetti({
            particleCount: 100,
            spread: 70,
            origin: { y: 0.6 },
        });
    </script>
@endif
@endsection