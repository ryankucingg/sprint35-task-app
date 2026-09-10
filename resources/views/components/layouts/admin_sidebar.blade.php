<div class="app-sidebar z-50 fixed top-0 left-0 h-screen bg-base-100 shadow-2xl">
    <nav role="navigation" class="sidebar-nav p-6 h-screen overflow-y-auto overflow-x-hidden">

        <div class="sidebar-brand">
            <a class="brand-link" href="{{ route('admin.dashboard') }}"
               title="{{ config('site.short_name') }}">
                <span class="brand-mark">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('site.name') }}" />
                </span>
                <span class="brand-text brand-copy">
                    <span class="brand-title">{{ config('site.short_name') }}</span>
                    <span class="brand-sub">{{ config('site.subtitle') }}</span>
                </span>
            </a>
        </div>

        <div class="menu-group mt-8 -mx-4 relative">
            <span class="menu-title text-gray-500">Menu</span>
            <ul class="nav-list menu space-y-4 px-4 mt-4 w-full">
                <x-ui.navigation-item session-active="admin-dashboard" link="{{ route('admin.dashboard') }}" name="Dashboard" icon="dashboard"/>
                @unless(auth()->user()->isAdmin())
                    @include('livewire.task.task_nav')
                @endunless
            </ul>
        </div>

        @if(auth()->user()->isAdmin())
            <div class="divider"></div>

            <div class="menu-group -mx-4">
                <span class="menu-title text-gray-500">Administrator</span>
                <ul class="nav-list menu space-y-4 mb-12 px-4 mt-4 w-full">
                    @include('livewire.admin.alltask.alltask_nav')
                    @include('livewire.admin.category.category_nav')
                    @include('livewire.admin.user.user_nav')
                    @include('livewire.admin.setting.setting_nav')
                </ul>
            </div>
        @endif

    </nav>
</div>
