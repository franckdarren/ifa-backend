<div class="grid grid-cols-1 md:grid-cols-5 gap-5 justify-center" wire:poll.5s>
    <div
        class="block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 text-center">
        <h5 class="mb-2 text-5xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $nbreClient }}</h5>
        <p class="font-normal text-gray-700 dark:text-white">Nombre de clients</p>
    </div>
    <div
        class="block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 text-center">
        <h5 class="mb-2 text-5xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $nbreBoutique }}
        </h5>
        <p class="font-normal text-gray-700 dark:text-white">Nombre de boutiques</p>
    </div>
    <div
        class="block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 text-center">
        <h5 class="mb-2 text-5xl font-bold tracking-tight text-gray-900 dark:text-white">
            {{ $nbreLivraison }}
        </h5>
        <p class="font-normal text-gray-700 dark:text-white">Nombre de livraisons</p>
    </div>
    <div
        class="block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 text-center">
        <h5 class="mb-2 text-5xl font-bold tracking-tight text-gray-900 dark:text-white">
            {{ $nbreArticle }}
        </h5>
        <p class="font-normal text-gray-700 dark:text-white">Nombre d'articles</p>
    </div>
    <div
        class="block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 text-center">
        <h5 class="mb-2 text-5xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $nbreCommande }}
        </h5>
        <p class="font-normal text-gray-700 dark:text-white ">Nombre de commandes</p>
    </div>
</div>
