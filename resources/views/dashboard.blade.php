{{-- 
    Dashboard Principal de MultiPagos
    - Layout responsivo con Breeze y Alpine.js
    - Funcionalidad AJAX para transacciones
    - Tema claro/oscuro persistente
--}}

<x-app-layout>
    {{-- 
        Aquí llamamos a la función `dashboardData()` que definimos en la etiqueta <script> al final del archivo.
        Esto soluciona los errores de sintaxis y organiza mejor el código.
    --}}
    <div x-data="dashboardData()" 
         :class="{'dark': darkMode === true}"
         class="bg-gray-100 dark:bg-gray-900 min-h-screen">

        {{-- Layout principal de dos columnas --}}
        <div class="flex">

            {{-- Sidebar Desktop --}}
            <aside class="hidden md:flex md:flex-col md:w-64 bg-white dark:bg-gray-800 shadow-lg min-h-screen">
                <div class="px-6 py-4 border-b dark:border-gray-700">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">MultiPagos</h1>
                </div>
                <nav class="flex-1 px-4 py-4 space-y-2">
                    <a href="#" class="nav-item nav-item-active">
                        <svg class="nav-icon text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="#" class="nav-item">
                        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Transacciones</span>
                    </a>
                    <a href="#" class="nav-item">
                        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Perfil</span>
                    </a>
                </nav>
            </aside>

            {{-- Contenido Principal --}}
            <div class="flex-1">
                <div class="max-w-4xl mx-auto px-4 py-6">
                    
                    {{-- Header Móvil --}}
                    <header class="md:hidden flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">MultiPagos</h1>
                        <div class="flex items-center space-x-2">
                            <button @click="toggleDarkMode()" class="text-gray-700 dark:text-gray-300 hover:text-pink-500 transition-colors">
                                <svg x-show="!darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                                <svg x-show="darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </button>
                            <button class="text-gray-700 dark:text-gray-300 hover:text-pink-500 transition-colors">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    </header>
                    
                    {{-- Balance Card --}}
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg mb-6">
                        <div class="flex justify-between items-center">
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Saldo Disponible</p>
                                <p class="text-2xl font-bold text-gray-800 dark:text-white">${{ number_format(Auth::user()->balance, 2) }}</p>
                            </div>
                            <button class="bg-pink-500 hover:bg-pink-600 text-white p-3 rounded-full transition-colors">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h5M20 20v-5h-5M4 20l5-5M20 4l-5 5" />
                                </svg>
                            </button>
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ganancias</p>
                                <p class="text-2xl font-bold text-gray-800 dark:text-white">${{ number_format(Auth::user()->earnings, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons Grid --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center mb-6">
                        <button @click="openModal('deposito')" class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-lg flex flex-col items-center justify-center space-y-2 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="h-8 w-8 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="font-semibold text-sm text-gray-700 dark:text-gray-200">Depósitos</span>
                        </button>
                        
                        <button @click="openModal('retiro')" class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-lg flex flex-col items-center justify-center space-y-2 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="h-8 w-8 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5 5.5a2.5 2.5 0 113.536-3.536l6.464 6.464a2.5 2.5 0 11-3.536 3.536L5.5 9.5z" />
                            </svg>
                            <span class="font-semibold text-sm text-gray-700 dark:text-gray-200">Retiros</span>
                        </button>
                        
                        <a href="#" class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-lg flex flex-col items-center justify-center space-y-2 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="h-8 w-8 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-semibold text-sm text-gray-700 dark:text-gray-200">Historial</span>
                        </a>
                        
                        <a href="#" class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-lg flex flex-col items-center justify-center space-y-2 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="h-8 w-8 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-semibold text-sm text-gray-700 dark:text-gray-200">Ayuda</span>
                        </a>
                    </div>
                    
                    {{-- Recent Activity Section --}}
<div>
    <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Actividad Reciente</h2>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
        <div class="space-y-4">
            <template x-for="transaction in activeTransactions" :key="transaction.id">
                <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center space-x-4">
                        <div class="p-2 rounded-full" :class="getTransactionIconClass(transaction.type)">
                            <svg x-show="transaction.type === 'deposito'" class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <svg x-show="transaction.type === 'retiro'" class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-white">
                                <span x-text="formatTransactionType(transaction.type)"></span> de $<span x-text="formatAmount(transaction.amount)"></span>
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="formatStatus(transaction.status)"></p>
                        </div>
                    </div>
                    
                    {{-- Botones dinámicos según el estado --}}
                    <div>
                        {{-- Si está pendiente de aceptación --}}
                        <template x-if="transaction.status === 'pending_acceptance'">
                            <span class="text-sm font-bold text-orange-500 bg-orange-100 dark:bg-orange-900 px-3 py-1 rounded-full">
                                Esperando cajero
                            </span>
                        </template>
                        
                        {{-- Si fue aceptada o pago enviado (puede ir al chat) --}}
                        <template x-if="transaction.status === 'accepted' || transaction.status === 'payment_sent'">
                            <a :href="'/transaction/' + transaction.id + '/chat'" 
                               class="bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                <span x-show="transaction.status === 'accepted'">Ir al Chat</span>
                                <span x-show="transaction.status === 'payment_sent'">Ver Chat</span>
                            </a>
                        </template>
                        
                        {{-- Si está completada --}}
                        <template x-if="transaction.status === 'completed'">
                            <span class="text-sm font-bold text-green-500 bg-green-100 dark:bg-green-900 px-3 py-1 rounded-full">
                                ✅ Completada
                            </span>
                        </template>
                        
                        {{-- Fallback para otros estados --}}
                        <template x-if="!['pending_acceptance', 'accepted', 'payment_sent', 'completed'].includes(transaction.status)">
                            <a href="#" class="text-sm font-bold text-pink-500 hover:text-pink-600 hover:underline transition-colors">Ver</a>
                        </template>
                    </div>
                </div>
            </template>
            
            <div x-show="activeTransactions.length === 0" class="text-center py-8">
                <div class="text-gray-400 dark:text-gray-500 mb-2">
                    <svg class="w-12 h-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div

                </div>
            </div>
        </div>

        {{-- Modal de Transacción --}}
        <div x-show="isModalOpen" 
             x-cloak 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div @click.outside="closeModal()" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md p-8 m-4">
                
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-pink-100 dark:bg-pink-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="modalType === 'deposito'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            <path x-show="modalType === 'retiro'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" style="display: none;" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2" x-text="getModalTitle()"></h2>
                    <p class="text-gray-600 dark:text-gray-400">Completa los datos para iniciar la operación</p>
                </div>
                
                <form @submit.prevent="submitTransaction" class="space-y-6">
                    <input type="hidden" name="type" x-bind:value="modalType">
                    
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Monto</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">$</span>
                            </div>
                            <input type="number" 
                                   step="0.01" 
                                   name="amount" 
                                   id="amount" 
                                   class="block w-full pl-7 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-colors" 
                                   placeholder="0.00" 
                                   required>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">USD</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex gap-3 pt-4">
                        <button type="button" 
                                @click="closeModal()" 
                                class="flex-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 font-bold py-3 px-6 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" 
                                :disabled="isSubmitting" 
                                class="flex-1 bg-pink-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-pink-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center justify-center">
                            <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSubmitting ? 'Procesando...' : 'Confirmar'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        {{-- Modal de Estado de Transacción --}}
        <div x-show="showStatusModal" 
             x-cloak 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50">
            <div @click.outside="minimizeStatusModal()" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-sm p-8 m-4 text-center">
                
                <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-6">Transacción Creada</h2>
                
                <div class="relative w-24 h-24 mx-auto mb-8">
                    <div class="absolute inset-0 border-4 border-pink-500 rounded-full animate-ping opacity-20"></div>
                    <div class="absolute inset-2 border-4 border-gray-300 dark:border-gray-600 rounded-full"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-8 h-8 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>

                <div class="flex justify-between items-center text-xs font-semibold text-gray-500 dark:text-gray-400 mb-8">
                    <div class="flex flex-col items-center text-pink-500">
                        <div class="w-8 h-8 rounded-full bg-pink-500 flex items-center justify-center text-white mb-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <span>Creada</span>
                    </div>
                    <div class="flex-1 h-0.5 bg-gray-300 dark:bg-gray-600 mx-2"></div>
                    <div class="flex flex-col items-center" :class="getStepClass('accepted')">
                        <div class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center mb-2 text-white text-sm font-bold" :class="getStepBgClass('accepted')">2</div>
                        <span>En Proceso</span>
                    </div>
                    <div class="flex-1 h-0.5 bg-gray-300 dark:bg-gray-600 mx-2"></div>
                    <div class="flex flex-col items-center" :class="getStepClass('completed')">
                        <div class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center mb-2 text-white text-sm font-bold" :class="getStepBgClass('completed')">3</div>
                        <span>Completada</span>
                    </div>
                </div>

                <button @click="minimizeStatusModal()" 
                        class="w-full bg-pink-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-pink-700 transition-colors">
                    Entendido
                </button>
            </div>
        </div>

        {{-- NUEVO: Indicador de estado minimizado --}}
        <div x-show="isStatusMinimized" 
             x-cloak
             @click="showStatusModal = true; isStatusMinimized = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2"
             class="fixed bottom-4 right-4 bg-white dark:bg-gray-700 p-3 rounded-full shadow-lg cursor-pointer flex items-center space-x-3 z-40">
            <div class="relative flex items-center justify-center w-8 h-8">
                <div class="absolute inset-0 bg-pink-500 rounded-full animate-ping opacity-75"></div>
                <div class="relative bg-pink-600 rounded-full w-4 h-4"></div>
            </div>
            <span class="font-semibold text-sm text-gray-700 dark:text-gray-200 pr-3">Transacción en Proceso</span>
        </div>

    </div>

    {{-- Estilos CSS adicionales --}}
    <style>
        .nav-item {
            @apply flex items-center px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors;
        }
        .nav-item-active {
            @apply text-gray-700 dark:text-gray-200 bg-gray-200 dark:bg-gray-700;
        }
        .nav-icon {
            @apply h-6 w-6 mr-3;
        }
    </style>

    {{-- JavaScript para Alpine.js --}}
    <script>
        function dashboardData() {
            return {
                // Estado de la aplicación
                darkMode: localStorage.getItem('darkMode') === 'true',
                isModalOpen: false,
                modalType: '',
                showStatusModal: false,
                statusTransaction: null,
                activeTransactions: @json($activeTransactions),
                isSubmitting: false,
                isStatusMinimized: false, // Nuevo estado para el indicador

                // Inicialización
                init() {
    this.$watch('darkMode', val => localStorage.setItem('darkMode', val));
    this.setupPusherConnection(); // ✅ Agregar esta línea
},

setupPusherConnection() {
    console.log('🔄 Configurando Pusher para vendedor...');
    
    const pusher = new Pusher('f1b3a9569a8bd0f48b63', {
        cluster: 'sa1',
        forceTLS: true
    });

    // Escuchar en canal específico del usuario
    const userChannel = pusher.subscribe('user-{{ Auth::id() }}');

    userChannel.bind('transaction-accepted', (data) => {
        console.log('🎉 Tu transacción fue aceptada:', data);
        
        // Cerrar modal de estado si está abierto
        this.showStatusModal = false;
        this.isStatusMinimized = false;
        
        // Mostrar notificación
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('¡Transacción aceptada!', {
                body: 'Tu solicitud fue aceptada. Redirigiendo al chat...',
                icon: '/favicon.ico'
            });
        }
        
        // Ir automáticamente al chat después de un breve delay
        setTimeout(() => {
            window.location.href = `/transaction/${data.transaction.id}/chat`;
        }, 1000);
    });

    userChannel.bind('pusher:subscription_succeeded', () => {
        console.log('✅ Suscrito al canal user-{{ Auth::id() }}');
    });

    pusher.connection.bind('connected', () => {
        console.log('✅ Pusher conectado para vendedor');
    });
},

                // Funciones de utilidad
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                },
                openModal(type) {
                    this.modalType = type;
                    this.isModalOpen = true;
                },
                closeModal() {
                    this.isModalOpen = false;
                    this.modalType = '';
                },
                minimizeStatusModal() {
                    this.showStatusModal = false;
                    this.isStatusMinimized = true;
                },
                getModalTitle() {
                    return this.modalType === 'deposito' ? 'Crear Nuevo Depósito' : 'Solicitar Nuevo Retiro';
                },

                // Funciones de formato
                formatTransactionType(type) {
                    return type.charAt(0).toUpperCase() + type.slice(1);
                },
                formatAmount(amount) {
                    return parseFloat(amount).toFixed(2);
                },
                formatStatus(status) {
                    return status.replace(/_/g, ' ').replace(/^\w/, c => c.toUpperCase());
                },
                getTransactionIconClass(type) {
                    return type === 'deposito' 
                        ? 'bg-green-100 dark:bg-green-900' 
                        : 'bg-red-100 dark:bg-red-900';
                },
                getStepClass(step) {
                    if (!this.statusTransaction) return '';
                    return this.statusTransaction.status === step ? 'text-pink-500' : '';
                },
                getStepBgClass(step) {
                    if (!this.statusTransaction) return '';
                    return this.statusTransaction.status === step ? 'bg-pink-500' : '';
                },

                // Envío del formulario AJAX
                async submitTransaction(event) {
                    if (this.isSubmitting) return;
                    
                    this.isSubmitting = true;
                    const formData = new FormData(event.target);
                    
                    try {
                        const response = await fetch('{{ route('transactions.store') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const result = await response.json();

                        if (!response.ok) {
                            throw new Error(result.message || 'Ha ocurrido un error inesperado');
                        }
                        
                        this.activeTransactions.unshift(result.transaction);
                        this.statusTransaction = result.transaction;
                        
                        this.closeModal();
                        this.showStatusModal = true;
                        this.isStatusMinimized = false; // Asegurarse de que no esté minimizado al crear una nueva
                        
                        event.target.reset();

                    } catch (error) {
                        console.error('Error al procesar la transacción:', error);
                        alert('Error: ' + error.message);
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
