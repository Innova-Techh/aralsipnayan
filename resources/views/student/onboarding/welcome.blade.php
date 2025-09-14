@extends('layouts.user_layout')

@section('title', 'Welcome')

@section('content')
<div class="min-h-screen flex items-start justify-center px-2 mt-10 lg:mt-20">
  <div 
    class="w-full max-w-4xl min-h-[600px] rounded-2xl shadow-2xl overflow-hidden flex flex-col justify-between bg-[url('{{ asset('images/onboarding/bg.png') }}')] bg-cover bg-center border-b-8 border-[#532f2b] shadow-lg"
    style="background-image: url('{{ asset('images/onboarding/bg.png') }}');"
  >
    <div class=" w-full h-full flex flex-col justify-between">
      <!-- Welcome Header -->
    <div class="text-center px-4 pt-10 mb-4 lg:mb-40 font-baloo font-extrabold">
      <h1 class="text-7xl md:text-8xl font-extrabold">
        <span class="relative text-gray-700"
              style="-webkit-text-stroke: 2px #facc15;">
          Welcome,
        </span>
        <span class="relative "
              style="-webkit-text-stroke: 2px #06b6d4; color: #dc2626;">
          {{ $student->firstname }}!
        </span>
      </h1>
    </div>



      <!-- Feature Highlights -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6 lg:mb-14">
        <!-- Card 1 -->
        <div class="relative rounded-xl text-center h-32 md:h-40 lg:h-56 flex flex-col items-center justify-center font-medium text-white transition-transform duration-300 transform hover:scale-105 hover:-translate-y-2 border-b-8 border-[#A12115] shadow-lg"
             style="background: linear-gradient(135deg, #D8493B 0%, #E18435 50%, #E1B316 100%);">
          <span class="text-5xl lg:text-8xl">📘</span>
          <p class="mt-2 text-2xl md:text-3xl font-baloo font-extrabold">
            Learn through fun challenges
          </p>
        </div>

        <!-- Card 2 -->
        <div class="relative rounded-xl text-center h-32 md:h-40 lg:h-56 flex flex-col items-center justify-center font-medium text-white transition-transform duration-300 transform hover:scale-105 hover:-translate-y-2 border-b-8 border-[#28267B] shadow-lg"
             style="background: linear-gradient(135deg, #615ED9 0%, #B076D9 50%, #D867A8 100%);">
          <span class="text-6xl lg:text-8xl">🏆</span>
          <p class="mt-2 text-2xl md:text-3xl font-baloo font-extrabold">
            Earn points & badges
          </p>
        </div>

        <!-- Card 3 -->
        <div class="relative rounded-xl text-center h-32 md:h-40 lg:h-56 flex flex-col items-center justify-center font-medium text-white transition-transform duration-300 transform hover:scale-105 hover:-translate-y-2 border-b-8 border-[#275D97] shadow-lg"
             style="background: linear-gradient(135deg, #9154DE 0%, #5596DD 50%, #43C479 100%);">
          <span class="text-6xl lg:text-8xl">📈</span>
          <p class="mt-2 text-2xl md:text-3xl font-baloo font-extrabold">
            Track your progress
          </p>
    
        </div>
      </div>

      <!-- Start Button -->
      <div class="px-8 py-6 flex justify-center relative">
        <a href="{{ route('student.onboarding.avatar') }}"
          class="relative px-6 py-3 bg-gradient-to-r from-[#F6510C] to-[#F5D70B] text-white font-bold rounded-xl inline-block text-base transition-transform duration-300 hover:scale-105 border-b-8 border-[#A12115] shadow-lg"
          style="z-index:1;">
            Choose Your Avatar
        </a>
      </div>
    </div>
  </div>
</div>

@endsection