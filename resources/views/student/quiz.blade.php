@extends('layouts.user_layout')

@section('title', 'Quiz - AralSipnayan')

@section('content')

<div class="space-y-8 font-baloo mt-10">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6 md:mb-8">
        <!-- Question Counter -->
<!-- Question Counter -->
<div class="bg-purple-700 backdrop-blur-sm rounded-full px-6 py-3 md:px-9 md:py-4 border-b-6 border-[#4a1377]"
     style="box-shadow: 0 8px 0 #4a1377;">
    <span class="text-white font-semibold text-base md:text-lg">
        Question {{ $currentQuestion ?? 1 }} of {{ $totalQuestions ?? 5 }}
    </span>
</div>

<!-- Timer -->
<div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-full px-6 py-3 md:px-9 md:py-4 flex items-center gap-3 border-b-6 border-[#cc4713]"
     style="box-shadow: 0 8px 0 #cc4713;">
    <svg class="w-6 h-6 md:w-7 md:h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
    </svg>
    <span class="text-white font-bold text-base md:text-lg">22:35</span>
</div>

    </div>

    <!-- Quiz Card -->
    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl md:rounded-3xl p-6 md:p-8 lg:p-10 shadow-2xl">
        
        <!-- Hint Button -->
        <div class="flex justify-start mb-6">
            <button class="bg-gradient-to-r from-orange-400 to-orange-500 text-white px-4 py-2 md:px-6 md:py-2 rounded-full font-semibold text-sm md:text-base hover:from-orange-500 hover:to-orange-600 transition-all duration-200 transform hover:scale-105 border-b-4 border-[#cc4713] shadow-lg">
                💡 HINT
            </button>
        </div>

        <!-- Question -->
        <div class="mb-8 md:mb-10">
            <h2 class="text-gray-800 text-lg md:text-xl lg:text-2xl font-semibold leading-relaxed">
                {{ $question->text ?? "What is the area of a rectangle with length 8 cm and width 5 cm?" }}
            </h2>
            
            <!-- Question Image (if exists) -->
            @if(isset($question->image))
            <div class="mt-4 flex justify-center">
                <img src="{{ $question->image }}" alt="Question Image" class="max-w-full h-auto rounded-lg shadow-md">
            </div>
            @endif
        </div>

        <!-- Answer Section - Dynamic based on question type -->
        <div class="mb-8 md:mb-10">
            @php
                $questionType = $question->type ?? 'multiple_choice';
            @endphp

            @if($questionType === 'multiple_choice')
                <!-- Multiple Choice Options -->
                <div class="space-y-4 md:space-y-5" id="multiple-choice-container">
                    @foreach(['A', 'B', 'C', 'D'] as $index => $letter)
                    <label class="flex items-center p-4 md:p-5 bg-gray-100 border-2 border-transparent rounded-xl md:rounded-2xl cursor-pointer hover:bg-gray-200 transition-all duration-200 option-label">
                        <input type="radio" name="answer" value="{{ $letter }}" class="hidden">
                        <div class="flex items-center justify-center w-8 h-8 md:w-10 md:h-10 bg-gray-500 text-white rounded-full font-bold text-sm md:text-base mr-4 md:mr-5 option-circle">
                            {{ $letter }}
                        </div>
                        <span class="text-gray-700 font-medium text-base md:text-lg">
                            {{ $question->options[$index] ?? ($letter === 'A' ? '30 cm²' : ($letter === 'B' ? '40 cm²' : ($letter === 'C' ? '45 cm²' : '50 cm²'))) }}
                        </span>
                    </label>
                    @endforeach
                </div>

            @elseif($questionType === 'fill_in_blanks')
                <!-- Fill in the Blanks -->
                <div class="space-y-4" id="fill-blanks-container">
                    <div class="text-gray-700 text-lg md:text-xl leading-relaxed">
                        {!! str_replace('_____', '<input type="text" name="blank_answer" class="inline-block border-b-2 border-blue-400 bg-transparent px-2 py-1 mx-2 min-w-24 text-center focus:outline-none focus:border-blue-600" placeholder="Answer">', $question->text_with_blanks ?? 'The area of a rectangle is _____ × _____.') !!}
                    </div>
                </div>

            @elseif($questionType === 'true_false')
                <!-- True or False -->
                <div class="space-y-4 md:space-y-5" id="true-false-container">
                    <label class="flex items-center p-4 md:p-5 bg-gray-100 border-2 border-transparent rounded-xl md:rounded-2xl cursor-pointer hover:bg-gray-200 transition-all duration-200 option-label">
                        <input type="radio" name="answer" value="true" class="hidden">
                        <div class="flex items-center justify-center w-8 h-8 md:w-10 md:h-10 bg-gray-500 text-white rounded-full font-bold text-sm md:text-base mr-4 md:mr-5 option-circle">
                            T
                        </div>
                        <span class="text-gray-700 font-medium text-base md:text-lg">True</span>
                    </label>
                    
                    <label class="flex items-center p-4 md:p-5 bg-gray-100 border-2 border-transparent rounded-xl md:rounded-2xl cursor-pointer hover:bg-gray-200 transition-all duration-200 option-label">
                        <input type="radio" name="answer" value="false" class="hidden">
                        <div class="flex items-center justify-center w-8 h-8 md:w-10 md:h-10 bg-gray-500 text-white rounded-full font-bold text-sm md:text-base mr-4 md:mr-5 option-circle">
                            F
                        </div>
                        <span class="text-gray-700 font-medium text-base md:text-lg">False</span>
                    </label>
                </div>

            @elseif($questionType === 'drag_drop')
                <!-- Drag and Drop -->
                <div id="drag-drop-container">
                    <div class="mb-6">
                        <h3 class="text-gray-700 font-semibold mb-4">Drag the items to their correct positions:</h3>
                        
                        <!-- Draggable Items -->
                        <div class="flex flex-wrap gap-3 mb-6 p-4 bg-blue-50 rounded-lg min-h-20" id="draggable-items">
                            @foreach($question->draggable_items ?? ['Item 1', 'Item 2', 'Item 3'] as $item)
                            <div class="draggable-item bg-white px-4 py-2 rounded-lg shadow-md cursor-move border-2 border-blue-200 hover:border-blue-400 transition-colors" draggable="true" data-item="{{ $item }}">
                                {{ $item }}
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- Drop Zones -->
                        <div class="space-y-3">
                            @foreach($question->drop_zones ?? ['Zone 1', 'Zone 2', 'Zone 3'] as $zone)
                            <div class="drop-zone min-h-16 p-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 flex items-center justify-center text-gray-500" data-zone="{{ $zone }}">
                                {{ $zone }} (Drop here)
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            @elseif($questionType === 'connect_dots')
                <!-- Connect the Dots -->
                <div id="connect-dots-container">
                    <div class="mb-6">
                        <h3 class="text-gray-700 font-semibold mb-4">Connect the matching items:</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Left Column -->
                            <div class="space-y-4">
                                <h4 class="font-medium text-gray-600">Column A</h4>
                                @foreach($question->left_items ?? ['Triangle', 'Square', 'Circle'] as $index => $item)
                                <div class="connect-item bg-white p-3 rounded-lg shadow-md border-2 border-blue-200 cursor-pointer hover:border-blue-400 transition-colors" data-left="{{ $index }}">
                                    {{ $item }}
                                </div>
                                @endforeach
                            </div>
                            
                            <!-- Right Column -->
                            <div class="space-y-4">
                                <h4 class="font-medium text-gray-600">Column B</h4>
                                @foreach($question->right_items ?? ['3 sides', '4 equal sides', 'Round shape'] as $index => $item)
                                <div class="connect-item bg-white p-3 rounded-lg shadow-md border-2 border-green-200 cursor-pointer hover:border-green-400 transition-colors" data-right="{{ $index }}">
                                    {{ $item }}
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Selected Connections Display -->
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-medium text-gray-600 mb-2">Your Connections:</h4>
                            <div id="connections-display" class="text-sm text-gray-700">
                                No connections made yet.
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button class="bg-gradient-to-r from-green-500 to-green-600 text-white px-8 py-3 md:px-12 md:py-4 rounded-2xl font-bold text-base md:text-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 transform hover:scale-105 border-b-4 border-[#0b830b] shadow-lg" onclick="submitAnswer()">
                SUBMIT ANSWER
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeQuestionType();
});

