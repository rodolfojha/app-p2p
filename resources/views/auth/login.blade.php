<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - UltraMultiPagos</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen overflow-hidden">
    <!-- Container que ocupa toda la pantalla -->
    <div class="h-screen flex">
        <!-- Panel Izquierdo - Formulario -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
            <div class="w-full max-w-md">
                <!-- Logo y título -->
                <div class="text-center mb-8">
                    <div class="flex items-center justify-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-yellow-500 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                        <span class="ml-3 text-2xl font-bold text-gray-800">
                            Ultra<span class="text-red-500">MultiPagos</span>
                        </span>
                    </div>
                </div>

                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">
                        Multi Pagos de <span class="text-blue-600">ULTRA-NET</span>
                    </h1>
                    <p class="text-gray-600">
                        Introduce los datos de autenticación de tu cuenta para iniciar sesión
                    </p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Formulario -->
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input id="email" 
                                   type="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus 
                                   autocomplete="username"
                                   placeholder="vendedor@gmail.com"
                                   class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg bg-gray-50 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                        </div>
                        @error('email')
                            <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input id="password" 
                                   type="password" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password"
                                   placeholder="••••••••"
                                   class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg bg-gray-50 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                        </div>
                        @error('password')
                            <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Recordarme -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="flex items-center">
                            <input id="remember_me" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" name="remember">
                            <span class="ml-2 text-sm text-gray-600">Recordarme</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-blue-600 hover:text-blue-500" href="{{ route('password.request') }}">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    <!-- Botón de iniciar sesión -->
                    <div>
                        <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            INICIAR SESIÓN
                        </button>
                    </div>

                    <!-- Link de registro -->
                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            ¿No tienes una cuenta? 
                            <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                Regístrate aquí
                            </a>
                        </p>
                    </div>
                </form>

                <!-- Info de la empresa -->
                <div class="text-center mt-8">
                    <p class="text-sm text-gray-500 mb-2">MultiPagos una empresa de UltraNet Telecomunicaciones SAS</p>
                    <div class="flex items-center justify-center text-sm text-gray-400">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Carrera 24 #26 152 Pasto Nariño
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Derecho - Ilustración -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-pink-400 to-pink-600 relative overflow-hidden">
            <!-- Contenido del panel derecho -->
            <div class="relative z-10 flex flex-col justify-center items-center text-white p-12 w-full">
                <div class="text-center">
                    <!-- Ícono principal -->
                    <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full mx-auto mb-8 flex items-center justify-center">
                        <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    
                    <!-- Título -->
                    <h2 class="text-3xl lg:text-4xl font-bold mb-6">
                        Gestiona tus Pagos de Forma Inteligente
                    </h2>
                    
                    <!-- Descripción -->
                    <p class="text-lg lg:text-xl mb-8 opacity-90">
                        Plataforma segura y confiable para todas tus transacciones financieras
                    </p>
                    
                    <!-- Lista de características -->
                    <div class="space-y-4 text-left max-w-sm mx-auto">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg mr-4 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-lg">Transacciones seguras y rápidas</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg mr-4 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-lg">Soporte 24/7</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg mr-4 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-lg">Comisiones competitivas</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg mr-4 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-lg">Interface intuitiva</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Elementos decorativos de fondo -->
            <div class="absolute inset-0 overflow-hidden">
                <!-- Formas geométricas -->
                <div class="absolute top-20 left-20 w-32 h-32 bg-white bg-opacity-10 rounded-2xl transform rotate-12"></div>
                <div class="absolute top-40 right-32 w-24 h-24 bg-white bg-opacity-10 rounded-xl transform -rotate-6"></div>
                <div class="absolute bottom-40 left-32 w-16 h-16 bg-white bg-opacity-10 rounded-lg transform rotate-45"></div>
                <div class="absolute bottom-20 right-20 w-28 h-28 bg-white bg-opacity-10 rounded-2xl transform -rotate-12"></div>
                
                <!-- Elementos isométricos simulados -->
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 opacity-20">
                    <!-- Documentos/formularios -->
                    <div class="absolute top-8 left-8 w-28 h-36 bg-white rounded-lg shadow-lg transform rotate-12 skew-y-2"></div>
                    <div class="absolute top-16 right-12 w-24 h-32 bg-white rounded-lg shadow-lg transform -rotate-6 skew-y-1"></div>
                    <div class="absolute bottom-12 left-16 w-32 h-40 bg-white rounded-lg shadow-lg transform rotate-3 skew-y-1"></div>
                    
                    <!-- Gráficos de barras */
                    <div class="absolute top-24 left-24 w-6 h-20 bg-white rounded transform skew-x-12"></div>
                    <div class="absolute top-20 left-32 w-6 h-28 bg-white rounded transform skew-x-12"></div>
                    <div class="absolute top-28 left-40 w-6 h-16 bg-white rounded transform skew-x-12"></div>
                    
                    <!-- Iconos de dinero */
                    <div class="absolute top-16 right-16 w-10 h-10 bg-yellow-200 rounded-full flex items-center justify-center">
                        <span class="text-yellow-600 font-bold text-sm">$</span>
                    </div>
                    <div class="absolute bottom-24 right-24 w-8 h-8 bg-green-200 rounded-full flex items-center justify-center">
                        <span class="text-green-600 font-bold text-xs">$</span>
                    </div>
                </div>
                
                <!-- Figuras de personas (simuladas) -->
                <div class="absolute bottom-8 left-8 w-12 h-16 bg-white bg-opacity-30 rounded-t-full"></div>
                <div class="absolute bottom-12 right-12 w-10 h-14 bg-white bg-opacity-30 rounded-t-full"></div>
                <div class="absolute top-12 left-1/2 w-8 h-12 bg-white bg-opacity-30 rounded-t-full"></div>
            </div>
        </div>
    </div>
</body>
</html>