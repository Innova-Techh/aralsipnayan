@extends('layouts.user_layout')
@section('title', 'Courses - AralSipnayan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <!-- Floating Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 opacity-20">
            <div class="w-10 h-10 bg-blue-500 clip-triangle animate-bounce"></div>
        </div>
        <div class="absolute top-40 right-20 opacity-20">
            <div class="w-12 h-12 bg-green-500 rounded-full animate-pulse"></div>
        </div>
        <div class="absolute bottom-40 left-1/4 opacity-20">
            <div class="w-9 h-9 bg-yellow-500 animate-bounce" style="animation-delay: 2s;"></div>
        </div>
        <div class="absolute top-1/3 right-1/3 opacity-20">
            <div class="w-11 h-11 bg-purple-500 clip-pentagon animate-bounce" style="animation-delay: 1s;"></div>
        </div>
        <div class="absolute bottom-20 right-10 opacity-20">
            <div class="w-10 h-10 bg-pink-500 clip-hexagon animate-bounce" style="animation-delay: 3s;"></div>
        </div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 py-8">
        <!-- Header with Mascot -->
        <div class="text-center mb-12">
            <div class="flex justify-center items-center gap-4 mb-6">
                <div class="w-20 h-20 bg-gradient-to-r from-orange-400 to-orange-500 rounded-full flex items-center justify-center animate-bounce">
                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-2">
                        Math
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                            Courses
                        </span>
                    </h1>
                    <p class="text-lg text-gray-600">Explore our comprehensive Grade 6 mathematics curriculum</p>
                </div>
                <div class="w-20 h-20 bg-gradient-to-r from-blue-400 to-blue-500 rounded-full flex items-center justify-center animate-pulse">
                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                </div>
            </div>

            <!-- Fun Math Fact Banner -->
            <div class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-2xl p-4 mb-8 relative overflow-hidden">
                <div class="absolute top-2 right-2">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                </div>
                <div class="absolute top-2 left-2 opacity-30">
                    <div class="w-10 h-10 bg-white/20 rounded-full"></div>
                </div>
                <h3 class="text-white font-bold text-lg mb-2">🧠 Math Fun Fact!</h3>
                <p class="text-white text-sm max-w-2xl mx-auto" id="mathFact">
                    Did you know? The word 'mathematics' comes from the Greek word 'mathema' meaning 'knowledge'!
                </p>
                <button class="mt-2 px-4 py-2 bg-white/20 border border-white/30 text-white hover:bg-white/30 rounded-lg text-sm" onclick="nextFact()">
                    Next Fact! 🎲
                </button>
            </div>
        </div>

        <!-- Filter Buttons -->
        <div class="flex flex-wrap justify-center gap-3 mb-8">
            <button class="px-4 py-2 rounded-lg font-medium filter-btn active bg-gradient-to-r from-blue-600 to-purple-600 text-white" data-level="All">
                All
            </button>
            <button class="px-4 py-2 rounded-lg font-medium filter-btn border border-gray-300 text-gray-700 hover:bg-blue-50" data-level="Beginner">
                Beginner
            </button>
            <button class="px-4 py-2 rounded-lg font-medium filter-btn border border-gray-300 text-gray-700 hover:bg-blue-50" data-level="Intermediate">
                Intermediate
            </button>
            <button class="px-4 py-2 rounded-lg font-medium filter-btn border border-gray-300 text-gray-700 hover:bg-blue-50" data-level="Advanced">
                Advanced
            </button>
        </div>

        <!-- Courses Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12" id="coursesGrid">
            @foreach($courses as $course)
            <div class="course-card group hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 bg-white/80 backdrop-blur-sm border-0 overflow-hidden relative rounded-xl" data-level="{{ $course['level'] }}">
                <!-- Gradient Header -->
                <div class="h-32 bg-gradient-to-r {{ $course['color'] }} relative overflow-hidden">
                    <div class="absolute inset-0 bg-black/10"></div>
                    <div class="absolute top-4 right-4">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center animate-bounce">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="{{ $course['icon_path'] }}"/>
                            </svg>
                        </div>
                    </div>
                    <div class="absolute bottom-4 left-4">
                        <svg class="h-8 w-8 text-white/80" fill="currentColor" viewBox="0 0 24 24">
                            <path d="{{ $course['main_icon_path'] }}"/>
                        </svg>
                    </div>
                    <div class="absolute top-2 left-2 text-white/30 text-2xl animate-pulse">+</div>
                    <div class="absolute bottom-2 right-12 text-white/30 text-xl animate-bounce">×</div>
                </div>

                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors mb-2">
                                {{ $course['title'] }}
                            </h3>
                            <p class="text-sm text-gray-600">{{ $course['description'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 mb-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            @if($course['level'] == 'Beginner') bg-blue-100 text-blue-800
                            @elseif($course['level'] == 'Intermediate') bg-gray-100 text-gray-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ $course['level'] }}
                        </span>
                        <div class="flex items-center text-yellow-500">
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <span class="text-sm font-medium ml-1">{{ $course['rating'] }}</span>
                        </div>
                    </div>

                    <!-- Course Stats -->
                    <div class="grid grid-cols-3 gap-4 mb-4 text-center">
                        <div class="bg-blue-50 rounded-lg p-2">
                            <svg class="h-4 w-4 text-blue-600 mx-auto mb-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                            </svg>
                            <div class="text-xs font-medium text-blue-600">{{ $course['duration'] }}</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-2">
                            <svg class="h-4 w-4 text-green-600 mx-auto mb-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/>
                            </svg>
                            <div class="text-xs font-medium text-green-600">{{ $course['lessons'] }} lessons</div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-2">
                            <svg class="h-4 w-4 text-purple-600 mx-auto mb-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A1.5 1.5 0 0 0 18.54 8H16c-.8 0-1.54.37-2.01.97l-2.5 3.15c-.28.35-.49.74-.49 1.18v7.7h2v-7h1.5v7H18z"/>
                            </svg>
                            <div class="text-xs font-medium text-purple-600">{{ $course['students'] }}</div>
                        </div>
                    </div>

                    <!-- Progress -->
                    @if($course['progress'] > 0)
                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Progress</span>
                            <span class="font-medium text-blue-600">{{ $course['progress'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $course['progress'] }}%"></div>
                        </div>
                    </div>
                    @endif

                    <!-- Topics -->
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Topics covered:</h4>
                        <div class="flex flex-wrap gap-1">
                            @foreach($course['topics'] as $topic)
                            <span class="px-2 py-1 bg-gray-100 border border-gray-300 rounded text-xs">{{ $topic }}</span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Action Button -->
                    <a href="{{ route('courses.show', $course['id']) }}" 
                       class="w-full bg-gradient-to-r {{ $course['color'] }} hover:opacity-90 text-white font-medium py-2 px-4 rounded-lg flex items-center justify-center transition-all duration-200">
                        <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                        {{ $course['progress'] > 0 ? 'Continue Learning' : 'Start Course' }}
                    </a>
                </div>

                <!-- Achievement Badge -->
                @if($course['progress'] == 100)
                <div class="absolute top-2 left-2">
                    <div class="bg-yellow-400 rounded-full p-2 shadow-lg">
                        <svg class="h-4 w-4 text-yellow-800" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2-7H3v2h1v11c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V6h1V4zm-3 13H5V6h14v11z"/>
                        </svg>
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Visual Learning Section -->
        <div class="bg-white/60 backdrop-blur-sm rounded-3xl p-8 mb-8 border border-white/20">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    Visual Learning
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Tools</span>
                </h2>
                <p class="text-gray-600">Interactive visual aids to help you understand complex concepts</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Fractions -->
                <div class="text-center">
                    <div class="bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl p-6 mb-4">
                        <div class="flex justify-center items-center gap-4">
                            <div class="w-15 h-15 bg-blue-500 rounded-full relative">
                                <div class="absolute inset-0 rounded-full" style="background: conic-gradient(from 0deg, #3b82f6 0deg 135deg, #e5e7eb 135deg 360deg);"></div>
                            </div>
                            <div class="w-15 h-4 bg-gray-300 rounded relative">
                                <div class="absolute left-0 top-0 h-full w-2/5 bg-green-500 rounded-l"></div>
                            </div>
                        </div>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Fraction Visualization</h3>
                    <p class="text-sm text-gray-600">
                        Understand fractions with pie charts, bars, and real-world examples
                    </p>
                </div>

                <!-- Geometry -->
                <div class="text-center">
                    <div class="bg-gradient-to-br from-green-100 to-green-200 rounded-2xl p-6 mb-4">
                        <div class="flex justify-center items-center gap-3">
                            <div class="w-10 h-10 bg-green-500 clip-triangle"></div>
                            <div class="w-10 h-10 bg-blue-500"></div>
                            <div class="w-10 h-10 bg-yellow-500 rounded-full"></div>
                        </div>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Geometric Shapes</h3>
                    <p class="text-sm text-gray-600">Explore 2D and 3D shapes with interactive tools and measurements</p>
                </div>

                <!-- Problem Solving -->
                <div class="text-center">
                    <div class="bg-gradient-to-br from-purple-100 to-purple-200 rounded-2xl p-6 mb-4">
                        <div class="flex justify-center items-center gap-4">
                            <div class="w-15 h-15 bg-orange-500 rounded-full relative pizza-slice">
                                <div class="absolute inset-0 rounded-full" style="background: conic-gradient(from 0deg, #f97316 0deg 135deg, #e5e7eb 135deg 360deg);"></div>
                            </div>
                            <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Problem Solving</h3>
                    <p class="text-sm text-gray-600">
                        Step-by-step solutions with visual aids and real-world applications
                    </p>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center bg-gradient-to-r from-blue-600 to-purple-600 rounded-3xl p-8 text-white relative overflow-hidden">
            <div class="absolute top-4 left-4 opacity-30">
                <svg class="w-15 h-15" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/>
                </svg>
            </div>
            <div class="absolute bottom-4 right-4 opacity-30">
                <svg class="w-15 h-15" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            </div>
            <div class="relative z-10">
                <h2 class="text-3xl font-bold mb-4">Ready to Master Grade 6 Math?</h2>
                <p class="text-xl mb-6 opacity-90">
                    Join thousands of students who are excelling with our interactive learning platform
                </p>
                <div class="flex justify-center gap-4">
                    <a href="{{ route('courses.index') }}" class="px-6 py-3 bg-white text-blue-600 hover:bg-gray-100 font-semibold rounded-lg flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                        Start Learning Now
                    </a>
                    <a href="{{ route('lessons.index') }}" class="px-6 py-3 border border-white text-white hover:bg-white/10 bg-transparent rounded-lg">
                        View All Lessons
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.clip-triangle {
    clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
}

.clip-pentagon {
    clip-path: polygon(50% 0%, 100% 38%, 82% 100%, 18% 100%, 0% 38%);
}

.clip-hexagon {
    clip-path: polygon(30% 0%, 70% 0%, 100% 50%, 70% 100%, 30% 100%, 0% 50%);
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

.animate-pulse-slow {
    animation: pulse 3s ease-in-out infinite;
}

.filter-btn.active {
    background: linear-gradient(to right, #2563eb, #9333ea);
    color: white;
}

.course-card {
    display: block;
}

.course-card.hidden {
    display: none;
}
</style>

<script>
// Math facts array
const mathFacts = [
    "Did you know? The word 'mathematics' comes from the Greek word 'mathema' meaning 'knowledge'!",
    "Fun fact: Zero was invented by ancient Indian mathematicians around 500 AD!",
    "Amazing: The number π (pi) has been calculated to over 31 trillion decimal places!",
    "Cool: A 'googol' is the number 1 followed by 100 zeros!"
];

let currentFactIndex = 0;

function nextFact() {
    currentFactIndex = (currentFactIndex + 1) % mathFacts.length;
    document.getElementById('mathFact').textContent = mathFacts[currentFactIndex];
}

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const courseCards = document.querySelectorAll('.course-card');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const level = this.dataset.level;
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            filterButtons.forEach(btn => {
                btn.classList.remove('bg-gradient-to-r', 'from-blue-600', 'to-purple-600', 'text-white');
                btn.classList.add('border', 'border-gray-300', 'text-gray-700', 'hover:bg-blue-50');
            });
            
            this.classList.add('active');
            this.classList.remove('border', 'border-gray-300', 'text-gray-700', 'hover:bg-blue-50');
            this.classList.add('bg-gradient-to-r', 'from-blue-600', 'to-purple-600', 'text-white');

            // Filter courses
            courseCards.forEach(card => {
                if (level === 'All' || card.dataset.level === level) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        });
    });
});
</script>
@endsection