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
    const nameInput = document.getElementById('name');
    const numberInput = document.getElementById('number');
    const imageUrlInput = document.getElementById('image_url');
    const type1Input = document.getElementById('type1');
    const type2Input = document.getElementById('type2');
    const heightInput = document.getElementById('height');
    const weightInput = document.getElementById('weight');
    const descriptionInput = document.getElementById('description');
    const autocompleteResults = document.getElementById('autocomplete-results');
    const searchInput = document.getElementById('search-input');
    const typeFilterButtons = document.getElementById('type-filter-buttons');
    
    // --- ESTADO DA APLICAÇÃO ---
    let allPokemonDetails = []; 
    let caughtPokemonMap = new Map();
    let activeTypeFilter = 'all';

    // --- FUNÇÕES DE GERAÇÃO DE HTML ---
    const createCaughtCardHTML = (pokemon) => {
        const type2Badge = pokemon.type2 ? `<span class="type-badge type-${pokemon.type2.toLowerCase()}">${pokemon.type2}</span>` : '';
        return `
            <div class="p-4 bg-slate-700 flex justify-between items-center actions">
                <p class="font-bold text-lg">#${String(pokemon.number).padStart(3, '0')}</p>
                <div class="flex space-x-2">
                    <button class="edit-btn text-blue-400 hover:text-blue-300" data-id="${pokemon.id}"><i class="fas fa-edit"></i></button>
                    <button class="delete-btn text-red-500 hover:text-red-400" data-id="${pokemon.id}"><i class="fas fa-trash"></i></button>
                </div>
            </div>
            <div class="flex-grow flex flex-col items-center p-4">
                <img src="${pokemon.image_url}" alt="${pokemon.name}" class="pokemon-image w-32 h-32 md:w-40 md:h-40 object-contain">
                <h3 class="text-2xl font-bold mt-4 capitalize">${pokemon.name}</h3>
                <div class="flex space-x-2 mt-2">
                    <span class="type-badge type-${pokemon.type1.toLowerCase()}">${pokemon.type1}</span>
                    ${type2Badge}
                </div>
            </div>
        `;
    };

    const createSilhouetteCardHTML = (pokemon) => {
        return `
            <div class="p-4 bg-slate-900 flex justify-between items-center actions">
                 <p class="font-bold text-lg text-slate-600">#${String(pokemon.id).padStart(3, '0')}</p>
            </div>
            <div class="flex-grow flex flex-col items-center p-4">
                <img src="${pokemon.sprites.other['official-artwork'].front_default}" alt="???" class="pokemon-image w-32 h-32 md:w-40 md:h-40 object-contain">
                 <h3 class="text-2xl font-bold mt-4 capitalize text-slate-600">???</h3>
                <div class="flex space-x-2 mt-2 invisible">
                    <span class="type-badge">Tipo</span>
                </div>
            </div>
        `;
    };
    
    // --- LÓGICA PRINCIPAL DE RENDERIZAÇÃO ---
    const buildFullPokedex = async () => {
        loader.style.display = 'flex';
        grid.innerHTML = '';
        try {
            const [caughtResponse, apiListResponse] = await Promise.all([
                fetch(BASE_URL).then(res => res.ok ? res.json() : Promise.reject('Falha ao buscar Pokémon capturados')),
                fetch('https://pokeapi.co/api/v2/pokemon?limit=151&offset=0').then(res => res.ok ? res.json() : Promise.reject('Falha ao buscar na PokéAPI'))
            ]);

            caughtPokemonMap = new Map(caughtResponse.map(p => [p.number, p]));
            
            const detailPromises = apiListResponse.results.map(p => fetch(p.url).then(res => res.json()));
            allPokemonDetails = await Promise.all(detailPromises);
            
            grid.innerHTML = ''; 
            allPokemonDetails.sort((a,b) => a.id - b.id).forEach(pokemonDetail => {
                const card = document.createElement('div');
                card.id = `pokemon-card-${pokemonDetail.id}`;
                card.className = 'pokemon-card bg-slate-800 rounded-lg shadow-md overflow-hidden flex flex-col';
                card.dataset.pokename = pokemonDetail.name;
                
                if (caughtPokemonMap.has(pokemonDetail.id)) {
                    const caughtData = caughtPokemonMap.get(pokemonDetail.id);
                    card.innerHTML = createCaughtCardHTML(caughtData);
                    card.dataset.types = `${caughtData.type1.toLowerCase()}${caughtData.type2 ? ',' + caughtData.type2.toLowerCase() : ''}`;
                } else {
                    card.classList.add('silhouette');
                    card.innerHTML = createSilhouetteCardHTML(pokemonDetail);
                    const types = pokemonDetail.types.map(t => t.type.name).join(',');
                    card.dataset.types = types;
                }
                grid.appendChild(card);
            });

        } catch (error) {
            console.error("Erro ao construir a Pokédex:", error);
            grid.innerHTML = `<p class="text-red-500 col-span-full text-center">Falha ao carregar a Pokédex. Tente recarregar a página.</p>`;
        } finally {
            loader.style.display = 'none';
            addCardEventListeners();
            filterAndSearch();
        }
    };

    // --- LÓGICA DE CRUD E UI ---
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
            const newOrUpdatedPokemon = await response.json();
            
            caughtPokemonMap.set(newOrUpdatedPokemon.number, newOrUpdatedPokemon);
            
            const cardToUpdate = document.getElementById(`pokemon-card-${newOrUpdatedPokemon.number}`);
            if (cardToUpdate) {
                cardToUpdate.innerHTML = createCaughtCardHTML(newOrUpdatedPokemon);
                cardToUpdate.classList.remove('silhouette');
                cardToUpdate.dataset.types = `${newOrUpdatedPokemon.type1.toLowerCase()}${newOrUpdatedPokemon.type2 ? ',' + newOrUpdatedPokemon.type2.toLowerCase() : ''}`;
                addCardEventListenersForCard(cardToUpdate);
            }
            
            showToast(id ? 'Pokémon atualizado!' : 'Captura registrada!');
            closeModal();
            filterAndSearch();
        } catch (error) { showToast(error.message || 'Erro ao salvar.', 'bg-red-500'); }
    };

    const handleDelete = async (id, cardElement) => {
        if (!confirm('Tem certeza que deseja soltar este Pokémon?')) return;
        try {
            const pokemonNumber = parseInt(cardElement.querySelector('p').textContent.replace('#', ''));
            
            const response = await fetch(`${BASE_URL}/${id}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }});
            if (!response.ok) throw new Error('Falha ao soltar o Pokémon.');

            caughtPokemonMap.delete(pokemonNumber);
            const originalPokemonData = allPokemonDetails.find(p => p.id === pokemonNumber);
            if (originalPokemonData) {
                cardElement.innerHTML = createSilhouetteCardHTML(originalPokemonData);
                cardElement.classList.add('silhouette');
                cardElement.dataset.types = originalPokemonData.types.map(t => t.type.name).join(',');
            }
            showToast('Pokémon solto com sucesso!', 'bg-yellow-500');
            filterAndSearch();
        } catch (error) { console.error('Erro ao excluir:', error); }
    };
    
    const openModal = async (pokemonId = null) => {
        modalForm.reset();
        document.getElementById('pokemon-id').value = '';
        autocompleteResults.innerHTML = '';
        autocompleteResults.classList.add('hidden');
        nameInput.disabled = false;
        if (pokemonId) {
            modalTitle.innerText = "Editar Captura";
            const pokemonData = [...caughtPokemonMap.values()].find(p => p.id === pokemonId);
            if(pokemonData) {
                document.getElementById('pokemon-id').value = pokemonData.id;
                nameInput.value = pokemonData.name;
                numberInput.value = pokemonData.number;
                type1Input.value = pokemonData.type1;
                type2Input.value = pokemonData.type2 || '';
                imageUrlInput.value = pokemonData.image_url;
                heightInput.value = pokemonData.height;
                weightInput.value = pokemonData.weight;
                descriptionInput.value = pokemonData.description;
            }
        } else {
            modalTitle.innerText = "Registrar Nova Captura";
        }
        modal.classList.remove('hidden');
    };

    const closeModal = () => { modal.classList.add('hidden'); };

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
            const details = allPokemonDetails.find(p => p.id === pokemon.id);
            if(details) {
                nameInput.value = details.name.charAt(0).toUpperCase() + details.name.slice(1);
                numberInput.value = details.id;
                imageUrlInput.value = details.sprites.other['official-artwork'].front_default;
                heightInput.value = details.height / 10;
                weightInput.value = details.weight / 10;
                type1Input.value = details.types[0].type.name;
                type2Input.value = details.types[1] ? details.types[1].type.name : '';
            }
        } catch (error) { showToast("Não foi possível carregar os detalhes.", "bg-red-500"); nameInput.value = '';
        } finally { nameInput.disabled = false; }
    };

    const addCardEventListenersForCard = (card) => {
        const editBtn = card.querySelector('.edit-btn');
        const deleteBtn = card.querySelector('.delete-btn');
        if(editBtn) {
            editBtn.addEventListener('click', (e) => { e.stopPropagation(); openModal(parseInt(editBtn.dataset.id)); });
        }
        if(deleteBtn) {
            deleteBtn.addEventListener('click', (e) => { e.stopPropagation(); handleDelete(parseInt(deleteBtn.dataset.id), card); });
        }
    };

    const addCardEventListeners = () => {
        document.querySelectorAll('.pokemon-card:not(.silhouette)').forEach(card => addCardEventListenersForCard(card));
    };
    
    const filterAndSearch = () => {
        const searchTerm = searchInput.value.toLowerCase();
        document.querySelectorAll('.pokemon-card').forEach(card => {
            const name = card.dataset.pokename.toLowerCase();
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
        // Sugere apenas Pokémon que ainda não foram capturados
        const suggestions = allPokemonDetails.filter(p => !caughtPokemonMap.has(p.id) && p.name.startsWith(query)).slice(0, 7);
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
    buildFullPokedex();
});