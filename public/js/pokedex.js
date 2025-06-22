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