function initializeQuestionType() {
    // Multiple Choice & True/False Handler
    const labels = document.querySelectorAll('.option-label');
    labels.forEach((label) => {
        label.addEventListener('click', function() {
            // Reset all labels in the same container
            const container = this.closest('[id$="-container"]');
            const allLabels = container.querySelectorAll('.option-label');
            
            allLabels.forEach(l => {
                l.classList.remove('bg-blue-100', 'border-blue-400');
                l.classList.add('bg-gray-100', 'border-transparent');
                const circle = l.querySelector('.option-circle');
                circle.classList.remove('bg-blue-500');
                circle.classList.add('bg-gray-500');
            });
            
            // Set selected label
            this.classList.remove('bg-gray-100', 'border-transparent');
            this.classList.add('bg-blue-100', 'border-blue-400');
            const circle = this.querySelector('.option-circle');
            circle.classList.remove('bg-gray-500');
            circle.classList.add('bg-blue-500');
            
            // Check the radio button
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
        });
    });

    // Drag and Drop Handler
    initializeDragDrop();
    
    // Connect the Dots Handler
    initializeConnectDots();
}

function initializeDragDrop() {
    const draggableItems = document.querySelectorAll('.draggable-item');
    const dropZones = document.querySelectorAll('.drop-zone');
    
    draggableItems.forEach(item => {
        item.addEventListener('dragstart', function(e) {
            e.dataTransfer.setData('text/plain', this.dataset.item);
            this.classList.add('opacity-50');
        });
        
        item.addEventListener('dragend', function() {
            this.classList.remove('opacity-50');
        });
    });
    
    dropZones.forEach(zone => {
        zone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('bg-blue-100', 'border-blue-400');
        });
        
        zone.addEventListener('dragleave', function() {
            this.classList.remove('bg-blue-100', 'border-blue-400');
        });
        
        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            const itemData = e.dataTransfer.getData('text/plain');
            this.textContent = itemData;
            this.classList.remove('bg-blue-100', 'border-blue-400');
            this.classList.add('bg-green-100', 'border-green-400');
        });
    });
}

