@extends('layouts.user_layout')

@section('title', 'AralSipnayan')

@section('content')

<div class="min-h-screen bg-white">
    <div class="space-y-6 sm:space-y-8">
        <!-- Header -->
        <div class="relative -mx-6 sm:-mx-8 lg:-mx-12 p-5 sm:p-6 text-white overflow-hidden bg-center bg-cover" style="background-image: url('{{ asset('images/achievements/background.png') }}');">
        <div class="relative z-10 px-6 sm:px-8 lg:px-12">
            <h1 class="text-[22px] sm:text-base md:text-xl lg:text-3xl font-extrabold leading-tight">My Achievements</h1>
            <p class="text-[10px] sm:text-sm md:text-base lg:text-lg text-blue-100 mt-2 sm:mt-3 md:mt-4">Celebrate your math journey! Unlock badges, earn trophies,
                 and show off your incredible learning progress.</p>
        </div>
        </div>

        <!-- Progress -->
        @php
            $progress = $totalCount > 0 ? round(($earnedCount / $totalCount) * 100) : 0;
        @endphp
        <div class="bg-white shadow-xl border  p-6 rounded-2xl mb-6">
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
<div class="flex justify-between items-center gap-3 mb-6">             
    <!-- Tabs -->             
    <div class="flex space-x-1 bg-white rounded-xl p-1 shadow-sm flex-shrink min-w-0">                 
        <a href="{{ route('achievements.index', ['filter' => 'all', 'sort' => $sort]) }}"                     
           class="px-2 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition-colors whitespace-nowrap {{ $filter === 'all' ? 'bg-primary-blue text-white' : 'text-gray-600 hover:text-gray-900' }}">                     
            All                 
        </a>                 
        <a href="{{ route('achievements.index', ['filter' => 'earned', 'sort' => $sort]) }}"                     
           class="px-2 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition-colors whitespace-nowrap {{ $filter === 'earned' ? 'bg-primary-blue text-white' : 'text-gray-600 hover:text-gray-900' }}">                     
            Earned                 
        </a>                 
        <a href="{{ route('achievements.index', ['filter' => 'locked', 'sort' => $sort]) }}"                     
           class="px-2 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition-colors whitespace-nowrap {{ $filter === 'locked' ? 'bg-primary-blue text-white' : 'text-gray-600 hover:text-gray-900' }}">                     
            Locked                 
        </a>             
    </div>             
    
    <!-- Custom Sort Dropdown -->             
    <div x-data="{ open: false }" class="relative inline-block text-left flex-shrink-0">             
        <!-- Button -->             
        <button @click="open = !open"                      
                class="flex items-center justify-between w-24 sm:w-40 rounded-xl border border-gray-300 bg-white px-2 sm:px-4 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">                 
                <div class="flex items-center space-x-2 py-1">
                    <span class="text-xs sm:text-sm px-1 sm:px-2 truncate">Rarity</span>
                    <span class="text-xs sm:text-sm text-gray-500 font-extrabold">🔽</span>
                </div>                         
        </button>              
        
        <!-- Dropdown Menu -->             
        <div x-show="open"                  
             @click.outside="open = false"                 
             class="absolute right-0 mt-2 w-32 sm:w-40 rounded-xl bg-white shadow-lg border border-gray-200 z-10 overflow-hidden">                 
            <a href="{{ route('achievements.index', ['filter' => $filter, 'sort' => 'default']) }}"                 
               class="block px-3 sm:px-4 py-2 text-xs sm:text-sm hover:bg-gray-100">Default</a>                 
            <a href="{{ route('achievements.index', ['filter' => $filter, 'sort' => 'rarity']) }}"                 
               class="block px-3 sm:px-4 py-2 text-xs sm:text-sm hover:bg-gray-100">Rarity</a>                 
            <a href="{{ route('achievements.index', ['filter' => $filter, 'sort' => 'date']) }}"                 
               class="block px-3 sm:px-4 py-2 text-xs sm:text-sm hover:bg-gray-100">Date Earned</a>             
        </div>             
    </div>              
