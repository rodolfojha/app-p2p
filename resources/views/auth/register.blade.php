<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - UltraMultiPagos</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen overflow-hidden">
    <!-- Container que ocupa toda la pantalla -->
    <div class="h-screen flex">
        <!-- Panel Izquierdo - Formulario -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 bg-white overflow-y-auto">
            <div class="w-full max-w-md">
                <!-- Logo y título -->
                <div class="text-center mb-6">
                    <div class="flex items-center justify-center mb-4">
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

                <div class="mb-6">
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 mb-2">
                        Únete a <span class="text-blue-600">ULTRA-NET</span>
                    </h1>
                    <p class="text-gray-600 text-sm lg:text-base">
                        Crea tu cuenta y comienza a gestionar tus pagos de forma inteligente
                    </p>
                </div>

                <!-- Formulario -->
                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <!-- Nombre completo -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input id="name" 
                                   type="text" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required 
                                   autofocus 
                                   autocomplete="name"
                                   placeholder="Juan Pérez"
                                   class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" />
                        </div>
                        @error('name')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
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
                                   autocomplete="username"
                                   placeholder="juan@ejemplo.com"
                                   class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" />
                        </div>
                        @error('email')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Selección de rol -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Cuenta</label>
                        <div class="space-y-2">
                            <label class="relative flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 hover:bg-blue-50 transition-all group">
                                <input type="radio" name="role" value="vendedor" class="sr-only" {{ old('role') === 'vendedor' ? 'checked' : '' }} required>
                                <div class="flex items-center w-full">
                                    <div class="flex-shrink-0">
                                        <div class="w-5 h-5 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4a2 2 0 00-2 2v6a2 2 0 002 2h8a2 2 0 002-2v-6a2 2 0 00-2-2z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-sm font-semibold text-gray-900">Vendedor</div>
                                        <div class="text-xs text-gray-500">Realiza depósitos y retiros de fondos</div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="w-4 h-4 border-2 border-gray-300 rounded-full group-hover:border-blue-500 radio-indicator"></div>
                                    </div>
                                </div>
                            </label>

                            <label class="relative flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 hover:bg-blue-50 transition-all group">
                                <input type="radio" name="role" value="cashier" class="sr-only" {{ old('role') === 'cashier' ? 'checked' : '' }}>
                                <div class="flex items-center w-full">
                                    <div class="flex-shrink-0">
                                        <div class="w-5 h-5 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-sm font-semibold text-gray-900">Cajero</div>
                                        <div class="text-xs text-gray-500">Procesa transacciones y gestiona pagos</div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="w-4 h-4 border-2 border-gray-300 rounded-full group-hover:border-blue-500 radio-indicator"></div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('role')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Código de referido -->
                    <div>
                        <label for="referral_code" class="block text-sm font-medium text-gray-700 mb-1">Código de Referido (Opcional)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                            </div>
                            <input id="referral_code" 
                                   type="text" 
                                   name="referral_code" 
                                   value="{{ old('referral_code') }}" 
                                   autocomplete="off"
                                   placeholder="ABC123"
                                   class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" />
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Si tienes un código de referido, ingrésalo para obtener beneficios</p>
                        @error('referral_code')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
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
                                   autocomplete="new-password"
                                   placeholder="Mínimo 8 caracteres"
                                   class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" />
                        </div>
                        @error('password')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirmar contraseña -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <input id="password_confirmation" 
                                   type="password" 
                                   name="password_confirmation" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="Repite tu contraseña"
                                   class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" />
                        </div>
                        @error('password_confirmation')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Términos y condiciones -->
                    <div class="flex items-start">
                        <input type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1" name="terms" required>
                        <span class="ml-2 text-sm text-gray-600">
                            Acepto los 
                            <a href="#" class="text-blue-600 hover:text-blue-500 font-semibold underline">términos y condiciones</a> 
                            y la 
                            <a href="#" class="text-blue-600 hover:text-blue-500 font-semibold underline">política de privacidad</a>
                        </span>
                    </div>
                    @error('terms')
                        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                    @enderror

                    <!-- Botón de registro -->
                    <div class="pt-2">
                        <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            CREAR CUENTA
                        </button>
                    </div>

                    <!-- Link de login -->
                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            ¿Ya tienes una cuenta? 
                            <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                                Inicia sesión aquí
                            </a>
                        </p>
                    </div>
                </form>

                <!-- Info de la empresa -->
                <div class="text-center mt-6">
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
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 relative overflow-hidden">
            <!-- Contenido del panel derecho -->
            <div class="relative z-10 flex flex-col justify-center items-center text-white p-12 w-full">
                <div class="text-center">
                    <!-- Ícono principal -->
                    <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full mx-auto mb-8 flex items-center justify-center animate-pulse">
                        <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    
                    <!-- Título -->
                    <h2 class="text-3xl lg:text-4xl font-bold mb-6">
                        Comienza tu Viaje Financiero
                    </h2>
                    
                    <!-- Descripción -->
                    <p class="text-lg lg:text-xl mb-8 opacity-90">
                        Únete a miles de usuarios que confían en nuestra plataforma
                    </p>
                    
                    <!-- Lista de beneficios -->
                    <div class="space-y-4 text-left max-w-sm mx-auto">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg mr-4 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-lg">Registro rápido y seguro</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg mr-4 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-lg">Bonificaciones por referidos</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg mr-4 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-lg">Sin costos de mantenimiento</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg mr-4 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-lg">Activación inmediata</span>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="grid grid-cols-2 gap-6 mt-8 max-w-sm mx-auto">
                        <div class="text-center">
                            <div class="text-3xl font-bold">10K+</div>
                            <div class="text-sm opacity-80">Usuarios Activos</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold">$5M+</div>
                            <div class="text-sm opacity-80">Transacciones</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold">99.9%</div>
                            <div class="text-sm opacity-80">Tiempo Activo</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold">24/7</div>
                            <div class="text-sm opacity-80">Soporte</div>
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
                    
                    <!-- Iconos de usuario y dinero */
                    <div class="absolute top-16 right-16 w-10 h-10 bg-blue-200 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
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

    <!-- Estilos para radio buttons -->
    <style>
        /* Radio button personalizado */
        input[type="radio"]:checked + div {
            border-color: #3b82f6 !important;
            background-color: rgba(59, 130, 246, 0.1) !important;
        }
        
        input[type="radio"]:checked + div .radio-indicator {
            border-color: #3b82f6 !important;
            background-color: #3b82f6 !important;
            position: relative;
        }
        
        input[type="radio"]:checked + div .radio-indicator::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 6px;
            height: 6px;
            background-color: white;
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</body>
</html>