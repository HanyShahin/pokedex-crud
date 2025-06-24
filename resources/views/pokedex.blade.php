<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pokédex - Kanto</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/pokedex.css') }}">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-slate-900 text-white pb-12">

    <header class="container mx-auto px-4 py-8 flex justify-between items-center">
        <div class="text-left">
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-yellow-400">
                Pokédex - Kanto
            </h1>
            <p class="text-slate-400 mt-2">Bem-vindo, {{ Auth::user()->name }}!</p>
            <div class="mt-4 max-w-sm">
                <div class="flex justify-between items-center mb-1">
                    <span class="text-base font-medium text-slate-300">Progresso da Captura</span>
                    <span id="progress-text" class="text-sm font-medium text-slate-300">0 / 151</span>
                </div>
                <div class="w-full bg-slate-700 rounded-full h-4 shadow-inner">
                    <div id="progress-bar-fill" class="bg-gradient-to-r from-green-400 to-blue-500 h-4 rounded-full transition-all duration-500 ease-out" style="width: 0%"></div>
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg">
                <i class="fas fa-sign-out-alt mr-2"></i>Sair
            </button>
        </form>
    </header>

    <main class="container mx-auto px-4">
        <div class="text-center mb-8">
            <button id="add-pokemon-btn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg">
                <i class="fas fa-plus mr-2"></i> Registrar Captura
            </button>
        </div>

        <div class="mb-8 p-4 bg-slate-800 rounded-lg shadow-md">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div class="md:col-span-1">
                    <label for="search-input" class="block text-slate-400 mb-2 font-semibold">Buscar por Nome:</label>
                    <input type="text" id="search-input" placeholder="Ex: Pikachu" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2">

                    <div class="flex items-center mt-4">
                    <input id="show-caught-only-checkbox" type="checkbox" class="h-4 w-4 rounded border-slate-500 bg-slate-600 text-indigo-600 focus:ring-indigo-500">
                    <label for="show-caught-only-checkbox" class="ml-2 text-sm font-medium text-slate-300">Mostrar apenas capturados</label>
                </div>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-slate-400 mb-2 font-semibold">Filtrar por Tipo:</label>
                    <div id="type-filter-buttons" class="flex flex-wrap gap-2">
                        <button class="type-filter-btn active px-3 py-1.5 rounded-full text-sm font-semibold bg-indigo-600 text-white" data-type="all">Todos</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-grass" data-type="grass">Grass</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-fire" data-type="fire">Fire</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-water" data-type="water">Water</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-bug" data-type="bug">Bug</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-normal" data-type="normal">Normal</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-poison" data-type="poison">Poison</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-electric" data-type="electric">Electric</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-ground" data-type="ground">Ground</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-fairy" data-type="fairy">Fairy</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-fighting" data-type="fighting">Fighting</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-psychic" data-type="psychic">Psychic</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-rock" data-type="rock">Rock</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-ghost" data-type="ghost">Ghost</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-ice" data-type="ice">Ice</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-dragon" data-type="dragon">Dragon</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-flying" data-type="flying">Flying</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-steel" data-type="steel">Steel</button>
                    </div>
                </div>
                
            </div>
        </div>
        
        <div id="pokemon-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6"></div>
        <div id="loader" class="flex justify-center items-center py-16"><div class="pokeball-loader"></div></div>
    </main>

    <div id="pokemon-modal" class="fixed inset-0 bg-black bg-opacity-75 flex justify-center items-center p-4 z-50 hidden">
        <div class="bg-slate-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-full overflow-y-auto">
            <form id="pokemon-form" class="p-8">
                <input type="hidden" id="pokemon-id">
                <h2 id="modal-title" class="text-3xl font-bold mb-6 text-center">Registrar Nova Captura</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="relative mb-4">
                            <label for="name" class="block text-slate-400 mb-1">Nome</label>
                            <input type="text" id="name" name="name" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2" required autocomplete="off">
                            <div id="autocomplete-results" class="absolute z-10 w-full bg-slate-600 border border-slate-500 rounded-md shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
                        </div>
                        <div class="mb-4">
                            <label for="number" class="block text-slate-400 mb-1">Número</label>
                            <input type="number" id="number" name="number" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2" required>
                        </div>
                        <div class="mb-4">
                            <label for="type1" class="block text-slate-400 mb-1">Tipo 1</label>
                            <input type="text" id="type1" name="type1" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2" required>
                        </div>
                        <div class="mb-4">
                            <label for="type2" class="block text-slate-400 mb-1">Tipo 2 (Opcional)</label>
                            <input type="text" id="type2" name="type2" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2">
                        </div>
                    </div>
                    <div>
                        <div class="mb-4">
                            <label for="image_url" class="block text-slate-400 mb-1">URL da Imagem</label>
                            <input type="url" id="image_url" name="image_url" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2" required>
                        </div>
                        <div class="mb-4">
                            <label for="height" class="block text-slate-400 mb-1">Altura (m)</label>
                            <input type="number" step="0.1" id="height" name="height" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2" required>
                        </div>
                        <div class="mb-4">
                            <label for="weight" class="block text-slate-400 mb-1">Peso (kg)</label>
                            <input type="number" step="0.1" id="weight" name="weight" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2" required>
                        </div>
                        <div class="mb-4">
                            <label for="description" class="block text-slate-400 mb-1">Descrição</label>
                            <textarea id="description" name="description" rows="3" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex justify-end space-x-4">
                    <button type="button" id="cancel-btn" class="bg-slate-600 hover:bg-slate-700 text-white font-bold py-2 px-6 rounded-lg">Cancelar</button>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="toast" class="fixed bottom-5 right-5 bg-green-500 text-white py-3 px-6 rounded-lg shadow-lg transform translate-x-[120%]">
        <p id="toast-message"></p>
    </div>

    <div id="pokemon-details-modal" class="fixed inset-0 bg-black bg-opacity-80 flex justify-center items-center p-4 z-50 hidden">
        <div id="details-modal-content" class="bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto relative p-6 text-white">
            <button id="details-close-btn" class="absolute top-4 right-4 text-slate-400 hover:text-white text-3xl z-10">&times;</button>
            <div class="text-center mb-4">
                <h2 id="details-name" class="text-4xl font-bold capitalize"></h2>
                <span id="details-number" class="text-xl text-slate-400 font-bold"></span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <div class="flex flex-col items-center">
                    <img id="details-image" src="" alt="Pokemon" class="w-48 h-48 object-contain">
                    <div id="details-types" class="flex space-x-2 mt-4"></div>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-2 text-center text-slate-300">Base Stats</h3>
                    <canvas id="stats-chart"></canvas>
                </div>
            </div>
            <div class="mt-6">
                <div class="bg-slate-700 p-4 rounded-lg mb-4">
                    <h3 class="font-bold text-md mb-1 text-slate-300">Descrição do Treinador</h3>
                    <p id="details-description" class="text-slate-200 text-sm"></p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-slate-700 p-4 rounded-lg">
                        <h3 class="font-bold text-md mb-2 text-slate-300">Físico</h3>
                        <p id="details-physical" class="text-slate-200"></p>
                    </div>
                    <div class="bg-slate-700 p-4 rounded-lg">
                        <h3 class="font-bold text-md mb-2 text-slate-300">Habilidades</h3>
                        <div id="details-abilities" class="flex flex-wrap gap-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/pokedex.js') }}" defer></script>

</body>
</html>