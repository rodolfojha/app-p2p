{{-- 
    Dashboard Principal para el rol de Cajero.
    Muestra las solicitudes disponibles y las que ya ha aceptado.
    INCLUYE FUNCIONALIDAD EN TIEMPO REAL
--}}

<x-app-layout>
    <div x-data="cashierDashboardData()" 
         :class="{'dark': darkMode === true}"
         class="bg-gray-100 dark:bg-gray-900 min-h-screen">

        <div class="flex">
            {{-- Sidebar Desktop (similar al del vendedor) --}}
            <aside class="hidden md:flex md:flex-col md:w-64 bg-white dark:bg-gray-800 shadow-lg min-h-screen">
                <div class="px-6 py-4 border-b dark:border-gray-700">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">MultiPagos (Cajero)</h1>
                </div>
                <nav class="flex-1 px-4 py-4 space-y-2">
                    <a href="#" class="flex items-center px-4 py-2 rounded-lg transition-colors text-gray-700 dark:text-gray-200 bg-gray-200 dark:bg-gray-700">
                        <svg class="h-6 w-6 mr-3 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                        <span>Dashboard</span>
                    </a>
                </nav>
            </aside>

            {{-- Contenido Principal --}}
            <div class="flex-1">
                <div class="max-w-4xl mx-auto px-4 py-6">
                    
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Panel de Cajero</h1>

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
                    <div>
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
                    <div class="mt-8" x-show="acceptedTransactions.length > 0">
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

    {{-- JavaScript para tiempo real --}}
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
                    this.setupEchoConnection();
                    this.$watch('darkMode', val => localStorage.setItem('darkMode', val));
                },

                // Configurar conexión en tiempo real
             setupEchoConnection() {
    console.log('🔄 Configurando Pusher directo...');

    // ✅ USAR PUSHER DIRECTO (que sí funciona)
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
                    
                    // Mostrar notificación
                    this.showNotification('Nueva solicitud de ' + transactionData.type);
                    
                    // Quitar el indicador "nuevo" después de 5 segundos
                    setTimeout(() => {
                        transactionData.isNew = false;
                        this.newRequestsCount = Math.max(0, this.newRequestsCount - 1);
                    }, 5000);
                },

                // Aceptar transacción
                acceptTransaction(transaction) {
                    // TODO: Implementar lógica para aceptar transacción
                    alert('Funcionalidad de aceptar transacción en desarrollo');
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