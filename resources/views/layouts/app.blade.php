<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>{{ config('app.name', 'Bamboo Assur') }}</title>

    <link rel="stylesheet" href="../../css/skilline.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <script src="https://code.iconify.design/2/2.1.0/iconify.min.js"></script>


    @filamentStyles
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>

<body class="font-sans antialiased">
    <x-banner />

    <div class="min-h-screen bg-white">
        <!-- Page Content -->

        <!-- component -->
        <div>

            <div x-data="{ sidebarOpen: false }" class="flex h-screen">
                <div :class="sidebarOpen ? 'block' : 'hidden'" @click="sidebarOpen = false"
                    class="fixed inset-0 z-20 transition-opacity bg-black opacity-50 lg:hidden"></div>

                <div :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'"
                    class="fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto transition duration-300 transform bg-gray-900 lg:translate-x-0 lg:static lg:inset-0">
                    <div class="flex items-center justify-center mt-8">
                        <div class="flex items-center">
                            <a href="{{ route('dashboard') }}">
                                <x-application-mark class="block w-auto h-9" />
                            </a>
                        </div>
                    </div>

                    <nav class="mt-10">
                        <!-- Lien Tableau de bord -->
                        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" :icone="'<span class=\'iconify text-4xl\'
                                                                                                                                                                                                                                                                                                                                                                                                                                data-icon=\'duo-icons:dashboard\' data-inline=\'false\'></span>'">
                            {{ __('Tableau de bord') }}
                        </x-nav-link>

                        <!-- Gestion articles -->
                        <x-nav-link href="{{ route('articles') }}" :active="request()->routeIs('articles')" :icone="'<span class=\'iconify text-4xl\'
                                                                                                                                                                                                                                                    data-icon=\'duo-icons:dashboard\' data-inline=\'false\'></span>'">
                            {{ __('Gestion articles') }}
                        </x-nav-link>

                        <!-- Gestion boutiques -->
                        <x-nav-link href="{{ route('boutiques') }}" :active="request()->routeIs('boutiques')" :icone="'<span class=\'iconify text-4xl\'
                                                                                                                                                                                                                            data-icon=\'duo-icons:dashboard\' data-inline=\'false\'></span>'">
                            {{ __('Gestion boutiques') }}
                        </x-nav-link>

                        <!-- Gestion categories -->
                        <x-nav-link href="{{ route('categories') }}" :active="request()->routeIs('categories')" :icone="'<span class=\'iconify text-4xl\'
                                                                                                                                                                                                    data-icon=\'duo-icons:dashboard\' data-inline=\'false\'></span>'">
                            {{ __('Gestion categories') }}
                        </x-nav-link>

                        <!-- Gestion sous-categories -->
                        <x-nav-link href="{{ route('sous-categories') }}" :active="request()->routeIs('sous-categories')" :icone="'<span class=\'iconify text-4xl\'
                                                                                                                                                                            data-icon=\'duo-icons:dashboard\' data-inline=\'false\'></span>'">
                            {{ __('Gestion sous categories') }}
                        </x-nav-link>

                        <!-- Gestion commandes -->
                        <x-nav-link href="{{ route('commandes') }}" :active="request()->routeIs('commandes')" :icone="'<span class=\'iconify text-4xl\'
                                                                                                                            data-icon=\'duo-icons:dashboard\' data-inline=\'false\'></span>'">
                            {{ __('Gestion commandes') }}
                        </x-nav-link>

                        <!-- Gestion livraisons -->
                        <x-nav-link href="{{ route('livraisons') }}" :active="request()->routeIs('livraisons')" :icone="'<span class=\'iconify text-4xl\'
                                                                                                    data-icon=\'duo-icons:dashboard\' data-inline=\'false\'></span>'">
                            {{ __('Gestion livraisons') }}
                        </x-nav-link>

                        <!-- Gestion publicites -->
                        <x-nav-link href="{{ route('publicites') }}" :active="request()->routeIs('publicites')" :icone="'<span class=\'iconify text-4xl\'
                                                    data-icon=\'duo-icons:dashboard\' data-inline=\'false\'></span>'">
                            {{ __('Gestion publicites') }}
                        </x-nav-link>

                        <!-- Gestion reclamations -->
                        <x-nav-link href="{{ route('reclamations') }}" :active="request()->routeIs('reclamations')" :icone="'<span class=\'iconify text-4xl\'
                                                                            data-icon=\'duo-icons:dashboard\' data-inline=\'false\'></span>'">
                            {{ __('Gestion reclamations') }}
                        </x-nav-link>

                        {{-- Profil --}}
                        <x-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')" :icone="'<span class=\'iconify text-4xl\'
                                                                                                                                                                                                                                                                                                                                                                                                                                data-icon=\'solar:user-bold-duotone\' data-inline=\'false\'></span>'">
                            {{ __('Profil') }}
                        </x-nav-link>

                    </nav>
                </div>
                <div class="flex flex-col flex-1 overflow-hidden">
                    <header class="flex items-center justify-between px-6 py-4 bg-white border-b-2 border-gray-100">
                        <div class="flex items-center">
                            <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </button>
                        </div>

                    </header>
                    <main class="flex-1 overflow-x-hidden overflow-y-auto">
                        <div class="space-y-5">

                            <div class="flex space-x-5">
                                <div class="w-full bg-white rounded-t-2xl container mx-auto md:py-8">
                                    @if (isset($header))
                                        <header class="">
                                            <div
                                                class="flex justify-between border-b-4 border-[#4996d1] pb-5 items-center mx-5">
                                                {{ $header }}
                                            </div>
                                        </header>
                                    @endif
                                    <div class="">
                                        {{ $slot }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>

    @stack('modals')

    @livewire('notifications')
    @livewireScripts
    @filamentScripts
    @vite('resources/js/app.js')
    <!-- Script JavaScript -->
    <script>
        let idleTime = 0;
        const maxIdleTime = 120 * 60 * 1000; // 120 minutes

        function resetIdleTime() {
            idleTime = 0;
        }

        document.onmousemove = resetIdleTime;
        document.onkeypress = resetIdleTime;

        setInterval(() => {
            idleTime += 1000;
            if (idleTime >= maxIdleTime) {
                alert('Votre session a expiré. Vous allez être redirigé.');
                window.location.href = '/login';
            }
        }, 1000);
    </script>
</body>

</html>