</div>

        <!-- Achievements Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4 px-4 py-4">             
        @forelse($filteredAchievements as $achievement)                 
            <div class="flip-card-container" style="perspective: 1000px;">                     
                <div class="flip-card relative w-full aspect-[4/5] cursor-pointer transition-all duration-300 hover:scale-105 hover:-translate-y-1 {{ $achievement['rarity'] === 'Legendary' ? 'shadow-lg shadow-yellow-400' : '' }}"                           
                    data-achievement-id="{{ $loop->index }}"
                    data-earned="{{ $achievement['is_earned'] ? 'true' : 'false' }}"                          
                    title="{{ $achievement['is_earned'] ? 'Click to flip' : 'Achievement locked' }}">                                                  
                    <!-- Front of card -->                         
                    <div class="flip-card-front absolute w-full h-full rounded-xl overflow-hidden shadow-lg {{ !$achievement['is_earned'] ? 'opacity-60' : '' }}" style="background-color: <?= $achievement['background_light'] ?>; backface-visibility: hidden;">
                            <!-- Rarity Badge - Top Right -->
                            <div class="absolute top-2 right-2 z-20">
                                <span class="inline-block px-2 py-1 text-xs rounded-full border-2 shadow-sm text-white" style ="background-color: <?= $achievement['background_light'] ?>;border-color: <?= $achievement['background_dark'] ?>;">
                                    {{ $achievement['rarity'] }}
                                </span>
                            </div>
                        <!-- Card Header -->
                            <div class="relative h-2/3 flex items-center justify-center mt-6 mx-4 rounded-xl" style="background-color: <?= $achievement['background_dark'] ?>;">
                                <!-- Character Image -->
                                <img src="{{ asset('images/achievements/' . $achievement['front_image']) }}" 
                                    alt="{{ $achievement['title'] }}" 
                                    class="w-36 h-36 sm:w-40 sm:h-40 md:w-44 md:h-44 object-contain ">
                                
                                <!-- Lock overlay for unearned achievements -->
                                @if(!$achievement['is_earned'])
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="bg-black/60 rounded-full p-3">
                                            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2C8.1 2 5 5.1 5 9v1H4c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2v-8c0-1.1-.9-2-2-2h-1V9c0-3.9-3.1-7-7-7zM12 4c2.8 0 5 2.2 5 5v1H7V9c0-2.8 2.2-5 5-5zm0 13c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/>
                                            </svg>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        
                        
                        <!-- Card Footer -->
                        <div class="absolute bottom-2 left-2 right-0 to-transparent p-3 text-white">

                            <h3 class="text-sm sm:text-base md:text-lg lg:text-xl font-bold truncate">
                                {{ $achievement['title'] }}
                            </h3>

                            <p class="text-[9px] sm:text-sm md:text-sm lg:text-md opacity-80 truncate">
                                {{ $achievement['description'] }}
                            </p>
                        </div>
                    </div>                                                  
                    
                    <!-- Back of card (only shown for earned achievements) -->      
                    @if($achievement['is_earned'])                   
                    <div class="flip-card-back absolute w-full h-full rounded-xl overflow-hidden shadow-lg" 
                        style="backface-visibility: hidden; transform: rotateY(180deg);">
                        <div class="p-2 sm:p-4 h-full flex flex-col items-center justify-center text-white text-center border-8 rounded-2xl" style="background-color: <?= $achievement['background_dark'] ?>; border-color: <?= $achievement['background_light'] ?>; ">
                            
                            <h3 class="text-xl sm:text-2xl font-bold mb-1 sm:mb-2">{{ $achievement['title'] }}</h3>
                            <p class="text-sm sm:text-sm opacity-90 mb-6">{{ $achievement['description'] }}</p>
                            
                            <!-- Centered Content -->
                            <div class="flex flex-col gap-4 items-center justify-center flex-1">
                                <!-- Rarity Section -->
                                <div class="flex-2 flex-col items-center">
                                    <span class="text-xs sm:text-sm font-medium">Rarity</span>
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($achievement['rarity'] === 'Common') bg-gray-500 text-white
                                        @elseif($achievement['rarity'] === 'Uncommon') bg-green-500 text-white
                                        @elseif($achievement['rarity'] === 'Rare') bg-red-500 text-white
                                        @elseif($achievement['rarity'] === 'Epic') bg-purple-500 text-white
                                        @elseif($achievement['rarity'] === 'Legendary') bg-yellow-500 text-black
                                        @endif">
                                        {{ $achievement['rarity'] }}
                                    </span>
                                </div>

                                <!-- Reward Section -->
                                <div class="flex-2 flex-col items-center">
                                    <span class="text-xs sm:text-sm font-medium">Reward</span>
                                    <span class="text-xs sm:text-sm font-semibold">{{ $achievement['reward'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>      
                    @endif                                      
                    
                    <!-- Flip indicator (only for earned achievements) -->         
                    @if($achievement['is_earned'])                
                    <div class="flip-indicator absolute top-2 left-2 bg-black/50 text-white text-xs px-2 py-1 rounded-full opacity-0 transition-opacity duration-200">                             
                        Flip                         
                    </div>   
                    @endif                  
                </div>                 
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

<!-- Flip Card JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const flipCards = document.querySelectorAll('.flip-card');
    
    flipCards.forEach(card => {
        let isFlipped = false;
        const isEarned = card.dataset.earned === 'true';
        
        card.addEventListener('click', function() {
            if (!isEarned) {
                // Shake animation for locked achievements
                this.style.animation = 'shake 0.5s ease-in-out';
                setTimeout(() => {
                    this.style.animation = '';
                }, 500);
                return;
            }
            
            // Flip animation for earned achievements
            if (!isFlipped) {
                // Flip to back
                this.style.transform = 'rotateY(180deg)';
                this.style.transformStyle = 'preserve-3d';
                isFlipped = true;
            } else {
                // Flip to front
                this.style.transform = 'rotateY(0deg)';
                this.style.transformStyle = 'preserve-3d';
                isFlipped = false;
            }
        });
        
        // Show flip indicator on hover (only for earned achievements)
        if (isEarned) {
            card.addEventListener('mouseenter', function() {
                const indicator = this.querySelector('.flip-indicator');
                if (indicator) {
                    indicator.style.opacity = '1';
                }
            });
            
            card.addEventListener('mouseleave', function() {
                const indicator = this.querySelector('.flip-indicator');
                if (indicator) {
                    indicator.style.opacity = '0';
                }
            });
        }
    });
});
</script>

@endsection