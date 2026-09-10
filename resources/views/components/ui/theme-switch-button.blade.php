@props([
    'triggerClass' => 'p-2 rounded-full text-white hover:text-brand-red-soft hover:bg-white/10 transition-all',
    'wrapperClass' => '',
])

<div class="theme-dropdown-wrapper relative {{ $wrapperClass }}">
    <button onclick="toggleThemeDropdown(this)" class="{{ $triggerClass }}" aria-label="Pilih tema">
        <span class="theme-trigger-icon">
            <svg data-icon-for="mrcatz-light" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <svg data-icon-for="mrcatz-dark" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
            <svg data-icon-for="system" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </span>
    </button>
    <div class="theme-dropdown-menu hidden absolute right-0 top-full mt-2 bg-base-100 shadow-xl rounded-lg py-1.5 min-w-40 z-50 border border-base-content/10">
        <button data-theme-value="mrcatz-light" onclick="setTheme('mrcatz-light')" class="flex items-center gap-3 w-full px-4 py-2 text-sm text-base-content hover:bg-base-200 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Light
        </button>
        <button data-theme-value="mrcatz-dark" onclick="setTheme('mrcatz-dark')" class="flex items-center gap-3 w-full px-4 py-2 text-sm text-base-content hover:bg-base-200 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
            Dark
        </button>
        <button data-theme-value="system" onclick="setTheme('system')" class="flex items-center gap-3 w-full px-4 py-2 text-sm text-base-content hover:bg-base-200 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            System
        </button>
    </div>
</div>
