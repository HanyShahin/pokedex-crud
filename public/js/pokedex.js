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
    const showCaughtOnlyCheckbox = document.getElementById('show-caught-only-checkbox');
    const progressText = document.getElementById('progress-text');
    const progressBarFill = document.getElementById('progress-bar-fill');

    // --- NOVO: Elementos do Modal de Detalhes ---
    const detailsModal = document.getElementById('pokemon-details-modal');
    const detailsCloseBtn = document.getElementById('details-close-btn');
    const detailsName = document.getElementById('details-name');
    const detailsNumber = document.getElementById('details-number');
    const detailsImage = document.getElementById('details-image');
    const detailsTypes = document.getElementById('details-types');
    const detailsDescription = document.getElementById('details-description');
    const detailsPhysical = document.getElementById('details-physical');
    const detailsAbilities = document.getElementById('details-abilities');
    const statsChartCanvas = document.getElementById('stats-chart');
    
    // --- ESTADO DA APLICAÇÃO ---
    let allPokemonDetails = []; 
    let caughtPokemonMap = new Map();
    let activeTypeFilter = 'all';
    let statsChartInstance = null; // NOVO: Para controlar a instância do gráfico

    // --- NOVA FUNÇÃO AUXILIAR PARA GERAR CORES A PARTIR DE UM TEXTO ---
    const stringToHslColor = (str, saturation = 75, lightness = 45) => {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }

        const hue = hash % 360;
        return `hsl(${hue}, ${saturation}%, ${lightness}%)`;
    };  

    const updateProgressBar = () => {
        const caughtCount = caughtPokemonMap.size;
        const totalCount = allPokemonDetails.length || 151;

        if (!progressText || !progressBarFill) return;

        const percentage = totalCount > 0 ? (caughtCount / totalCount) * 100 : 0;
        
        progressText.textContent = `${caughtCount} / ${totalCount}`;
        progressBarFill.style.width = `${percentage}%`;
    };

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

    // --- NOVA FUNÇÃO PARA CALCULAR E EXIBIR AS RELAÇÕES DE DANO ---
    const calculateAndDisplayDamageRelations = async (types, relationsContainer) => {
        try {
            relationsContainer.innerHTML = '<p class="text-slate-400">Calculando...</p>';

            // 1. & 2. A busca e o cálculo dos multiplicadores continuam os mesmos
            const typePromises = types.map(type => fetch(`https://pokeapi.co/api/v2/type/${type.toLowerCase()}`).then(res => res.json()));
            const typesData = await Promise.all(typePromises);

            const damageMultipliers = {};
            const allTypes = ['normal', 'fire', 'water', 'electric', 'grass', 'ice', 'fighting', 'poison', 'ground', 'flying', 'psychic', 'bug', 'rock', 'ghost', 'dragon', 'dark', 'steel', 'fairy'];
            allTypes.forEach(t => { damageMultipliers[t] = 1; });

            typesData.forEach(typeData => {
                const relations = typeData.damage_relations;
                relations.double_damage_from.forEach(t => { damageMultipliers[t.name] *= 2; });
                relations.half_damage_from.forEach(t => { damageMultipliers[t.name] *= 0.5; });
                relations.no_damage_from.forEach(t => { damageMultipliers[t.name] *= 0; });
            });

            // 3. Coletar todas as fraquezas (2x e 4x) em uma lista única
            const weaknesses = [];
            for (const type in damageMultipliers) {
                const multiplier = damageMultipliers[type];
                if (multiplier >= 2) {
                    weaknesses.push({ name: type, multiplier: multiplier });
                }
            }
            
            // Opcional: Ordenar para que as fraquezas 4x apareçam primeiro
            weaknesses.sort((a, b) => b.multiplier - a.multiplier);

            // 4. Gerar o HTML com uma lista única de badges
            let html = '';
            if (weaknesses.length > 0) {
                const badgesHtml = weaknesses.map(weakness => {
                    // Adiciona a classe de destaque se a fraqueza for 4x
                    const highlightClass = weakness.multiplier === 4 ? 'super-weakness-badge' : '';
                    
                    // Retorna o badge, com a classe de destaque se aplicável
                    return `<span class="type-badge type-${weakness.name.toLowerCase()} ${highlightClass}">${weakness.name}</span>`;
                }).join('');

                // Envolve todos os badges em um único container flexível
                html = `<div class="flex flex-wrap gap-2">${badgesHtml}</div>`;
            }
            
            relationsContainer.innerHTML = html || '<p class="text-slate-400">Este Pokémon não possui fraquezas.</p>';

        } catch (error) {
            console.error("Erro ao buscar relações de dano:", error);
            relationsContainer.innerHTML = '<p class="text-red-500">Não foi possível carregar as fraquezas.</p>';
        }
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

            caughtPokemonMap = new Map(caughtResponse.map(p => [parseInt(p.number, 10), p]));

            
            const detailPromises = apiListResponse.results.map(p => fetch(p.url).then(res => res.json()));
            allPokemonDetails = await Promise.all(detailPromises);
            
            grid.innerHTML = ''; 
            allPokemonDetails.sort((a,b) => a.id - b.id).forEach(pokemonDetail => {
                const card = document.createElement('div');
                card.id = `pokemon-card-${pokemonDetail.id}`;
                // --- ALTERADO --- Adicionado cursor-pointer para indicar que é clicável
                card.className = 'pokemon-card bg-slate-800 rounded-lg shadow-md overflow-hidden flex flex-col cursor-pointer';
                card.dataset.pokename = pokemonDetail.name;
                
                if (caughtPokemonMap.has(pokemonDetail.id)) {
                    const caughtData = caughtPokemonMap.get(pokemonDetail.id);
                    card.innerHTML = createCaughtCardHTML(caughtData);
                    card.dataset.types = `${caughtData.type1.toLowerCase()}${caughtData.type2 ? ',' + caughtData.type2.toLowerCase() : ''}`;
                    card.dataset.number = caughtData.number; // --- NOVO --- Adiciona o número ao card
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
            filterAndSearch();
            updateProgressBar();
        }
    };

    // --- LÓGICA DE CRUD E UI ---
    const handleFormSubmit = async (e) => {
        e.preventDefault();
        const id = document.getElementById('pokemon-id').value;

        if (!id) {
            const pokemonNumber = parseInt(numberInput.value, 10);
            if (caughtPokemonMap.has(pokemonNumber)) {
                showToast('Este Pokémon já foi capturado!', 'bg-yellow-500');
                return;
            }
        }
        
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
            
            caughtPokemonMap.set(parseInt(newOrUpdatedPokemon.number, 10), newOrUpdatedPokemon);
            
            const cardToUpdate = document.getElementById(`pokemon-card-${newOrUpdatedPokemon.number}`);
            if (cardToUpdate) {
                cardToUpdate.innerHTML = createCaughtCardHTML(newOrUpdatedPokemon);
                cardToUpdate.classList.remove('silhouette');
                cardToUpdate.dataset.types = `${newOrUpdatedPokemon.type1.toLowerCase()}${newOrUpdatedPokemon.type2 ? ',' + newOrUpdatedPokemon.type2.toLowerCase() : ''}`;
                cardToUpdate.dataset.number = newOrUpdatedPokemon.number;
            }
            
            showToast(id ? 'Pokémon atualizado!' : 'Captura registrada!');
            closeModal();
            filterAndSearch();
            updateProgressBar();
        } catch (error) { 
            showToast(error.message || 'Erro ao salvar.', 'bg-red-500'); 
        }
    };

    const handleDelete = async (id, cardElement) => {
        if (!confirm('Tem certeza que deseja soltar este Pokémon?')) return;
        try {
            const pokemonNumber = parseInt(cardElement.dataset.number);
            
            const response = await fetch(`${BASE_URL}/${id}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }});
            if (!response.ok) throw new Error('Falha ao soltar o Pokémon.');

            caughtPokemonMap.delete(pokemonNumber);
            const originalPokemonData = allPokemonDetails.find(p => p.id === pokemonNumber);
            if (originalPokemonData) {
                cardElement.innerHTML = createSilhouetteCardHTML(originalPokemonData);
                cardElement.classList.add('silhouette');
                cardElement.dataset.types = originalPokemonData.types.map(t => t.type.name).join(',');
                delete cardElement.dataset.number; // --- NOVO --- Remove o número ao soltar
            }
            showToast('Pokémon solto com sucesso!', 'bg-yellow-500');
            filterAndSearch();
            updateProgressBar();
        } catch (error) { 
            console.error('Erro ao excluir:', error); 
        }
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

    // --- NOVO: Funções para o Modal de Detalhes ---
    const openDetailsModal = (pokemonNumber) => {
        const caughtData = caughtPokemonMap.get(parseInt(pokemonNumber, 10));
        const apiData = allPokemonDetails.find(p => p.id === parseInt(pokemonNumber, 10));

        const cardContainer = document.getElementById('details-modal-content');
        
        if (!caughtData || !apiData) {
            console.error("Não foi possível encontrar os dados para o Pokémon:", pokemonNumber);
            return;
        }

        // --- LÓGICA PRINCIPAL PARA PREENCHER A CARTA ---

        // 1. Limpa classes de tipo antigas e adiciona a nova
        cardContainer.className = 'pokemon-tcg-card max-w-md mx-auto max-h-[90vh] overflow-y-auto relative text-slate-900'; // Reseta para o padrão
        cardContainer.classList.add(`card-bg-${caughtData.type1.toLowerCase()}`);

        // 2. Preenche o cabeçalho
        document.getElementById('details-name').textContent = caughtData.name;
        const hpStat = apiData.stats.find(s => s.stat.name === 'hp').base_stat;
        document.getElementById('details-hp').textContent = `HP ${hpStat}`;
        document.getElementById('details-type-icon').src = `/images/icones/${caughtData.type1.toLowerCase()}.svg`;

        // 3. Preenche a imagem
        document.getElementById('details-image').src = caughtData.image_url;

        // 4. Preenche as informações da Pokédex
        const species = apiData.species.name;
        document.getElementById('details-pokedex-info').textContent = `NO. ${String(caughtData.number).padStart(3, '0')} ${species}  HT: ${caughtData.height}m  WT: ${caughtData.weight}kg`;

        // 5. Preenche as habilidades
        const abilitiesContainer = document.getElementById('details-abilities');
        renderAbilitiesWithDescriptions(apiData.abilities, abilitiesContainer);
        
        // 6. Preenche a descrição do treinador
        document.getElementById('details-description').textContent = caughtData.description || 'Nenhuma anotação do treinador.';

        // 7. Preenche as fraquezas (usando a função que já tínhamos)
        const weaknessesContainer = document.getElementById('details-weaknesses');
        const currentTypes = [caughtData.type1];
        if (caughtData.type2) currentTypes.push(caughtData.type2);
        // Adaptação da chamada para o novo container de fraquezas
        calculateAndDisplayDamageRelations(currentTypes, weaknessesContainer);
        
        // Mostra o modal
        detailsModal.classList.remove('hidden');
    };

    const closeDetailsModal = () => {
        detailsModal.classList.add('hidden');
        if (statsChartInstance) {
            statsChartInstance.destroy();
            statsChartInstance = null;
        }
    };

    // --- NOVA FUNÇÃO PARA BUSCAR E EXIBIR AS DESCRIÇÕES DAS HABILIDADES ---
    const renderAbilitiesWithDescriptions = async (abilities, container) => {
        container.innerHTML = '<p class="text-xs italic text-slate-700/80">Carregando...</p>';

        try {
            const abilityPromises = abilities.map(a => fetch(a.ability.url).then(res => res.json()));
            const abilitiesDetails = await Promise.all(abilityPromises);

            const abilitiesHtml = abilitiesDetails.map(detail => {
                const abilityName = detail.name.replace('-', ' ');

                // --- ESTA É A PARTE QUE MUDOU ---
                // Em vez de 'effect_entries', buscamos em 'flavor_text_entries'
                const flavorTextEntry = detail.flavor_text_entries.find(entry => entry.language.name === 'en');
                
                // Usamos 'flavor_text' em vez de 'short_effect'
                const description = flavorTextEntry ? flavorTextEntry.flavor_text : 'Sem descrição curta.';
                // --- FIM DA MUDANÇA ---

                return `<div class="mb-3">
                            <strong class="capitalize text-lg font-bold text-slate-800">${abilityName}</strong>
                            <p class="text-xs mt-1 text-slate-900/90">${description}</p>
                        </div>`;
            }).join('');

            container.innerHTML = abilitiesHtml;

        } catch (error) {
            console.error("Erro ao buscar detalhes das habilidades:", error);
            container.innerHTML = '<p class="text-red-500 text-xs">Não foi possível carregar as descrições.</p>';
        }
    };

    const showToast = (message, colorClass = 'bg-green-500') => {
        toast.style.transform = ''; 
        toastMessage.innerText = message;
        toast.className = `fixed bottom-5 right-5 text-white py-3 px-6 rounded-lg shadow-lg transform transition-transform duration-500 ease-in-out ${colorClass} translate-x-0`;
        
        setTimeout(() => { 
            toast.style.transform = 'translateX(120%)'; 
        }, 3000);
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
        } catch (error) { 
            showToast("Não foi possível carregar os detalhes.", "bg-red-500"); 
            nameInput.value = '';
        } finally { 
            nameInput.disabled = false; 
        }
    };
    
    const filterAndSearch = () => {
        const searchTerm = searchInput.value.toLowerCase();
        const showCaughtOnly = showCaughtOnlyCheckbox.checked;
        document.querySelectorAll('.pokemon-card').forEach(card => {
            const name = card.dataset.pokename.toLowerCase();
            const types = card.dataset.types.split(',');
            const isCaught = !card.classList.contains('silhouette'); 
            const nameMatch = name.includes(searchTerm);
            const typeMatch = activeTypeFilter === 'all' || types.includes(activeTypeFilter);
            const caughtMatch = !showCaughtOnly || isCaught;
            if (nameMatch && typeMatch && caughtMatch) {
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

    // --- NOVO: Listeners para o Modal de Detalhes ---
    detailsCloseBtn.addEventListener('click', closeDetailsModal);
    detailsModal.addEventListener('click', (e) => {
        if (e.target === detailsModal) {
            closeDetailsModal();
        }
    });


    nameInput.addEventListener('input', () => {
        const query = nameInput.value.toLowerCase();
        autocompleteResults.innerHTML = '';
        if (query.length < 2) { autocompleteResults.classList.add('hidden'); return; }
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
    showCaughtOnlyCheckbox.addEventListener('change', filterAndSearch);
    typeFilterButtons.addEventListener('click', (e) => {
        const button = e.target.closest('.type-filter-btn');
        if (button) {
            // Lógica para desativar o botão antigo e ativar o novo
            const currentActive = typeFilterButtons.querySelector('.active');
            if (currentActive) {
                currentActive.classList.remove('active', 'bg-indigo-600', 'text-white');
            }
            button.classList.add('active', 'bg-indigo-600', 'text-white');
            activeTypeFilter = button.dataset.type;
            filterAndSearch();
        }
    });

    // --- NOVO OUVINTE DE EVENTOS CENTRALIZADO PARA A GRADE ---
    grid.addEventListener('click', (e) => {
        // Encontra o elemento .pokemon-card mais próximo de onde o clique ocorreu
        const card = e.target.closest('.pokemon-card');

        // Se o clique não foi dentro de um card, não faz nada
        if (!card) {
            return;
        }

        // Verifica se o alvo específico do clique foi um botão de editar ou deletar
        const editBtn = e.target.closest('.edit-btn');
        const deleteBtn = e.target.closest('.delete-btn');

        // Se foi o botão de editar, abre o modal de edição e para a execução
        if (editBtn) {
            openModal(parseInt(editBtn.dataset.id));
            return;
        }

        // Se foi o botão de deletar, chama a função de deleção e para a execução
        if (deleteBtn) {
            handleDelete(parseInt(deleteBtn.dataset.id), card);
            return;
        }

        // Se o card ainda é uma silhueta ou não tem um número, não faz nada
        if (card.classList.contains('silhouette') || !card.dataset.number) {
            return;
        }
        
        // Se passou por todas as verificações, abre o modal de detalhes
        openDetailsModal(parseInt(card.dataset.number, 10));
    });

    // --- INICIALIZAÇÃO ---
    buildFullPokedex();
});