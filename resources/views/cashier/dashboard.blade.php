{{-- 
    Dashboard Principal para el rol de Cajero.
    Muestra saldo, ganancias, solicitudes disponibles y las que ya ha aceptado.
    INCLUYE FUNCIONALIDAD EN TIEMPO REAL CON PUSHER
--}}

<x-app-layout>
    <div x-data="cashierDashboardData()" 
         :class="{'dark': darkMode === true}"
         class="bg-gray-100 dark:bg-gray-900 min-h-screen">

        <div class="flex">
            {{-- Sidebar Desktop --}}
            <aside class="hidden md:flex md:flex-col md:w-64 bg-white dark:bg-gray-800 shadow-lg min-h-screen">
                <div class="px-6 py-4 border-b dark:border-gray-700">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">MultiPagos (Cajero)</h1>
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
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Panel de Cajero</h1>
                        <div class="flex items-center space-x-2">
                            <button @click="toggleDarkMode()" class="text-gray-700 dark:text-gray-300 hover:text-pink-500 transition-colors">
                                <svg x-show="!darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                                <svg x-show="darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
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

                    {{-- Indicador de conexión en tiempo real --}}
                    <div class="mb-4 flex items-center space-x-2">
                        <div class="flex items-center space-x-2">
                            <div :class="isConnected ? 'bg-green-500' : 'bg-red-500'" class="w-3 h-3 rounded-full"></div>
                            <span class="text-sm text-gray-600 dark:text-gray-400" x-text="isConnected ? 'Conectado en tiempo real' : 'Desconectado'"></span>
                        </div>
                        <div x-show="newRequestsCount > 0" class="bg-pink-500 text-white text-xs px-2 py-1 rounded-full">
                            <span x-text="newRequestsCount"></span> nueva(s)
                        </div>
                    </div>

                    {{-- Sección de Solicitudes Disponibles --}}
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">
                            Solicitudes Disponibles 
                            <span x-show="availableTransactions.length > 0" class="text-sm font-normal text-gray-500" x-text="'(' + availableTransactions.length + ')'"></span>
                        </h2>
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                            <div class="space-y-4">
                                <template x-for="transaction in availableTransactions" :key="transaction.id">
                                    <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors border border-gray-200 dark:border-gray-600"
                                         :class="{'ring-2 ring-pink-500 bg-pink-50 dark:bg-pink-900/20': transaction.isNew}">
                                        <div class="flex items-center space-x-4">
                                            <div class="p-2 rounded-full" :class="transaction.type == 'deposito' ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900'">
                                                <svg x-show="transaction.type == 'deposito'" class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                                <svg x-show="transaction.type == 'retiro'" class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800 dark:text-white">
                                                    <span x-text="transaction.type.charAt(0).toUpperCase() + transaction.type.slice(1)"></span> por $<span x-text="parseFloat(transaction.amount).toFixed(2)"></span>
                                                    <span x-show="transaction.isNew" class="text-pink-500 text-sm ml-2">¡NUEVO!</span>
                                                </p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    Iniciada por: <span x-text="transaction.initiator.name"></span>
                                                </p>
                                                <p class="text-xs text-gray-400 dark:text-gray-500" x-text="formatDate(transaction.created_at)"></p>
                                            </div>
                                        </div>
                                        <button @click="acceptTransaction(transaction)" 
                                                class="bg-pink-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-pink-700 transition-colors">
                                            Aceptar
                                        </button>
                                    </div>
                                </template>
                                
                                <div x-show="availableTransactions.length === 0" class="text-center py-8">
                                    <div class="text-gray-400 dark:text-gray-500 mb-2">
                                        <svg class="w-12 h-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400">No hay solicitudes disponibles en este momento.</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Las nuevas solicitudes aparecerán automáticamente aquí.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sección de Transacciones Aceptadas --}}
                    <div x-show="acceptedTransactions.length > 0">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">
                            Mis Transacciones Aceptadas
                            <span class="text-sm font-normal text-gray-500" x-text="'(' + acceptedTransactions.length + ')'"></span>
                        </h2>
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                            <div class="space-y-4">
                                <template x-for="transaction in acceptedTransactions" :key="transaction.id">
                                    <div class="flex items-center justify-between p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700">
                                        <div class="flex items-center space-x-4">
                                            <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-900">
                                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800 dark:text-white">
                                                    <span x-text="transaction.type.charAt(0).toUpperCase() + transaction.type.slice(1)"></span> por $<span x-text="parseFloat(transaction.amount).toFixed(2)"></span>
                                                </p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    Estado: <span x-text="transaction.status.replace(/_/g, ' ').replace(/^\w/, c => c.toUpperCase())"></span>
                                                </p>
                                            </div>
                                        </div>
                                        <button class="bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                                            Ver Detalles
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Estilos CSS --}}
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

    {{-- JavaScript para Alpine.js y tiempo real --}}
    <script>
        function cashierDashboardData() {
            return {
                // Estado de conexión
                isConnected: false,
                newRequestsCount: 0,
                
                // Datos de transacciones
                availableTransactions: @json($availableTransactions),
                acceptedTransactions: @json($acceptedTransactions),
                
                // Estado general
                darkMode: localStorage.getItem('darkMode') === 'true',

                // Inicialización
                init() {
                    this.setupPusherConnection();
                    this.$watch('darkMode', val => localStorage.setItem('darkMode', val));
                },

                // Funciones de utilidad
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                },

                // Configurar conexión en tiempo real con Pusher directo
                setupPusherConnection() {
                    console.log('🔄 Configurando Pusher directo...');

                    // ✅ USAR PUSHER DIRECTO (que funciona)
                    window.Pusher.logToConsole = true;

                    const pusher = new Pusher('f1b3a9569a8bd0f48b63', {
                        cluster: 'sa1',
                        forceTLS: true
                    });

                    const channel = pusher.subscribe('public-requests');

                    // ✅ Listener para nueva transacción
                    channel.bind('new-transaction-request', (data) => {
                        console.log('🎉 NUEVA SOLICITUD RECIBIDA:', data);
                        
                        if (data.transaction) {
                            this.addNewTransaction(data.transaction);
                            this.showNotification('Nueva solicitud: $' + data.transaction.amount);
                        } else {
                            // Usar datos básicos si no hay transaction completa
                            const transactionData = {
                                id: data.transaction_id,
                                type: 'deposito',
                                amount: 100,
                                created_at: new Date().toISOString(),
                                initiator: {
                                    id: 1,
                                    name: 'Vendedor'
                                }
                            };
                            this.addNewTransaction(transactionData);
                            this.showNotification('Nueva solicitud recibida');
                        }
                    });

                    // Eventos de conexión
                    pusher.connection.bind('connected', () => {
                        console.log('✅ Conectado a Pusher directo');
                        this.isConnected = true;
                    });

                    pusher.connection.bind('disconnected', () => {
                        console.log('❌ Desconectado de Pusher');
                        this.isConnected = false;
                    });

                    console.log('✅ Pusher directo configurado');
                },

                // Agregar nueva transacción en tiempo real
                addNewTransaction(transactionData) {
                    // Verificar que no existe ya en la lista
                    const exists = this.availableTransactions.find(t => t.id === transactionData.id);
                    if (exists) return;

                    // Marcar como nueva
                    transactionData.isNew = true;
                    
                    // Agregar al inicio de la lista
                    this.availableTransactions.unshift(transactionData);
                    
                    // Incrementar contador
                    this.newRequestsCount++;
                    
                    // Quitar el indicador "nuevo" después de 5 segundos
                    setTimeout(() => {
                        transactionData.isNew = false;
                        this.newRequestsCount = Math.max(0, this.newRequestsCount - 1);
                    }, 5000);
                },

                // Aceptar transacción
                async acceptTransaction(transaction) {
                    try {
                        const response = await fetch(`/transactions/${transaction.id}/accept`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const result = await response.json();

                        if (!response.ok) {
                            throw new Error(result.message || 'Error al aceptar la transacción');
                        }

                        // Remover de solicitudes disponibles
                        this.availableTransactions = this.availableTransactions.filter(t => t.id !== transaction.id);
                        
                        // Agregar a transacciones aceptadas
                        this.acceptedTransactions.unshift(result.transaction);
                        
                        alert('Transacción aceptada exitosamente');
                        
                        // TODO: Abrir chat en el siguiente paso
                        window.location.href = `/transaction/${result.transaction.id}/chat`;

                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error: ' + error.message);
                    }
                },

                // Mostrar notificación
                showNotification(message) {
                    // Crear notificación simple
                    if ('Notification' in window && Notification.permission === 'granted') {
                        new Notification('MultiPagos', {
                            body: message,
                            icon: '/favicon.ico'
                        });
                    }
                },

                // Formatear fecha
                formatDate(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleString('es-ES', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            }
        }

        // Solicitar permisos de notificación al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }
        });
    </script>
</x-app-layout>