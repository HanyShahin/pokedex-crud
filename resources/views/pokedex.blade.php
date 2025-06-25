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
<body class="panel bg-slate-900 text-white pb-12">

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

        <div class="mb-8 p-6 bg-slate-800 rounded-lg shadow-md">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex-grow">
            <label for="search-input" class="block text-slate-400 mb-2 font-semibold">Buscar por Nome:</label>
            <input type="text" id="search-input" placeholder="Ex: Pikachu" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 text-white">
        </div>
        <div class="flex-shrink-0 md:pt-8"> <div class="flex items-center">
                <input id="show-caught-only-checkbox" type="checkbox" class="h-4 w-4 rounded border-slate-500 bg-slate-600 text-indigo-600 focus:ring-indigo-500">
                <label for="show-caught-only-checkbox" class="ml-2 text-sm font-medium text-slate-300">Mostrar apenas registrados</label>
            </div>
        </div>
    </div>

    <hr class="my-6 border-slate-700">

    <div>
        <label class="block text-slate-400 mb-4 font-semibold text-center">Filtrar por Tipo:</label>
        <div id="type-filter-buttons" class="flex flex-wrap gap-x-6 gap-y-4 justify-center">
        
            <button class="type-filter-btn flex flex-col items-center gap-y-1 active" data-type="all" title="Todos">
                <i class="fas fa-globe fa-2x text-slate-400"></i>
                <span class="text-xs font-semibold text-slate-400 uppercase mt-1">Todos</span>
            </button>
            
            <button class="type-filter-btn flex flex-col items-center gap-y-1" data-type="grass" title="Grass">
                <img src="/images/icones/grass.svg" alt="Grass" class="w-10 h-10">
                <span class="text-xs font-semibold text-slate-400 uppercase">Grass</span>
            </button>
            <button class="type-filter-btn flex flex-col items-center gap-y-1" data-type="fire" title="Fire">
                <img src="/images/icones/fire.svg" alt="Fire" class="w-10 h-10">
                <span class="text-xs font-semibold text-slate-400 uppercase">Fire</span>
            </button>
            <button class="type-filter-btn flex flex-col items-center gap-y-1" data-type="water" title="Water">
                <img src="/images/icones/water.svg" alt="Water" class="w-10 h-10">
                <span class="text-xs font-semibold text-slate-400 uppercase">Water</span>
            </button>
            <button class="type-filter-btn flex flex-col items-center gap-y-1" data-type="bug" title="Bug">
                <img src="/images/icones/bug.svg" alt="Bug" class="w-10 h-10">
                <span class="text-xs font-semibold text-slate-400 uppercase">Bug</span>
            </button>
            <button class="type-filter-btn flex flex-col items-center gap-y-1" data-type="normal" title="Normal">
                <img src="/images/icones/normal.svg" alt="Normal" class="w-10 h-10">
                <span class="text-xs font-semibold text-slate-400 uppercase">Normal</span>
            </button>
            <button class="type-filter-btn flex flex-col items-center gap-y-1" data-type="poison" title="Poison">
                <img src="/images/icones/poison.svg" alt="Poison" class="w-10 h-10">
                <span class="text-xs font-semibold text-slate-400 uppercase">Poison</span>
            </button>
            <button class="type-filter-btn flex flex-col items-center gap-y-1" data-type="electric" title="Electric">
                <img src="/images/icones/electric.svg" alt="Electric" class="w-10 h-10">
                <span class="text-xs font-semibold text-slate-400 uppercase">Electric</span>
            </button>
            <button class="type-filter-btn flex flex-col items-center gap-y-1" data-type="ground" title="Ground">
                <img src="/images/icones/ground.svg" alt="Ground" class="w-10 h-10">
                <span class="text-xs font-semibold text-slate-400 uppercase">Ground</span>
            </button>
            <button class="type-filter-btn flex flex-col items-center gap-y-1" data-type="fairy" title="Fairy">
                <img src="/images/icones/fairy.svg" alt="Fairy" class="w-10 h-10">
                <span class="text-xs font-semibold text-slate-400 uppercase">Fairy</span>
            </button>
            <button class="type-filter-btn flex flex-col items-center gap-y-1" data-type="fighting" title="Fighting">
                <img src="/images/icones/fighting.svg" alt="Fighting" class="w-10 h-10">
                <span class="text-xs font-semibold text-slate-400 uppercase">Fighting</span>
            </button>
            <button class="type-filter-btn flex flex-col items-center gap-y-1" data-type="psychic" title="Psychic">
                <img src="/images/icones/psychic.svg" alt="Psychic" class="w-10 h-10">
                <span class="text-xs font-semibold text-slate-400 uppercase">Psychic</span>
            </button>
            <button class="type-filter-btn flex flex-col items-center gap-y-1" data-type="rock" title="Rock">
                <img src="/images/icones/rock.svg" alt="Rock" class="w-10 h-10">
                <span class="text-xs font-semibold text-slate-400 uppercase">Rock</span>
            </button>
            <button class="type-filter-btn flex flex-col items-center gap-y-1" data-type="ghost" title="Ghost">
                <img src="/images/icones/ghost.svg" alt="Ghost" class="w-10 h-10">
                <span class="text-xs font-semibold text-slate-400 uppercase">Ghost</span>
            </button>
            <button class="type-filter-btn flex flex-col items-center gap-y-1" data-type="ice" title="Ice">
                <img src="/images/icones/ice.svg" alt="Ice" class="w-10 h-10">
                <span class="text-xs font-semibold text-slate-400 uppercase">Ice</span>
            </button>
            <button class="type-filter-btn flex flex-col items-center gap-y-1" data-type="dragon" title="Dragon">
                <img src="/images/icones/dragon.svg" alt="Dragon" class="w-10 h-10">
                <span class="text-xs font-semibold text-slate-400 uppercase">Dragon</span>
            </button>
            <button class="type-filter-btn flex flex-col items-center gap-y-1" data-type="flying" title="Flying">
                <img src="/images/icones/flying.svg" alt="Flying" class="w-10 h-10">
                <span class="text-xs font-semibold text-slate-400 uppercase">Flying</span>
            </button>
            <button class="type-filter-btn flex flex-col items-center gap-y-1" data-type="steel" title="Steel">
                <img src="/images/icones/steel.svg" alt="Steel" class="w-10 h-10">
                <span class="text-xs font-semibold text-slate-400 uppercase">Steel</span>
            </button>

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
        <div id="details-modal-content" class="pokemon-tcg-card max-w-md mx-auto max-h-[90vh] overflow-y-auto relative text-slate-900">
            <button id="details-close-btn" class="absolute top-2 right-4 text-slate-800 hover:text-black text-4xl z-10">&times;</button>
            
            <div class="p-4 pr-12 flex justify-between items-center">
                <h2 id="details-name" class="text-2xl font-bold capitalize"></h2>
                <div class="flex items-center gap-2">
                    <span id="details-hp" class="text-lg font-bold">HP 120</span>
                    <img id="details-type-icon" src="" alt="Tipo" class="w-8 h-8">
                </div>
            </div>

            <div class="image-frame">
                <img id="details-image" src="" alt="Pokemon" class="w-full object-contain">
            </div>

            <div class="px-4 text-xs italic font-semibold text-center">
                <p id="details-pokedex-info"></p>
            </div>
            <hr>

            <div class="p-4 space-y-4 text-sm">
                <div id="details-abilities">
                    </div>
                <div id="details-description" class="p-2 bg-white/30 rounded-md text-xs italic">
                    </div>
            </div>
            <hr>
            
            <div class="p-4">
                <div class="flex items-center gap-2">
                    <strong class="text-sm">Fraqueza:</strong>
                    <div id="details-weaknesses" class="flex flex-wrap gap-2">
                        </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/pokedex.js') }}" defer></script>

</body>
</html>