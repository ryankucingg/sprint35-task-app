@props(['sessionActive', 'link', 'name', 'icon', 'newtab' => null, 'badge' => null, 'badgeColor' => 'badge-error'])

@php
    $badgeCount = is_null($badge) ? 0 : (int) $badge;
    $badgeLabel = $badgeCount > 99 ? '99+' : $badgeCount;
@endphp

{{-- `nav-link` / `nav-label` dipakai CSS mode rel ikon: saat sidebar menyempit,
     labelnya disembunyikan dan ikonnya ditengahkan. `data-tip` menggantikan
     label yang hilang lewat tooltip (elemennya dirender di luar sidebar dan
     diposisikan JS — lihat admin_layout), `aria-label` menjaga menu tetap
     terbaca pembaca layar saat labelnya disembunyikan, dan `data-active`
     dipakai untuk menggulirkan sidebar ke menu yang sedang dibuka. --}}
<li>
    <a href="{{ $link }}"
       data-tip="{{ $name }}"
       aria-label="{{ $name }}"
       @if(session('active') == $sessionActive) data-active @endif
       class="nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200
           @if(session('active') == $sessionActive)
               bg-primary/10 text-primary border-l-3 border-primary
           @else
               text-base-content/70 hover:bg-base-200 hover:text-base-content
           @endif"
       @if(isset($newtab)) target="_blank" @endif>
        <span class="nav-icon relative flex items-center shrink-0">
            <span class="material-symbols-outlined text-lg">{{ $icon }}</span>
            @if($badgeCount > 0)
                <span class="nav-count nav-count--dot {{ $badgeColor }}" aria-hidden="true">{{ $badgeLabel }}</span>
            @endif
        </span>
        <span class="nav-label flex-1">{{ $name }}</span>
        @if($badgeCount > 0)
            <span class="nav-count {{ $badgeColor }}">{{ $badgeLabel }}</span>
            <span class="sr-only">{{ $badgeCount }} menunggu tindakan</span>
        @endif
    </a>
</li>
