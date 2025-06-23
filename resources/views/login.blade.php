<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokédex - Login</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Press Start 2P (fonte pixelada) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <style>
        /* Aplicando a fonte pixelada */
        body {
            font-family: 'Press Start 2P', cursive;
            overflow: hidden; /* Evita barras de rolagem indesejadas */
        }

        /* Contêiner para o fundo com efeito de paralaxe/desfoque */
        .background-container {
            position: fixed;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            z-index: -1;
            /* CORRIGIDO: Agora usando a imagem que você enviou */
            background-image: url('/imagem2.jpg');
            background-size: cover;
            background-position: center center;
            filter: blur(2px) brightness(0.8); /* Desfoca e escurece o fundo para destacar o formulário */
        }
        
        /* Estilo para o contêiner do formulário */
        .pokedex-container {
             box-shadow: 0 15px 30px rgba(0,0,0,0.5);
        }

        /* Estilo customizado para o botão de Pokébola */
        .pokeball-button {
            position: relative;
            background: linear-gradient(to bottom, #ef4444 50%, #ffffff 50%); /* red-500 */
            border: 4px solid #1f2937; /* gray-800 */
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .pokeball-button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            background: #fff;
            border: 4px solid #1f2937;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: background 0.2s ease-in-out;
        }
        .pokeball-button::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 10px;
            height: 10px;
            background: #d1d5db; /* gray-300 */
            border: 2px solid #1f2937;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
        }
        .pokeball-button:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }
        .pokeball-button:hover::before {
            background: #f3f4f6; /* gray-100 */
        }
        .pokeball-button:active {
            transform: translateY(0) scale(1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        /* Estilo para simular uma tela de Pokedex */
        .pokedex-screen {
            background-color: #a7f3d0; /* emerald-200 */
            border: 4px solid #064e3b; /* emerald-900 */
            box-shadow: inset 0 0 10px rgba(0,0,0,0.5);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4 bg-gray-900">

    <!-- Fundo com a imagem dos Pokémon -->
    <div class="background-container"></div>

    <!-- Container principal do login -->
    <div class="pokedex-container w-full max-w-lg mx-auto bg-red-600 rounded-2xl p-4 md:p-6 border-8 border-black transform -rotate-2">
        <div class="bg-red-500 rounded-lg p-6 transform rotate-2">
            
            <!-- Cabeçalho com luzes decorativas -->
            <div class="flex items-center space-x-4 mb-6 pb-4 border-b-4 border-black">
                <div class="w-16 h-16 bg-sky-400 rounded-full border-4 border-white shadow-inner"></div>
                <div class="flex space-x-2">
                    <div class="w-6 h-6 bg-red-700 rounded-full border-2 border-white"></div>
                    <div class="w-6 h-6 bg-yellow-400 rounded-full border-2 border-white"></div>
                    <div class="w-6 h-6 bg-green-500 rounded-full border-2 border-white"></div>
                </div>
            </div>

            <!-- Formulário de Login -->
            <div class="pokedex-screen p-6 rounded-lg">
                <h1 class="text-xl md:text-2xl text-gray-900 text-center mb-6">POKEDEX LOGIN</h1>
                <form>
                    <!-- Campo de Treinador (Usuário) -->
                    <div class="mb-6">
                        <label for="trainer" class="block text-gray-800 text-sm mb-2">TREINADOR</label>
                        <input type="text" id="trainer" name="trainer" placeholder="Ash Ketchum"
                               class="w-full px-4 py-3 bg-gray-200 text-gray-800 rounded-md border-2 border-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition text-base">
                    </div>

                    <!-- Campo de Senha -->
                    <div class="mb-4">
                        <label for="password" class="block text-gray-800 text-sm mb-2">SENHA</label>
                        <input type="password" id="password" name="password" placeholder="********"
                               class="w-full px-4 py-3 bg-gray-200 text-gray-800 rounded-md border-2 border-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition text-base">
                    </div>
                     <!-- Link para Registro -->
                    <div class="text-center mb-6">
                        <a href="#" class="text-xs text-gray-800 hover:text-black">Nao tem uma conta? Registre-se</a>
                    </div>

                    <!-- Botão de Login (Pokébola) -->
                    <div class="flex justify-center">
                        <button type="submit" 
                                class="pokeball-button w-24 h-24 rounded-full focus:outline-none"
                                aria-label="Entrar">
                        </button>
                    </div>
                </form>
            </div>

            <!-- Detalhes decorativos -->
            <div class="flex justify-between items-center mt-6 pt-4 border-t-4 border-black">
                <div class="w-8 h-8 bg-red-700 rounded-full border-2 border-white"></div>
                <div class="w-24 h-8 bg-black rounded-lg grid grid-cols-3 gap-1 p-1">
                    <div class="w-full h-full bg-gray-600 rounded-sm"></div>
                    <div class="w-full h-full bg-gray-600 rounded-sm"></div>
                    <div class="w-full h-full bg-gray-600 rounded-sm"></div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
