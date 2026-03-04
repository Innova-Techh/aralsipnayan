<!-- Our Team Section -->
<section id="ourteam" class="font-baloo py-16 md:py-20 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-10">
      <h2 class="text-3xl md:text-5xl font-bold text-blue-600">Our <span class="text-yellow-400">Team</span></h2>
      <p class="text-base md:text-lg text-gray-600 mt-3">Researchers, advisers, and panelists who guided AralSipnayan.
      </p>
      <div class="w-24 h-1 bg-yellow-300 mx-auto rounded-full mt-5"></div>
    </div>

    <!-- Researchers -->
    <div class="mb-12">
      <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-6">Researchers</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
        @php
          $researchers = [
            ["role" => "Lead and Full Stack Developer", "name" => "Eduardo II Buscato", "id" => "0001", "image" => "charac-2.png"],
            ["role" => "Quality Assurance, UI/UX and Business Analyst", "name" => "Krissa Mae Beringuel", "id" => "0002", "image" => "charac-3.png"],
            ["role" => "UI/UX and Frontend Developer", "name" => "Joshua Fernandez", "id" => "0003", "image" => "charac-4.png"],
            ["role" => "Quality Assurance and Full-Stack Developer", "name" => "Sean Russel Sicat", "id" => "0004", "image" => "charac-5.png"],
          ];
        @endphp
        @foreach ($researchers as $r)
          <div class="rounded-2xl border-2 border-slate-900 shadow-md overflow-hidden">
            <div class="flex h-full">
              <div class="w-1/2 bg-yellow-300 p-4 flex flex-col items-center justify-center gap-2">
                <img src="{{ asset("images/homepage/" . $r["image"]) }}" alt="{{ $r["name"] }}"
                  class="w-16 h-20 sm:w-20 sm:h-24 md:w-24 md:h-28 lg:w-28 lg:h-32 xl:w-32 xl:h-36 rounded-lg object-cover bg-yellow-200"
                  onerror="this.onerror=null;this.src=\" {{ asset("images/our-team/placeholder.png") }}\";" />
                <div class="text-xs text-slate-900 text-center leading-tight">{{ $r["role"] }}</div>
              </div>
              <div class="w-1/2 bg-blue-600 p-4 flex flex-col justify-between">
                <div>
                  <div class="text-[10px] uppercase tracking-widest text-white/70 font-semibold flex justify-between">
                    <span>ID</span>
                    <span>#{{ $r["id"] }}</span>
                  </div>
                  <p class="text-base sm:text-lg font-bold text-white mt-1 leading-tight">{{ $r["name"] }}</p>
                  <div class="mt-3 h-1.5 w-16 bg-yellow-300 rounded-full"></div>
                </div>
                <div class="text-xs text-white/80 mt-3">2025-2026</div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    <!-- Advisers -->
    <div class="mb-12">
      <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-6">Advisers</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
          $advisers = [
            ["role" => "Research Adviser", "name" => "Prof. Mary Ellaine Cervantes", "id" => "1001", "image" => "charac-6.png"],
            ["role" => "Technical Adviser", "name" => "Prof. Lester Glover Diampoc", "id" => "1002", "image" => "charac-7.png"],
            ["role" => "DEPED Adviser", "name" => "Prof. Michael Lee", "id" => "1003", "image" => "charac-8.png"],
          ];
        @endphp
        @foreach ($advisers as $a)
          <div class="rounded-2xl border-2 border-slate-900 shadow-md overflow-hidden">
            <div class="flex h-full">
              <div class="w-1/2 bg-yellow-300 p-4 flex flex-col items-center justify-center gap-2">
                <img src="{{ asset("images/homepage/" . $a["image"]) }}" alt="{{ $a["name"] }}"
                  class="w-16 h-20 sm:w-20 sm:h-24 md:w-24 md:h-28 lg:w-28 lg:h-32 xl:w-32 xl:h-36 rounded-lg object-cover bg-yellow-200"
                  onerror="this.onerror=null;this.src=\" {{ asset("images/our-team/placeholder.png") }}\";" />
                <div class="text-xs text-slate-900 text-center leading-tight">{{ $a["role"] }}</div>
              </div>
              <div class="w-1/2 bg-blue-600 p-4 flex flex-col justify-between">
                <div>
                  <div class="text-[10px] uppercase tracking-widest text-white/70 font-semibold flex justify-between">
                    <span>ID</span>
                    <span>#{{ $a["id"] }}</span>
                  </div>
                  <p class="text-base sm:text-lg font-bold text-white mt-1 leading-tight">{{ $a["name"] }}</p>
                  <div class="mt-3 h-1.5 w-16 bg-yellow-300 rounded-full"></div>
                </div>
                <div class="text-xs text-white/80 mt-3">2025-2026</div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    <!-- Panelists -->
    <div>
      <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-6">Panelists</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
          $panelists = [
            ["role" => "Panelist", "name" => "Dr. Maria Santos", "id" => "2001", "image" => "charac-9.png"],
            ["role" => "Panelist", "name" => "Dr. Juan Dela Cruz", "id" => "2002", "image" => "charac-10.png"],
            ["role" => "Panelist", "name" => "Dr. Ana Reyes", "id" => "2003", "image" => "charac-11.png"],
          ];
        @endphp
        @foreach ($panelists as $p)
          <div class="rounded-2xl border-2 border-slate-900 shadow-md overflow-hidden">
            <div class="flex h-full">
              <div class="w-1/2 bg-yellow-300 p-4 flex flex-col items-center justify-center gap-2">
                <img src="{{ asset("images/homepage/" . $p["image"]) }}" alt="{{ $p["name"] }}"
                  class="w-16 h-20 sm:w-20 sm:h-24 md:w-24 md:h-28 lg:w-28 lg:h-32 xl:w-32 xl:h-36 rounded-lg object-cover bg-yellow-200"
                  onerror="this.onerror=null;this.src=\" {{ asset("images/our-team/placeholder.png") }}\";" />
                <div class="text-xs text-slate-900 text-center leading-tight">{{ $p["role"] }}</div>
              </div>
              <div class="w-1/2 bg-blue-600 p-4 flex flex-col justify-between">
                <div>
                  <div class="text-[10px] uppercase tracking-widest text-white/70 font-semibold flex justify-between">
                    <span>ID</span>
                    <span>#{{ $p["id"] }}</span>
                  </div>
                  <p class="text-base sm:text-lg font-bold text-white mt-1 leading-tight">{{ $p["name"] }}</p>
                  <div class="mt-3 h-1.5 w-16 bg-yellow-300 rounded-full"></div>
                </div>
                <div class="text-xs text-white/80 mt-3">2025-2026</div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</section>