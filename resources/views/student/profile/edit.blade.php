@extends('layouts.user_layout')

@section('title', 'Choose Your Avatar')

@section('content')
<div class="min-h-screen">
    <div class="relative -mx-6 sm:-mx-8 lg:-mx-12 overflow-hidden" style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);">
        <!-- Avatar Header -->
        <div class="text-white px-6 pt-4">
            <h2 class="pl-8 text-4xl font-baloo font-bold mb-2">Avatar</h2>
        </div>

        <!-- Main Avatar Display -->
        <div class="text-center mb-4">
            <h1 class="text-white text-4xl font-baloo font-bold mb-4">Choose Your Avatar</h1>
            
            <!-- Avatar Carousel Container -->
            <div class="relative flex items-center justify-center mb-2">
                <!-- Left Arrow -->
                <div class="relative flex items-center justify-center">
                    <!-- Left Arrow -->
                    <button id="prevBtn" class="absolute left-4 md:left-4 text-white hover:text-gray-300 transition-colors z-10">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>

                    <!-- Main Avatar Display -->
                    <div class="w-64 h-100 bg-transparent rounded-3xl flex items-center justify-center mx-8">
                        <img id="mainAvatar" src="{{ $userAvatarUrl ?? asset('images/profile/avatar5.png') }}" alt="Selected Avatar" class="w-48 h-60 object-contain">
                    </div>

                    <!-- Right Arrow -->
                    <button id="nextBtn" class="absolute right-4 md:right-4 text-white hover:text-gray-300 transition-colors z-10">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Avatar Selection Grid -->
    <div class=" relative -mx-6 sm:-mx-8 lg:-mx-12 p-5 sm:p-6 bg-white rounded-2xl -mt-4 sm:-mt-4 lg:-mt-4 z-10 ">
        <div class="max-w-2xl mx-auto">
            <div class="grid grid-cols-3 gap-6 mb-8">
                <!-- Avatar 1 -->
                <div class="avatar-option cursor-pointer rounded-2xl transform hover:scale-105 transition-transform" data-avatar="avatar1.png">
                    <div class="w-full h-32 bg-yellow-400 rounded-2xl shadow-lg flex items-center justify-center">
                        <img src="{{ asset('images/profile/avatar1.png') }}" alt="Avatar 1" class="w-20 h-24 object-contain">
                    </div>
                </div>

                <!-- Avatar 2 -->
                <div class="avatar-option cursor-pointer rounded-2xl transform hover:scale-105 transition-transform" data-avatar="avatar2.png">
                    <div class="w-full h-32 bg-pink-400 rounded-2xl shadow-lg flex items-center justify-center">
                        <img src="{{ asset('images/profile/avatar2.png') }}" alt="Avatar 2" class="w-20 h-24 object-contain">
                    </div>
                </div>

                <!-- Avatar 3 -->
                <div class="avatar-option cursor-pointer rounded-2xl transform hover:scale-105 transition-transform" data-avatar="avatar3.png">
                    <div class="w-full h-32 bg-red-400 rounded-2xl shadow-lg flex items-center justify-center">
                        <img src="{{ asset('images/profile/avatar3.png') }}" alt="Avatar 3" class="w-20 h-24 object-contain">
                    </div>
                </div>

                <!-- Avatar 4 -->
                <div class="avatar-option cursor-pointer rounded-2xl transform hover:scale-105 transition-transform" data-avatar="avatar4.png">
                    <div class="w-full h-32 bg-gray-400 rounded-2xl shadow-lg flex items-center justify-center">
                        <img src="{{ asset('images/profile/avatar4.png') }}" alt="Avatar 4" class="w-20 h-24 object-contain">
                    </div>
                </div>

                <!-- Avatar 5 -->
                <div class="avatar-option cursor-pointer rounded-2xl transform hover:scale-105 transition-transform" data-avatar="avatar5.png">
                    <div class="w-full h-32 bg-orange-400 rounded-2xl shadow-lg flex items-center justify-center">
                        <img src="{{ asset('images/profile/avatar5.png') }}" alt="Avatar 5" class="w-20 h-24 object-contain">
                    </div>
                </div>

                <!-- Avatar 6 -->
                <div class="avatar-option cursor-pointer rounded-2xl transform hover:scale-105 transition-transform" data-avatar="avatar6.png">
                    <div class="w-full h-32 bg-teal-400 rounded-2xl shadow-lg flex items-center justify-center">
                        <img src="{{ asset('images/profile/avatar6.png') }}" alt="Avatar 6" class="w-20 h-24 object-contain">
                    </div>
                </div>
            </div>

            <!-- Select Avatar Button -->
            <div class="text-center">
                <button id="selectAvatarBtn" class="bg-yellow-400 hover:bg-yellow-500 text-white font-baloo font-bold py-4 px-12 rounded-full text-xl shadow-lg transform hover:scale-105 transition-all">
                    Select Your Avatar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const avatars = [
        'avatar1.png',
        'avatar2.png', 
        'avatar3.png',
        'avatar4.png',
        'avatar5.png',
        'avatar6.png'
    ];
    
    // Detect current avatar from the main avatar image src
    const mainAvatar = document.getElementById('mainAvatar');
    const currentAvatarSrc = mainAvatar.src;
    let currentAvatarIndex = 4; // Default to avatar5 (index 4)
    
    // Try to detect current avatar from src
    avatars.forEach((avatar, index) => {
        if (currentAvatarSrc.includes(avatar)) {
            currentAvatarIndex = index;
        }
    });
    
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const avatarOptions = document.querySelectorAll('.avatar-option');
    const selectAvatarBtn = document.getElementById('selectAvatarBtn');

    // Function to update main avatar display
    function updateMainAvatar(index) {
        mainAvatar.src = `{{ asset('images/profile/') }}/${avatars[index]}`;
        currentAvatarIndex = index;
        
        // Update selection highlight
        avatarOptions.forEach((option, i) => {
            if (i === index) {
                option.classList.add('ring-4', 'ring-red-400', 'ring-offset-2');
            } else {
                option.classList.remove('ring-4', 'ring-red-400', 'ring-offset-2');
            }
        });
    }

    // Initialize with current avatar selected
    updateMainAvatar(currentAvatarIndex);

    // Previous button
    prevBtn.addEventListener('click', function() {
        currentAvatarIndex = (currentAvatarIndex - 1 + avatars.length) % avatars.length;
        updateMainAvatar(currentAvatarIndex);
    });

    // Next button
    nextBtn.addEventListener('click', function() {
        currentAvatarIndex = (currentAvatarIndex + 1) % avatars.length;
        updateMainAvatar(currentAvatarIndex);
    });

    // Avatar option clicks
    avatarOptions.forEach((option, index) => {
        option.addEventListener('click', function() {
            updateMainAvatar(index);
        });
    });

    // Select avatar button
    selectAvatarBtn.addEventListener('click', function() {
        const selectedAvatar = avatars[currentAvatarIndex];
        
        // Show loading state
        selectAvatarBtn.textContent = 'Saving...';
        selectAvatarBtn.disabled = true;
        
        // Send AJAX request to complete onboarding with selected avatar
        fetch('{{ route("student.onboarding.complete") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                avatar: selectedAvatar
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                alert(data.message);
                // Redirect to dashboard
                window.location.href = data.redirect_url;
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating avatar. Please try again.');
        })
        .finally(() => {
            // Reset button state
            selectAvatarBtn.textContent = 'Select Your Avatar';
            selectAvatarBtn.disabled = false;
        });
    });
});
</script>
@endsection