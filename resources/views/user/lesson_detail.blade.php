@extends('layouts.user_layout')

@section('title', 'AralSipnayan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header with Back Navigation -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('courses') }}" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span>Back to Lessons</span>
            </a>
        </div>
        <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium" id="lesson-number">
            Lesson {{ $lesson->id }}
        </div>
    </div>

    <!-- Main Title -->
    <div class="text-center">
        <h1 class="text-4xl font-bold text-gray-900 mb-2" id="lesson-title">Loading...</h1>
        <p class="text-gray-600 text-lg" id="lesson-meta">Loading...</p>
    </div>

    <!-- Lesson Progress Section -->
    <div class="bg-gray-50 rounded-xl p-6">
        <div class="bg-white rounded-lg p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2" id="lesson-title-2">Loading...</h2>
                    <p class="text-gray-600" id="lesson-description">Loading...</p>
                </div>
                <div class="flex items-center space-x-2 text-green-600 ml-4" id="lesson-status">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Completed</span>
                </div>
            </div>
            
            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Progress</span>
                    <span class="text-sm font-medium text-blue-600" id="progress-percentage">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" id="progress-bar" style="width: 0%"></div>
                </div>
            </div>

            <!-- Lesson Tabs -->
            <div class="flex space-x-1 bg-gray-100 rounded-lg p-1">
                <button class="flex-1 py-2 px-4 bg-blue-600 text-white rounded-md text-sm font-medium transition-colors flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Lesson</span>
                </button>
                <button class="flex-1 py-2 px-4 text-gray-600 hover:text-gray-900 rounded-md text-sm font-medium transition-colors flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h6l2 2h6a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                    </svg>
                    <span>Video</span>
                </button>
                <button class="flex-1 py-2 px-4 text-gray-600 hover:text-gray-900 rounded-md text-sm font-medium transition-colors flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Quiz</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Lesson Content -->
    <div class="bg-blue-50 rounded-xl p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-4" id="overview-title">Loading...</h3>
        <p class="text-gray-700 leading-relaxed" id="overview-content">Loading...</p>
    </div>

    <!-- Examples Section -->
    <div class="space-y-4" id="examples-container">
        <h3 class="text-xl font-semibold text-gray-900">Examples</h3>
        <div id="examples-list" class="space-y-4">
            <!-- Examples will be populated by JavaScript -->
        </div>
    </div>

    <!-- Bottom Navigation -->
    <div class="flex items-center justify-between pt-6">
        <div class="text-gray-600">
            <span class="font-medium" id="points-available">Points Available: Loading...</span>
        </div>
        
        <div class="flex flex-col items-end space-y-2">
            <a href="#" class="inline-flex items-center space-x-2 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                <span>Next: Watch Video</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</div>

<script>
// Sample data for different lessons
const lessonData = {
    1: {
        title: 'Divisibility Rules',
        grade: 'Grade 6',
        subject: 'Numbers and Number Theory',
        points: 120,
        description: 'Learn the rules for determining if numbers are divisible by 2, 3, 4, 5, 6, 8, 9, and 10',
        progressPercentage: 100,
        status: 'Completed',
        overviewTitle: 'Divisibility Rules Overview',
        overviewContent: 'Divisibility rules help us quickly determine if one number can be divided by another without doing the actual division. These rules are shortcuts that save time and make calculations easier.',
        examples: [
            'A number is divisible by 2 if it ends in 0, 2, 4, 6, or 8. Example: 246 is divisible by 2.',
            'A number is divisible by 3 if the sum of its digits is divisible by 3. Example: 123 → 1+2+3=6, and 6÷3=2, so 123 is divisible by 3.',
            'A number is divisible by 5 if it ends in 0 or 5. Example: 125 and 130 are both divisible by 5.'
        ]
    },
    2: {
        title: 'Linear Equations',
        grade: 'Grade 7',
        subject: 'Algebra',
        points: 150,
        description: 'Solve linear equations with one variable using various methods',
        progressPercentage: 75,
        status: 'In Progress',
        overviewTitle: 'Linear Equations Overview',
        overviewContent: 'Linear equations are mathematical statements that show the equality of two expressions with variables raised only to the first power. They form the foundation for more complex algebraic concepts.',
        examples: [
            'Solve for x: 2x + 3 = 11. Subtract 3 from both sides: 2x = 8. Divide by 2: x = 4.',
            'Solve for y: 3y - 5 = 10. Add 5 to both sides: 3y = 15. Divide by 3: y = 5.',
            'Solve for z: 4z + 2 = 3z + 8. Subtract 3z from both sides: z + 2 = 8. Subtract 2: z = 6.'
        ]
    },
    3: {
        title: 'Quadratic Equations',
        grade: 'Grade 8',
        subject: 'Algebra',
        points: 180,
        description: 'Master quadratic equations and their solutions using factoring and the quadratic formula',
        progressPercentage: 50,
        status: 'In Progress',
        overviewTitle: 'Quadratic Equations Overview',
        overviewContent: 'Quadratic equations are polynomial equations of degree 2. They can be solved using factoring, completing the square, or the quadratic formula. The solutions are called roots or zeros.',
        examples: [
            'Solve x² + 5x + 6 = 0 by factoring: (x + 2)(x + 3) = 0, so x = -2 or x = -3.',
            'Solve 2x² - 8x + 6 = 0 using the quadratic formula: x = [8 ± √(64-48)]/4 = [8 ± 4]/4, so x = 3 or x = 1.',
            'Solve x² - 4 = 0 by factoring: (x + 2)(x - 2) = 0, so x = 2 or x = -2.'
        ]
    },
    4: {
        title: 'Basic Geometry',
        grade: 'Grade 6',
        subject: 'Geometry',
        points: 100,
        description: 'Introduction to geometric shapes and their properties',
        progressPercentage: 0,
        status: 'Not Started',
        overviewTitle: 'Basic Geometry Overview',
        overviewContent: 'Geometry is the branch of mathematics that deals with shapes, sizes, positions, and dimensions of objects. Understanding basic geometric concepts is essential for advanced mathematical studies.',
        examples: [
            'A triangle has three sides and three angles. The sum of all angles in a triangle is always 180 degrees.',
            'A rectangle has four sides with opposite sides equal and all angles are 90 degrees.',
            'A circle is a shape where all points are equidistant from the center. The distance from center to edge is called the radius.'
        ]
    },
    5: {
        title: 'Area and Perimeter',
        grade: 'Grade 7',
        subject: 'Geometry',
        points: 130,
        description: 'Calculate area and perimeter of various geometric shapes',
        progressPercentage: 25,
        status: 'In Progress',
        overviewTitle: 'Area and Perimeter Overview',
        overviewContent: 'Area measures the space inside a shape, while perimeter measures the distance around the shape. Different formulas are used for different geometric figures.',
        examples: [
            'Rectangle: Area = length × width, Perimeter = 2(length + width). For a 5×3 rectangle: Area = 15, Perimeter = 16.',
            'Triangle: Area = ½ × base × height, Perimeter = sum of all sides. For a triangle with sides 3,4,5: Area = 6, Perimeter = 12.',
            'Circle: Area = πr², Circumference = 2πr. For a circle with radius 3: Area ≈ 28.27, Circumference ≈ 18.85.'
        ]
    }
};

// Update page content based on lesson ID
document.addEventListener('DOMContentLoaded', function() {
    const lessonId = {{ $lesson->id }};
    
    if (lessonData[lessonId]) {
        const data = lessonData[lessonId];
        
        // Update title and metadata
        document.getElementById('lesson-title').textContent = data.title;
        document.getElementById('lesson-title-2').textContent = data.title;
        document.getElementById('lesson-meta').textContent = `${data.grade} • ${data.subject} • ${data.points} points`;
        
        // Update lesson description
        document.getElementById('lesson-description').textContent = data.description;
        
        // Update progress
        document.getElementById('progress-percentage').textContent = `${data.progressPercentage}%`;
        document.getElementById('progress-bar').style.width = `${data.progressPercentage}%`;
        
        // Update status
        const statusElement = document.getElementById('lesson-status');
        if (data.status === 'Completed') {
            statusElement.className = 'flex items-center space-x-2 text-green-600 ml-4';
            statusElement.innerHTML = `
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">Completed</span>
            `;
        } else if (data.status === 'In Progress') {
            statusElement.className = 'flex items-center space-x-2 text-yellow-600 ml-4';
            statusElement.innerHTML = `
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">In Progress</span>
            `;
        } else {
            statusElement.className = 'flex items-center space-x-2 text-gray-600 ml-4';
            statusElement.innerHTML = `
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">Not Started</span>
            `;
        }
        
        // Update overview
        document.getElementById('overview-title').textContent = data.overviewTitle;
        document.getElementById('overview-content').textContent = data.overviewContent;
        
        // Update examples
        const examplesList = document.getElementById('examples-list');
        examplesList.innerHTML = '';
        
        data.examples.forEach((example, index) => {
            const exampleDiv = document.createElement('div');
            exampleDiv.className = 'bg-white rounded-xl shadow-sm p-6 border border-gray-200';
            exampleDiv.innerHTML = `
                <h4 class="text-lg font-semibold text-gray-900 mb-3">Example ${index + 1}</h4>
                <p class="text-gray-700 leading-relaxed">${example}</p>
            `;
            examplesList.appendChild(exampleDiv);
        });
        
        // Update points
        document.getElementById('points-available').textContent = `Points Available: ${data.points}`;
        
        // Update page title
        document.title = data.title;
    } else {
        // Fallback for unknown lesson IDs
        document.getElementById('lesson-title').textContent = 'Lesson Not Found';
        document.getElementById('lesson-meta').textContent = 'This lesson does not exist';
    }
});
</script>
@endsection 