let connections = [];

function initializeConnectDots() {
    const leftItems = document.querySelectorAll('[data-left]');
    const rightItems = document.querySelectorAll('[data-right]');
    let selectedLeft = null;
    
    leftItems.forEach(item => {
        item.addEventListener('click', function() {
            // Reset previous selection
            leftItems.forEach(l => l.classList.remove('bg-blue-200', 'border-blue-500'));
            
            // Select current item
            this.classList.add('bg-blue-200', 'border-blue-500');
            selectedLeft = this.dataset.left;
        });
    });
    
    rightItems.forEach(item => {
        item.addEventListener('click', function() {
            if (selectedLeft !== null) {
                const leftText = document.querySelector(`[data-left="${selectedLeft}"]`).textContent;
                const rightText = this.textContent;
                
                // Add connection
                connections.push({left: selectedLeft, right: this.dataset.right, leftText, rightText});
                
                // Update display
                updateConnectionsDisplay();
                
                // Reset selection
                leftItems.forEach(l => l.classList.remove('bg-blue-200', 'border-blue-500'));
                this.classList.add('bg-green-200', 'border-green-500');
                selectedLeft = null;
            }
        });
    });
}

function updateConnectionsDisplay() {
    const display = document.getElementById('connections-display');
    if (connections.length === 0) {
        display.textContent = 'No connections made yet.';
    } else {
        display.innerHTML = connections.map(conn => 
            `<div class="mb-1">${conn.leftText} → ${conn.rightText}</div>`
        ).join('');
    }
}

function submitAnswer() {
    // Handle different question types
    const questionType = '{{ $questionType ?? "multiple_choice" }}';
    let answer = null;
    
    switch(questionType) {
        case 'multiple_choice':
        case 'true_false':
            const selectedRadio = document.querySelector('input[name="answer"]:checked');
            answer = selectedRadio ? selectedRadio.value : null;
            break;
            
        case 'fill_in_blanks':
            const blankInputs = document.querySelectorAll('input[name="blank_answer"]');
            answer = Array.from(blankInputs).map(input => input.value);
            break;
            
        case 'drag_drop':
            const dropZones = document.querySelectorAll('.drop-zone');
            answer = Array.from(dropZones).map(zone => ({
                zone: zone.dataset.zone,
                item: zone.textContent.replace(' (Drop here)', '')
            }));
            break;
            
        case 'connect_dots':
            answer = connections;
            break;
    }
    
    console.log('Answer submitted:', answer);
    // Here you would typically send the answer to your backend
}
</script>
@endsection