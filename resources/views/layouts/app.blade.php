<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>{{ config('app.name', 'Ewuang') }}</title>

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
                                                                                                                                                                                                                                data-icon=\'material-symbols:shop\' data-inline=\'false\'></span>'">
                            {{ __('Articles') }}
                        </x-nav-link>

                        <!-- Gestion boutiques -->
                        <x-nav-link href="{{ route('boutiques') }}" :active="request()->routeIs('boutiques')" :icone="'<span class=\'iconify text-4xl\'
                                                                                                                                                                                                                                data-icon=\'solar:shop-bold-duotone\' data-inline=\'false\'></span>'">
                            {{ __('Boutiques') }}
                        </x-nav-link>

                        <!-- Gestion categories -->




                        <!-- Gestion commandes -->
                        <x-nav-link href="{{ route('commandes') }}" :active="request()->routeIs('commandes')" :icone="'<span class=\'iconify text-4xl\'
                                                                                                                                                                                                                                data-icon=\'material-symbols:shopping-cart\' data-inline=\'false\'></span>'">
                            {{ __('Commandes') }}
                        </x-nav-link>

                        <!-- Gestion livraisons -->
                        <x-nav-link href="{{ route('livraisons') }}" :active="request()->routeIs('livraisons')" :icone="'<span class=\'iconify text-4xl\'
                                                                                                                                                                                                                                data-icon=\'material-symbols:local-shipping\' data-inline=\'false\'></span>'">
                            {{ __('Livraisons') }}
                        </x-nav-link>

                        <!-- Gestion publicites -->
                        <x-nav-link href="{{ route('publicites') }}" :active="request()->routeIs('publicites')" :icone="'<span class=\'iconify text-4xl\'
                                                                                                                                                                                                                                data-icon=\'material-symbols:campaign\' data-inline=\'false\'></span>'">
                            {{ __('Publicites') }}
                        </x-nav-link>

                        <!-- Gestion reclamations -->
                        <x-nav-link href="{{ route('reclamations') }}" :active="request()->routeIs('reclamations')" :icone="'<span class=\'iconify text-4xl\'
                                                                                                                                                                                                                                data-icon=\'material-symbols:error\' data-inline=\'false\'></span>'">
                            {{ __('Réclamations') }}
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

                        <div class="flex items-center">
                            {{-- Notification --}}
                            {{-- <div x-data="{ notificationOpen: false }" class="relative">
                                <button @click="notificationOpen = ! notificationOpen"
                                    class="flex mx-4 text-gray-600 focus:outline-none">
                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M15 17H20L18.5951 15.5951C18.2141 15.2141 18 14.6973 18 14.1585V11C18 8.38757 16.3304 6.16509 14 5.34142V5C14 3.89543 13.1046 3 12 3C10.8954 3 10 3.89543 10 5V5.34142C7.66962 6.16509 6 8.38757 6 11V14.1585C6 14.6973 5.78595 15.2141 5.40493 15.5951L4 17H9M15 17V18C15 19.6569 13.6569 21 12 21C10.3431 21 9 19.6569 9 18V17M15 17H9"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                        </path>
                                    </svg>
                                </button>

                                <div x-show="notificationOpen" @click="notificationOpen = false"
                                    class="fixed inset-0 z-10 w-full h-full" style="display: none;"></div>

                                <div x-show="notificationOpen"
                                    class="absolute right-0 z-10 mt-2 overflow-hidden bg-white rounded-lg shadow-xl w-80"
                                    style="width: 20rem; display: none;">
                                    <a href="#"
                                        class="flex items-center px-4 py-3 -mx-2 text-gray-600 hover:text-white hover:bg-indigo-600">
                                        <img class="object-cover w-8 h-8 mx-1 rounded-full"
                                            src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=334&amp;q=80"
                                            alt="avatar">
                                        <p class="mx-2 text-sm">
                                            <span class="font-bold" href="#">Sara Salah</span> replied on the
                                            <span class="font-bold text-indigo-400" href="#">Upload Image</span>
                                            artical . 2m
                                        </p>
                                    </a>
                                    <a href="#"
                                        class="flex items-center px-4 py-3 -mx-2 text-gray-600 hover:text-white hover:bg-indigo-600">
                                        <img class="object-cover w-8 h-8 mx-1 rounded-full"
                                            src="https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=634&amp;q=80"
                                            alt="avatar">
                                        <p class="mx-2 text-sm">
                                            <span class="font-bold" href="#">Slick Net</span> start following
                                            you . 45m
                                        </p>
                                    </a>
                                    <a href="#"
                                        class="flex items-center px-4 py-3 -mx-2 text-gray-600 hover:text-white hover:bg-indigo-600">
                                        <img class="object-cover w-8 h-8 mx-1 rounded-full"
                                            src="https://images.unsplash.com/photo-1450297350677-623de575f31c?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=334&amp;q=80"
                                            alt="avatar">
                                        <p class="mx-2 text-sm">
                                            <span class="font-bold" href="#">Jane Doe</span> Like Your reply on
                                            <span class="font-bold text-indigo-400" href="#">Test with
                                                TDD</span> artical . 1h
                                        </p>
                                    </a>
                                    <a href="#"
                                        class="flex items-center px-4 py-3 -mx-2 text-gray-600 hover:text-white hover:bg-indigo-600">
                                        <img class="object-cover w-8 h-8 mx-1 rounded-full"
                                            src="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=398&amp;q=80"
                                            alt="avatar">
                                        <p class="mx-2 text-sm">
                                            <span class="font-bold" href="#">Abigail Bennett</span> start
                                            following you . 3h
                                        </p>
                                    </a>
                                </div>
                            </div> --}}

                            <div x-data="{ dropdownOpen: false }" class="relative">
                                <button @click="dropdownOpen = ! dropdownOpen"
                                    class="relative block w-8 h-8 overflow-hidden rounded-full shadow focus:outline-none">
                                    <img class="object-cover w-8 h-8 rounded-full"
                                        src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                    <span class="text-gray-600">{{ Auth::user()->name }}</span>
                                </button>

                                <div x-show="dropdownOpen" @click="dropdownOpen = false"
                                    class="fixed inset-0 z-10 w-full h-full" style="display: none;"></div>

                                <div x-show="dropdownOpen"
                                    class="absolute right-0 z-10 w-48 mt-2 overflow-hidden bg-white rounded-md shadow-xl"
                                    style="display: none;">
                                    <!-- Account Management -->

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-900 hover:text-white w-full text-left">
                                            {{ __('Log Out') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
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
