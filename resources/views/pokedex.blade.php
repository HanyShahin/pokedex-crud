<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pokédex CRUD - 1ª Geração</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Poppins', sans-serif; }
        .type-grass { background-color: #78C850; color: white; }
        .type-fire { background-color: #F08030; color: white; }
        .type-water { background-color: #6890F0; color: white; }
        .type-bug { background-color: #A8B820; color: white; }
        .type-normal { background-color: #A8A878; color: white; }
        .type-poison { background-color: #A040A0; color: white; }
        .type-electric { background-color: #F8D030; color: black; }
        .type-ground { background-color: #E0C068; color: black; }
        .type-fairy { background-color: #EE99AC; color: white; }
        .type-fighting { background-color: #C03028; color: white; }
        .type-psychic { background-color: #F85888; color: white; }
        .type-rock { background-color: #B8A038; color: white; }
        .type-ghost { background-color: #705898; color: white; }
        .type-ice { background-color: #98D8D8; color: black; }
        .type-dragon { background-color: #7038F8; color: white; }
        .type-dark { background-color: #705848; color: white; }
        .type-steel { background-color: #B8B8D0; color: black; }
        .type-flying { background-color: #A890F0; color: white; }
        .pokemon-card {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out, opacity 0.3s;
        }
        .pokemon-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.2), 0 8px 10px -6px rgb(0 0 0 / 0.2);
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .pokeball-loader {
            width: 60px; height: 60px;
            background-image: url('https://upload.wikimedia.org/wikipedia/commons/5/53/Pok%C3%A9_Ball_icon.svg');
            background-size: contain;
            animation: spin 1s linear infinite;
        }
        body {
             background-color: #0f172a;
             background-image: radial-gradient(circle at top right, #1e293b, #0f172a);
        }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #1e293b; }
        ::-webkit-scrollbar-thumb { background: #4f46e5; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #6366f1; }
        .type-badge { padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; text-transform: capitalize; }
        /* Estilo para botão de filtro ativo */
        .type-filter-btn.active {
            transform: scale(1.1);
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.4);
            font-weight: 700;
        }
    </style>
</head>
<body class="bg-slate-900 text-white pb-12">

    <header class="container mx-auto px-4 py-8 flex justify-between items-center">
        <div class="text-left">
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-yellow-400">
                Pokédex - Kanto
            </h1>
            <p class="text-slate-400 mt-2">Bem-vindo, {{ Auth::user()->name }}!</p>
        </div>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300">
                <i class="fas fa-sign-out-alt mr-2"></i>Sair
            </button>
        </form>
    </header>

    <main class="container mx-auto px-4">
        <div class="text-center mb-8">
            <button id="add-pokemon-btn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300">
                <i class="fas fa-plus mr-2"></i> Adicionar Novo Pokémon
            </button>
        </div>

        <div class="mb-8 p-4 bg-slate-800 rounded-lg shadow-md">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div class="md:col-span-1">
                    <label for="search-input" class="block text-slate-400 mb-2 font-semibold">Buscar por Nome:</label>
                    <input type="text" id="search-input" placeholder="Ex: Pikachu" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-slate-400 mb-2 font-semibold">Filtrar por Tipo:</label>
                    <div id="type-filter-buttons" class="flex flex-wrap gap-2">
                        <button class="type-filter-btn active px-3 py-1.5 rounded-full text-sm font-semibold bg-indigo-600 text-white transition-transform duration-200" data-type="all">Todos</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-grass transition-transform duration-200" data-type="grass">Grass</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-fire transition-transform duration-200" data-type="fire">Fire</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-water transition-transform duration-200" data-type="water">Water</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-bug transition-transform duration-200" data-type="bug">Bug</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-normal transition-transform duration-200" data-type="normal">Normal</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-poison transition-transform duration-200" data-type="poison">Poison</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-electric transition-transform duration-200" data-type="electric">Electric</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-ground transition-transform duration-200" data-type="ground">Ground</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-fairy transition-transform duration-200" data-type="fairy">Fairy</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-fighting transition-transform duration-200" data-type="fighting">Fighting</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-psychic transition-transform duration-200" data-type="psychic">Psychic</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-rock transition-transform duration-200" data-type="rock">Rock</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-ghost transition-transform duration-200" data-type="ghost">Ghost</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-ice transition-transform duration-200" data-type="ice">Ice</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-dragon transition-transform duration-200" data-type="dragon">Dragon</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-flying transition-transform duration-200" data-type="flying">Flying</button>
                        <button class="type-filter-btn px-3 py-1.5 rounded-full text-sm font-semibold type-steel transition-transform duration-200" data-type="steel">Steel</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="pokemon-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6"></div>
        <div id="loader" class="hidden justify-center items-center py-16"><div class="pokeball-loader"></div></div>
    </main>

    <div id="pokemon-modal" class="fixed inset-0 bg-black bg-opacity-75 flex justify-center items-center p-4 z-50 hidden">
        <div class="bg-slate-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-full overflow-y-auto transform transition-all duration-300 scale-95 opacity-0">
            <form id="pokemon-form" class="p-8">
                <input type="hidden" id="pokemon-id">
                <h2 id="modal-title" class="text-3xl font-bold mb-6 text-center">Adicionar Pokémon</h2>
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
        <p id="toast-message">Operação realizada com sucesso!</p>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- CONSTANTES E ELEMENTOS DO DOM ---
        const BASE_URL = '/pokemons';
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const grid = document.getElementById('pokemon-grid');
        const loader = document.getElementById('loader');
        const modal = document.getElementById('pokemon-modal');
        const modalForm = document.getElementById('pokemon-form');
        const modalTitle = document.getElementById('modal-title');
        const cancelButton = document.getElementById('cancel-btn');
        const addButton = document.getElementById('add-pokemon-btn');
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');
        
        // Form
        const nameInput = document.getElementById('name');
        const numberInput = document.getElementById('number');
        const imageUrlInput = document.getElementById('image_url');
        const type1Input = document.getElementById('type1');
        const type2Input = document.getElementById('type2');
        const heightInput = document.getElementById('height');
        const weightInput = document.getElementById('weight');
        const descriptionInput = document.getElementById('description');
        const autocompleteResults = document.getElementById('autocomplete-results');

        // Filtros
        const searchInput = document.getElementById('search-input');
        const typeFilterButtons = document.getElementById('type-filter-buttons');
        let activeTypeFilter = 'all'; 

        let allPokemonList = [];

        // --- FUNÇÕES DE BUSCA NA API ---
        const fetchAllPokemon = async () => {
            const url = 'https://pokeapi.co/api/v2/pokemon?limit=151&offset=0';
            try {
                const response = await fetch(url);
                const data = await response.json();
                allPokemonList = data.results.map(pokemon => {
                    const urlParts = pokemon.url.split('/');
                    const id = urlParts[urlParts.length - 2];
                    return { id: parseInt(id), name: pokemon.name };
                });
            } catch (error) { console.error('Falha ao carregar a lista de Pokémon:', error); }
        };

        // --- FUNÇÕES DO CRUD COM AJAX ---
        const renderPokemons = async () => {
            grid.innerHTML = '';
            loader.classList.remove('hidden');
            loader.classList.add('flex');
            try {
                const response = await fetch(BASE_URL);
                if (!response.ok) throw new Error('A resposta da rede não foi OK');
                const pokemons = await response.json();
                if (pokemons.length === 0) {
                    grid.innerHTML = `<p class="text-slate-400 col-span-full text-center">Nenhum Pokémon capturado. Adicione o primeiro!</p>`;
                } else {
                    pokemons.forEach(p => {
                        const type2Badge = p.type2 ? `<span class="type-badge type-${p.type2.toLowerCase()}">${p.type2}</span>` : '';
                        const card = document.createElement('div');
                        card.className = 'pokemon-card bg-slate-800 rounded-lg shadow-md overflow-hidden flex flex-col';
                        card.dataset.types = `${p.type1.toLowerCase()}${p.type2 ? ',' + p.type2.toLowerCase() : ''}`;
                        card.innerHTML = `
                            <div class="p-4 bg-slate-700 flex justify-between items-center">
                                 <p class="font-bold text-lg">#${String(p.number).padStart(3, '0')}</p>
                                <div class="flex space-x-2">
                                     <button class="edit-btn text-blue-400 hover:text-blue-300" data-id="${p.id}"><i class="fas fa-edit"></i></button>
                                    <button class="delete-btn text-red-500 hover:text-red-400" data-id="${p.id}"><i class="fas fa-trash"></i></button>
                                </div>
                             </div>
                            <div class="flex-grow flex flex-col items-center p-4">
                                <img src="${p.image_url}" alt="${p.name}" class="w-32 h-32 md:w-40 md:h-40 object-contain">
                                 <h3 class="text-2xl font-bold mt-4 capitalize">${p.name}</h3>
                                <div class="flex space-x-2 mt-2">
                                    <span class="type-badge type-${p.type1.toLowerCase()}">${p.type1}</span>
                                     ${type2Badge}
                                </div>
                            </div>
                         `;
                        grid.appendChild(card);
                    });
                }
            } catch (error) { console.error('Erro ao buscar Pokémon:', error);
            } finally {
                loader.classList.add('hidden');
                loader.classList.remove('flex');
                addCardEventListeners();
                filterAndSearch();
            }
        };

        const handleFormSubmit = async (e) => {
            e.preventDefault();
            const id = document.getElementById('pokemon-id').value;
            const url = id ? `${BASE_URL}/${id}` : BASE_URL;
            const formData = new FormData(modalForm);
            if (id) { formData.append('_method', 'PUT'); }
            try {
                const response = await fetch(url, { method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: formData });
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(Object.values(errorData.errors).flat().join('\n'));
                }
                showToast(id ? 'Pokémon atualizado!' : 'Pokémon adicionado!');
                closeModal();
                renderPokemons();
            } catch (error) { showToast(error.message || 'Erro ao salvar.', 'bg-red-500'); }
        };

        const handleDelete = async (id) => {
            if (!confirm('Tem certeza que deseja soltar este Pokémon?')) return;
            try {
                const response = await fetch(`${BASE_URL}/${id}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }});
                if (!response.ok) throw new Error('Falha ao soltar o Pokémon.');
                showToast('Pokémon solto com sucesso!', 'bg-yellow-500');
                renderPokemons();
            } catch (error) { console.error('Erro ao excluir:', error); }
        };
        
        const openModal = async (pokemonId = null) => {
            modalForm.reset();
            document.getElementById('pokemon-id').value = '';
            autocompleteResults.innerHTML = '';
            autocompleteResults.classList.add('hidden');
            nameInput.disabled = false;
            if (pokemonId) {
                modalTitle.innerText = "Editar Pokémon";
                try {
                    const response = await fetch(`${BASE_URL}/${pokemonId}`);
                    if (!response.ok) throw new Error('Não foi possível carregar os dados.');
                    const pokemon = await response.json();
                    document.getElementById('pokemon-id').value = pokemon.id;
                    nameInput.value = pokemon.name;
                    numberInput.value = pokemon.number;
                    type1Input.value = pokemon.type1;
                    type2Input.value = pokemon.type2 || '';
                    imageUrlInput.value = pokemon.image_url;
                    heightInput.value = pokemon.height;
                    weightInput.value = pokemon.weight;
                    descriptionInput.value = pokemon.description;
                } catch (error) { showToast(error.message, 'bg-red-500'); return; }
            } else {
                modalTitle.innerText = "Adicionar Pokémon";
            }
            modal.classList.remove('hidden');
            setTimeout(() => modal.querySelector('.transform').classList.remove('scale-95', 'opacity-0'), 10);
        };

        const closeModal = () => {
            modal.querySelector('.transform').classList.add('scale-95', 'opacity-0');
            setTimeout(() => modal.classList.add('hidden'), 300);
        };

        const showToast = (message, colorClass = 'bg-green-500') => {
            toastMessage.innerText = message;
            toast.className = `fixed bottom-5 right-5 text-white py-3 px-6 rounded-lg shadow-lg transform transition-transform duration-500 ease-in-out ${colorClass} translate-x-0`;
            setTimeout(() => { toast.style.transform = 'translateX(120%)'; }, 3000);
        };

        const onSuggestionClick = async (pokemon) => {
            autocompleteResults.innerHTML = '';
            autocompleteResults.classList.add('hidden');
            nameInput.value = 'Carregando...';
            nameInput.disabled = true;
            try {
                const response = await fetch(`https://pokeapi.co/api/v2/pokemon/${pokemon.id}/`);
                const details = await response.json();
                nameInput.value = details.name.charAt(0).toUpperCase() + details.name.slice(1);
                numberInput.value = details.id;
                imageUrlInput.value = details.sprites.other['official-artwork'].front_default;
                heightInput.value = details.height / 10;
                weightInput.value = details.weight / 10;
                type1Input.value = details.types[0].type.name;
                type2Input.value = details.types[1] ? details.types[1].type.name : '';
            } catch (error) { showToast("Não foi possível carregar os detalhes.", "bg-red-500"); nameInput.value = '';
            } finally { nameInput.disabled = false; }
        };

        const addCardEventListeners = () => {
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', (e) => { e.stopPropagation(); openModal(parseInt(button.dataset.id)); });
            });
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', (e) => { e.stopPropagation(); handleDelete(parseInt(button.dataset.id)); });
            });
        };
        
        // --- FUNÇÃO DE FILTRO E BUSCA ---
        const filterAndSearch = () => {
            const searchTerm = searchInput.value.toLowerCase();
            document.querySelectorAll('.pokemon-card').forEach(card => {
                const name = card.querySelector('h3').textContent.toLowerCase();
                const types = card.dataset.types.split(',');
                const nameMatch = name.includes(searchTerm);
                const typeMatch = activeTypeFilter === 'all' || types.includes(activeTypeFilter);
                if (nameMatch && typeMatch) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        };

        // --- EVENT LISTENERS ---
        addButton.addEventListener('click', () => openModal());
        cancelButton.addEventListener('click', closeModal);
        modalForm.addEventListener('submit', handleFormSubmit);

        nameInput.addEventListener('input', () => {
            const query = nameInput.value.toLowerCase();
            autocompleteResults.innerHTML = '';
            if (query.length < 2) { autocompleteResults.classList.add('hidden'); return; }
            const suggestions = allPokemonList.filter(p => p.name.startsWith(query)).slice(0, 7);
            if (suggestions.length > 0) {
                suggestions.forEach(pokemon => {
                    const suggestionDiv = document.createElement('div');
                    suggestionDiv.className = 'p-2 hover:bg-slate-700 cursor-pointer text-white capitalize';
                    suggestionDiv.textContent = pokemon.name;
                    suggestionDiv.addEventListener('click', () => onSuggestionClick(pokemon));
                    autocompleteResults.appendChild(suggestionDiv);
                });
                autocompleteResults.classList.remove('hidden');
            } else { autocompleteResults.classList.add('hidden'); }
        });
        
        searchInput.addEventListener('input', filterAndSearch);
        typeFilterButtons.addEventListener('click', (e) => {
            const button = e.target.closest('.type-filter-btn');
            if (button) {
                typeFilterButtons.querySelector('.active')?.classList.remove('active', 'bg-indigo-600', 'text-white');
                button.classList.add('active', 'bg-indigo-600', 'text-white');
                activeTypeFilter = button.dataset.type;
                filterAndSearch();
            }
        });

        // --- INICIALIZAÇÃO ---
        renderPokemons();
        fetchAllPokemon();
    });
</script>

</body>
</html>