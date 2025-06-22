<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pokédex CRUD Mágica</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Estilos customizados para o design "mágico" */
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Cores dos tipos de Pokémon */
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

        /* Animação do card */
        .pokemon-card {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }
        .pokemon-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.2), 0 8px 10px -6px rgb(0 0 0 / 0.2);
        }

        /* Animação de carregamento (Pokébola) */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .pokeball-loader {
            width: 60px;
            height: 60px;
            background-image: url('https://upload.wikimedia.org/wikipedia/commons/5/53/Pok%C3%A9_Ball_icon.svg');
            background-size: contain;
            animation: spin 1s linear infinite;
        }
        
        /* Fundo com gradiente sutil */
        body {
             background-color: #0f172a; /* fallback */
             background-image: radial-gradient(circle at top right, #1e293b, #0f172a);
        }
        
        /* Estilização da barra de rolagem */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #1e293b;
        }
        ::-webkit-scrollbar-thumb {
            background: #4f46e5;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #6366f1;
        }
    </style>
</head>
<body class="bg-slate-900 text-white pb-12">

    <!-- Cabeçalho -->
    <header class="text-center py-8">
        <h1 class="text-4xl md:text-5xl font-bold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-yellow-400">
            Pokédex CRUD
        </h1>
        <p class="text-slate-400 mt-2">Gerencie sua coleção de Pokémon com PHP Laravel e AJAX</p>
    </header>

    <main class="container mx-auto px-4">
        <!-- Botão para Adicionar Novo Pokémon -->
        <div class="text-center mb-8">
            <button id="add-pokemon-btn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300">
                <i class="fas fa-plus mr-2"></i> Adicionar Novo Pokémon
            </button>
        </div>

        <!-- Grid de Pokémon -->
        <div id="pokemon-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            <!-- Os cards dos Pokémon serão inseridos aqui via JavaScript -->
        </div>

        <!-- Indicador de Carregamento -->
        <div id="loader" class="hidden justify-center items-center py-16">
            <div class="pokeball-loader"></div>
        </div>
    </main>

    <!-- Modal para Criar/Editar Pokémon -->
    <div id="pokemon-modal" class="fixed inset-0 bg-black bg-opacity-75 flex justify-center items-center p-4 z-50 hidden">
        <div class="bg-slate-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-full overflow-y-auto transform transition-all duration-300 scale-95 opacity-0">
            <form id="pokemon-form" class="p-8">
                <input type="hidden" id="pokemon-id">
                <h2 id="modal-title" class="text-3xl font-bold mb-6 text-center">Adicionar Pokémon</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Coluna 1 -->
                    <div>
                        <div class="mb-4">
                            <label for="name" class="block text-slate-400 mb-1">Nome</label>
                            <input type="text" id="name" name="name" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <div class="mb-4">
                            <label for="number" class="block text-slate-400 mb-1">Número</label>
                            <input type="number" id="number" name="number" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <div class="mb-4">
                            <label for="type1" class="block text-slate-400 mb-1">Tipo 1</label>
                            <input type="text" id="type1" name="type1" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <div class="mb-4">
                            <label for="type2" class="block text-slate-400 mb-1">Tipo 2 (Opcional)</label>
                            <input type="text" id="type2" name="type2" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    <!-- Coluna 2 -->
                    <div>
                        <div class="mb-4">
                            <label for="image_url" class="block text-slate-400 mb-1">URL da Imagem</label>
                            <input type="url" id="image_url" name="image_url" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <div class="mb-4">
                            <label for="height" class="block text-slate-400 mb-1">Altura (m)</label>
                            <input type="number" step="0.1" id="height" name="height" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <div class="mb-4">
                            <label for="weight" class="block text-slate-400 mb-1">Peso (kg)</label>
                            <input type="number" step="0.1" id="weight" name="weight" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <div class="mb-4">
                            <label for="description" class="block text-slate-400 mb-1">Descrição</label>
                            <textarea id="description" name="description" rows="3" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required></textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <button type="button" id="cancel-btn" class="bg-slate-600 hover:bg-slate-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">Cancelar</button>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast de Notificação -->
    <div id="toast" class="fixed bottom-5 right-5 bg-green-500 text-white py-3 px-6 rounded-lg shadow-lg transform translate-x-[120%] transition-transform duration-500 ease-in-out">
        <p id="toast-message">Operação realizada com sucesso!</p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- CONSTANTES E ELEMENTOS DO DOM ---
            const BASE_URL = '/pokemons'; // Usando a rota de web.php
            const grid = document.getElementById('pokemon-grid');
            const loader = document.getElementById('loader');
            const modal = document.getElementById('pokemon-modal');
            const modalForm = document.getElementById('pokemon-form');
            const modalTitle = document.getElementById('modal-title');
            const cancelButton = document.getElementById('cancel-btn');
            const addButton = document.getElementById('add-pokemon-btn');
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // --- LÓGICA DO CRUD COM AJAX (Fetch API) ---

            /**
             * READ (ALL): Busca todos os pokémons da API e renderiza na tela.
             */
            const renderPokemons = async () => {
                grid.innerHTML = '';
                loader.classList.remove('hidden');
                loader.classList.add('flex');

                try {
                    const response = await fetch(BASE_URL);
                    if (!response.ok) throw new Error('A resposta da rede não foi OK');
                    const pokemons = await response.json();

                    if (pokemons.length === 0) {
                        grid.innerHTML = `<p class="text-slate-400 col-span-full text-center">Nenhum Pokémon encontrado. Adicione o primeiro!</p>`;
                    } else {
                        pokemons.forEach(p => {
                            const type2Badge = p.type2 ? `<span class="type-badge type-${p.type2.toLowerCase()}">${p.type2}</span>` : '';
                            const card = document.createElement('div');
                            card.className = 'pokemon-card bg-slate-800 rounded-lg shadow-md overflow-hidden flex flex-col';
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
                } catch (error) {
                    console.error('Erro ao buscar Pokémon:', error);
                    grid.innerHTML = `<p class="text-red-500 col-span-full text-center">Falha ao carregar os Pokémon. Tente novamente mais tarde.</p>`;
                } finally {
                    loader.classList.add('hidden');
                    loader.classList.remove('flex');
                    addCardEventListeners();
                }
            };

            /**
             * CREATE / UPDATE: Lida com o envio do formulário para criar ou editar um Pokémon.
             */
            const handleFormSubmit = async (e) => {
                e.preventDefault();
                const id = document.getElementById('pokemon-id').value;
                const url = id ? `${BASE_URL}/${id}` : BASE_URL;
                // No Laravel, para atualizar um recurso via formulário, usamos POST com um campo _method='PUT'
                const method = 'POST';

                const formData = new FormData(modalForm);
                // Adiciona o campo _method se for uma atualização
                if (id) {
                    formData.append('_method', 'PUT');
                }

                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        // Constrói uma mensagem de erro a partir das respostas de validação do Laravel
                        const errorMessages = Object.values(errorData.errors).map(e => e.join('\n')).join('\n');
                        throw new Error(errorMessages);
                    }

                    showToast(id ? 'Pokémon atualizado com sucesso!' : 'Pokémon adicionado com sucesso!');
                    closeModal();
                    renderPokemons();

                } catch (error) {
                    console.error('Erro ao salvar Pokémon:', error);
                    showToast(error.message || 'Erro ao salvar Pokémon.', 'bg-red-500');
                }
            };

            /**
             * DELETE: Envia uma requisição para remover um Pokémon.
             */
            const handleDelete = async (id) => {
                if (!confirm('Tem certeza que deseja excluir este Pokémon?')) return;

                try {
                    const response = await fetch(`${BASE_URL}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    if (!response.ok) throw new Error('Falha ao excluir o Pokémon.');

                    showToast('Pokémon excluído com sucesso!', 'bg-yellow-500');
                    renderPokemons();

                } catch (error) {
                    console.error('Erro ao excluir:', error);
                    showToast(error.message, 'bg-red-500');
                }
            };


            // --- FUNÇÕES AUXILIARES DE UI (Interface do Usuário) ---

            /**
             * Abre o modal, seja para criar um novo ou editar um Pokémon existente.
             * Se um ID for passado, busca os dados do Pokémon na API para preencher o formulário.
             */
            const openModal = async (pokemonId = null) => {
                modalForm.reset();
                document.getElementById('pokemon-id').value = '';

                if (pokemonId) {
                    // Modo Edição
                    modalTitle.innerText = "Editar Pokémon";
                    try {
                        const response = await fetch(`${BASE_URL}/${pokemonId}`);
                        if (!response.ok) throw new Error('Não foi possível carregar os dados do Pokémon.');
                        const pokemon = await response.json();

                        document.getElementById('pokemon-id').value = pokemon.id;
                        document.getElementById('name').value = pokemon.name;
                        document.getElementById('number').value = pokemon.number;
                        document.getElementById('type1').value = pokemon.type1;
                        document.getElementById('type2').value = pokemon.type2 || '';
                        document.getElementById('image_url').value = pokemon.image_url;
                        document.getElementById('height').value = pokemon.height;
                        document.getElementById('weight').value = pokemon.weight;
                        document.getElementById('description').value = pokemon.description;
                    } catch (error) {
                        console.error(error);
                        showToast(error.message, 'bg-red-500');
                        return; // Não abre o modal se houver erro
                    }
                } else {
                    // Modo Criação
                    modalTitle.innerText = "Adicionar Pokémon";
                }
                modal.classList.remove('hidden');
                setTimeout(() => modal.querySelector('.transform').classList.remove('scale-95', 'opacity-0'), 10);
            };

            /**
             * Fecha o modal.
             */
            const closeModal = () => {
                modal.querySelector('.transform').classList.add('scale-95', 'opacity-0');
                setTimeout(() => modal.classList.add('hidden'), 300);
            };

            /**
             * Exibe uma notificação (toast) na tela.
             */
            const showToast = (message, colorClass = 'bg-green-500') => {
                toastMessage.innerText = message;
                toast.className = `fixed bottom-5 right-5 text-white py-3 px-6 rounded-lg shadow-lg transform transition-transform duration-500 ease-in-out ${colorClass} translate-x-0`;
                setTimeout(() => {
                    toast.style.transform = 'translateX(120%)';
                }, 3000);
            };

            /**
             * Adiciona os event listeners para os botões de editar e deletar em cada card.
             * Deve ser chamada toda vez que os cards são renderizados.
             */
            const addCardEventListeners = () => {
                document.querySelectorAll('.edit-btn').forEach(button => {
                    button.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const id = parseInt(button.dataset.id);
                        openModal(id);
                    });
                });

                document.querySelectorAll('.delete-btn').forEach(button => {
                    button.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const id = parseInt(button.dataset.id);
                        handleDelete(id);
                    });
                });
            };

            // --- INICIALIZAÇÃO E EVENT LISTENERS GLOBAIS ---
            addButton.addEventListener('click', () => openModal());
            cancelButton.addEventListener('click', closeModal);
            modalForm.addEventListener('submit', handleFormSubmit);

            // Inicia a aplicação buscando e renderizando os Pokémon.
            renderPokemons();
        });
    </script>
</body>
</html>
