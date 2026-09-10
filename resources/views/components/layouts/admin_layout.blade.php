<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-x.png') }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @stack('styles')
    @stack('title')

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @include('components.ui.theme-switch-js')

    <script>
        if (sessionStorage.getItem('side-expand') === 'close') {
            document.documentElement.classList.add('sidebar-mini');
        }
    </script>
</head>
<body class="font-sans">
<div class="h-screen grid-flow-row">

    <div id="dr" class="drawer z-10 lg:drawer-open">
        <input id="my-drawer" type="checkbox" class="drawer-toggle"/>
        <div class="drawer-content">
            <div id="d_top" class="navbar app-navbar fixed z-20 bg-primary text-primary-content h-16 min-h-0 px-2 sm:px-4 shadow-md">
                <div class="flex-none">
                    <label for="my-drawer" class="btn btn-ghost btn-sm btn-square lg:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                    </label>
                    <label onclick="setDrawer()" class="btn btn-ghost btn-sm btn-square hidden lg:flex ml-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                    </label>
                </div>

                <div class="flex-1 min-w-0 ml-2 sm:ml-4">
                    <div class="flex items-center gap-2 truncate">
                        <span class="badge badge-sm bg-white/15 border-0 text-primary-content uppercase font-bold text-[10px] sm:text-xs tracking-wide shrink-0">
                            {{ str_replace('-', ' ', auth()->user()->role) }}
                        </span>
                        <span class="text-sm sm:text-base font-medium truncate">{{ auth()->user()->name }}</span>
                    </div>
                </div>

                <div class="flex-none flex items-center gap-1">
                    <x-ui.theme-switch-button trigger-class="btn btn-ghost btn-sm btn-square" />

                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-sm gap-2 px-2 sm:px-3">
                            <div class="w-8 h-8 rounded-full overflow-hidden bg-white/15 flex items-center justify-center">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="" style="width:100%;height:100%;object-fit:cover;" />
                                @else
                                    <span class="text-sm font-bold text-primary-content">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            <span class="hidden md:inline text-sm font-medium max-w-24 truncate">{{ auth()->user()->name }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-60 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div tabindex="0" class="dropdown-content bg-base-100 rounded-xl z-[1] mt-2 w-60 shadow-lg border border-base-content/10 overflow-hidden">
                            <div class="p-3 bg-base-200/50 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full overflow-hidden shrink-0 bg-primary/10 flex items-center justify-center">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="" style="width:100%;height:100%;object-fit:cover;" />
                                    @else
                                        <span class="text-lg font-bold text-primary">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-base-content truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-base-content/50 truncate mt-0.5">{{ auth()->user()->email }}</p>
                                    <span class="badge badge-xs badge-primary text-white uppercase mt-1">{{ str_replace('-', ' ', auth()->user()->role) }}</span>
                                </div>
                            </div>
                            <div class="p-1.5">
                                <a href="{{ route('admin.profile') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-base-content hover:bg-base-200 transition-colors">
                                    <span class="material-icons text-lg text-base-content/50">person</span>
                                    Edit Profil
                                </a>
                                <a href="#" id="logout-btn" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-error hover:bg-error/10 transition-colors">
                                    <span class="material-icons text-lg">logout</span>
                                    Keluar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="d_content" class="pt-16 app-main flex flex-col min-h-screen">
                <div class="bg-neutral flex-1">
                    {{ $slot }}
                </div>
                <footer class="footer bg-neutral text-neutral-content justify-center lg:justify-end p-4">
                    <aside class="grid-flow-col items-center lg:mr-4">
                        <p>Copyright &copy; {{ date('Y') }} - {{ config('site.name') }}</p>
                    </aside>
                </footer>
            </div>
        </div>

        <div class="drawer-side z-50">
            <label for="my-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            @include('components.layouts.admin_sidebar')
        </div>
    </div>

    <div id="sidebar-tip" class="sidebar-tip" role="tooltip" aria-hidden="true"></div>

    @include('mrcatz::components.ui.notification')

</div>
</body>
</html>

<script>
    function setDrawer() {
        var mini = document.documentElement.classList.toggle('sidebar-mini');
        sessionStorage.setItem('side-expand', mini ? 'close' : 'open');
        hideSidebarTip();
        focusActiveMenu();
    }

    function focusActiveMenu() {
        var nav = document.querySelector('.app-sidebar .sidebar-nav');
        var active = nav && nav.querySelector('.nav-link[data-active]');
        if (!active) return;

        var navBox = nav.getBoundingClientRect();
        var itemBox = active.getBoundingClientRect();

        if (itemBox.top >= navBox.top && itemBox.bottom <= navBox.bottom) return;

        nav.scrollTop += (itemBox.top - navBox.top) - (nav.clientHeight - itemBox.height) / 2;
    }

    function showSidebarTip(link) {
        if (!document.documentElement.classList.contains('sidebar-mini')) return;
        if (!window.matchMedia('(min-width: 1024px)').matches) return;

        var tip = document.getElementById('sidebar-tip');
        var box = link.getBoundingClientRect();

        tip.textContent = link.dataset.tip || '';
        tip.style.left = (box.right + 12) + 'px';
        tip.style.top = (box.top + box.height / 2) + 'px';
        tip.classList.add('is-visible');
    }

    function hideSidebarTip() {
        document.getElementById('sidebar-tip')?.classList.remove('is-visible');
    }

    document.addEventListener('DOMContentLoaded', function () {
        focusActiveMenu();

        var sidebar = document.querySelector('.app-sidebar');
        if (! sidebar) return;

        sidebar.addEventListener('mouseover', function (e) {
            var link = e.target.closest('.nav-link');
            if (link) showSidebarTip(link);
        });
        sidebar.addEventListener('mouseout', function (e) {
            if (e.target.closest('.nav-link')) hideSidebarTip();
        });
        sidebar.addEventListener('focusin', function (e) {
            var link = e.target.closest('.nav-link');
            if (link) showSidebarTip(link);
        });
        sidebar.addEventListener('focusout', hideSidebarTip);
        sidebar.querySelector('.sidebar-nav')?.addEventListener('scroll', hideSidebarTip);
        window.addEventListener('resize', hideSidebarTip);
    });

</script>

@stack('scripts')

@auth
    <form action="{{ route('logout') }}" method="POST" id="logout-form">@csrf</form>
    <script>
        document.getElementById("logout-btn").addEventListener('click', function () {
            sessionStorage.removeItem('side-expand');
            document.getElementById("logout-form").submit();
        });
    </script>
@endauth
