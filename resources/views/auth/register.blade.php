<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokédex - Registro de Treinador</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Press Start 2P', cursive;
            overflow: hidden; /* Evita barras de rolagem */
        }
        .background-container {
            position: fixed;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            z-index: -1;
            background-image: url('/registro.jpg');
            background-size: cover;
            background-position: center center;
            filter: blur(3px) brightness(0.9);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="background-container"></div>

    <div class="w-full max-w-md bg-[#483434] p-4 rounded-xl shadow-2xl border-4 border-black">
        <div class="bg-[#F5EEDC] p-6 md:p-8 rounded-lg relative border-2 border-gray-700">
            
            <div class="absolute -top-5 left-1/2 -translate-x-1/2 w-24 h-8 bg-gray-500 rounded-t-lg border-2 border-b-0 border-gray-700"></div>

            <h1 class="text-xl md:text-2xl text-center text-gray-800 mb-2">REGISTRO DE</h1>
            <h2 class="text-xl md:text-2xl text-center text-gray-800 mb-8">NOVO TREINADOR</h2>

            <form method="POST" action="{{ route('register') }}">
                @csrf <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-xs mb-2">NOME DE TREINADOR:</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full px-3 py-2 bg-stone-200 text-gray-800 rounded-md border-2 border-stone-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition text-sm">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-xs mb-2">E-MAIL:</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-3 py-2 bg-stone-200 text-gray-800 rounded-md border-2 border-stone-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition text-sm">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-xs mb-2">SENHA:</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-3 py-2 bg-stone-200 text-gray-800 rounded-md border-2 border-stone-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition text-sm">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-8">
                    <label for="password_confirmation" class="block text-gray-700 text-xs mb-2">CONFIRMAR SENHA:</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full px-3 py-2 bg-stone-200 text-gray-800 rounded-md border-2 border-stone-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition text-sm">
                </div>

                <button type="submit" 
                        class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition-colors duration-300 focus:outline-none focus:ring-4 focus:ring-green-400 shadow-lg">
                    INICIAR JORNADA
                </button>
            </form>

            <div class="text-center mt-6">
                <a href="{{ route('login') }}" class="text-xs text-gray-600 hover:text-black">Ja tem uma conta? Faca o login!</a>
            </div>

            <div class="absolute bottom-4 right-4 transform rotate-12">
                <div class="border-2 border-red-600 rounded-md p-2">
                    <p class="text-red-600 text-xs leading-none">APROVADO</p>
                    <p class="text-red-600 text-xs leading-none">PROF. C</p>
                </div>
            </div>

        </div>
    </div>
</body>
</html>