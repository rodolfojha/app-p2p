{{-- Vista del chat con botones dinámicos según tipo de transacción --}}
<x-app-layout>
    <div x-data="transactionChatData()" 
         :class="{'dark': darkMode === true}"
         class="bg-gray-100 dark:bg-gray-900 min-h-screen">

        <div class="max-w-4xl mx-auto px-4 py-6">
            
            {{-- Header con información de la transacción --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                        {{ ucfirst($transaction->type) }} por ${{ number_format($transaction->amount, 2) }}
                    </h1>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full" 
                             :class="{
                                 'bg-blue-500': transactionStatus === 'accepted',
                                 'bg-yellow-500': transactionStatus === 'payment_sent', 
                                 'bg-green-500': transactionStatus === 'completed'
                             }"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400" 
                              x-text="getStatusText()"></span>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Vendedor:</p>
                        <p class="font-semibold text-gray-800 dark:text-white">{{ $transaction->initiator->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Cajero:</p>
                        <p class="font-semibold text-gray-800 dark:text-white">{{ $transaction->participant->name }}</p>
                    </div>
                </div>

                {{-- Indicador del flujo según tipo de transacción --}}
                <div class="mt-4 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700">
                    @if($transaction->type === 'deposito')
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            <strong>Flujo de Depósito:</strong> El vendedor realiza el pago → El cajero confirma recepción
                        </p>
                    @else
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            <strong>Flujo de Retiro:</strong> El cajero realiza el pago → El vendedor confirma recepción
                        </p>
                    @endif
                </div>
            </div>

            {{-- Área de chat --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Chat</h2>
                
                {{-- Mensajes --}}
                <div class="h-64 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg p-4 mb-4">
                    <div class="space-y-3" id="messages-container">
                        {{-- Los mensajes aparecerán aquí --}}
                        <div class="text-center text-gray-500 dark:text-gray-400 text-sm">
                            Transacción iniciada. Puedes chatear aquí con la otra parte.
                        </div>
                    </div>
                </div>

                {{-- Input de mensaje --}}
                <div class="flex space-x-2">
                    <input type="text" 
                           x-model="newMessage"
                           @keydown.enter="sendMessage()"
                           placeholder="Escribe un mensaje..."
                           class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500">
                    <button @click="sendMessage()" 
                            class="bg-pink-600 text-white px-4 py-2 rounded-lg hover:bg-pink-700 transition-colors">
                        Enviar
                    </button>
                </div>
            </div>

            {{-- Sección de comprobantes --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Comprobantes</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Subir comprobante --}}
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                        <input type="file" id="receipt-upload" accept="image/*" class="hidden" @change="handleFileUpload($event)">
                        <label for="receipt-upload" class="cursor-pointer">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-gray-600 dark:text-gray-400">Subir comprobante</p>
                        </label>
                    </div>

                    {{-- Lista de comprobantes --}}
                    <div class="space-y-2">
                        <template x-for="receipt in receipts" :key="receipt.id">
                            <div class="flex items-center justify-between p-2 border border-gray-200 dark:border-gray-600 rounded">
                                <span class="text-sm text-gray-700 dark:text-gray-300" x-text="receipt.name"></span>
                                <button class="text-pink-600 hover:text-pink-700 text-sm">Ver</button>
                            </div>
                        </template>
                        
                        <div x-show="receipts.length === 0" class="text-gray-500 dark:text-gray-400 text-sm text-center">
                            No hay comprobantes subidos
                        </div>
                    </div>
                </div>
            </div>

            {{-- ✅ BOTONES DINÁMICOS SEGÚN TIPO DE TRANSACCIÓN --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                <div class="flex justify-between items-center">
                    
                    @if($transaction->type === 'deposito')
                        {{-- LÓGICA PARA DEPÓSITOS: Vendedor paga → Cajero confirma --}}
                        @if(Auth::id() === $transaction->initiator_id && $transaction->status === 'accepted')
                            {{-- Botón para el vendedor: Marcar pago realizado --}}
                            <button @click="markPaymentSent()" 
                                    class="bg-green-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-green-700 transition-colors">
                                💰 Pago Realizado
                            </button>
                        @elseif(Auth::id() === $transaction->participant_id && $transaction->status === 'payment_sent')
                            {{-- Botón para el cajero: Confirmar recepción --}}
                            <button @click="confirmPayment()" 
                                    class="bg-blue-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors">
                                ✅ Confirmar Recepción
                            </button>
                        @endif

                    @elseif($transaction->type === 'retiro')
                        {{-- LÓGICA PARA RETIROS: Cajero paga → Vendedor confirma --}}
                        @if(Auth::id() === $transaction->participant_id && $transaction->status === 'accepted')
                            {{-- Botón para el cajero: Marcar pago realizado --}}
                            <button @click="markPaymentSent()" 
                                    class="bg-green-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-green-700 transition-colors">
                                💰 Pago Realizado
                            </button>
                        @elseif(Auth::id() === $transaction->initiator_id && $transaction->status === 'payment_sent')
                            {{-- Botón para el vendedor: Confirmar recepción --}}
                            <button @click="confirmPayment()" 
                                    class="bg-blue-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors">
                                ✅ Confirmar Recepción
                            </button>
                        @endif
                    @endif

                    {{-- Estado visual cuando no hay acción disponible --}}
                    @if($transaction->status === 'completed')
                        <div class="flex items-center space-x-2 text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="font-bold">Transacción Completada</span>
                        </div>
                    @elseif(
                        ($transaction->type === 'deposito' && Auth::id() === $transaction->participant_id && $transaction->status === 'accepted') ||
                        ($transaction->type === 'retiro' && Auth::id() === $transaction->initiator_id && $transaction->status === 'accepted')
                    )
                        <div class="text-gray-600 dark:text-gray-400">
                            <p class="font-semibold">Esperando pago de la otra parte...</p>
                            <p class="text-sm">Recibirás una notificación cuando se marque como realizado.</p>
                        </div>
                    @endif

                    {{-- Botón para volver siempre presente --}}
                    <a href="{{ route('dashboard') }}" 
                       class="bg-gray-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-gray-600 transition-colors">
                        Volver al Dashboard
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script>
        function transactionChatData() {
            return {
                darkMode: localStorage.getItem('darkMode') === 'true',
                newMessage: '',
                messages: [],
                receipts: [],
                transactionId: {{ $transaction->id }},
                transactionStatus: '{{ $transaction->status }}',
                transactionType: '{{ $transaction->type }}', // ✅ Agregar tipo

                init() {
                    this.$watch('darkMode', val => localStorage.setItem('darkMode', val));
                    this.setupRealtimeUpdates();
                },

                // ✅ Configurar actualizaciones en tiempo real
                setupRealtimeUpdates() {
                    console.log('🔄 Configurando actualizaciones en tiempo real del chat...');
                    
                    const pusher = new Pusher('f1b3a9569a8bd0f48b63', {
                        cluster: 'sa1',
                        forceTLS: true
                    });

                    const channel = pusher.subscribe('transaction-' + this.transactionId);

                    // Verificar suscripción
                    channel.bind('pusher:subscription_succeeded', () => {
                        console.log('✅ Suscrito al canal transaction-' + this.transactionId);
                    });

                    // Escuchar cuando se marca el pago como enviado
                    channel.bind('payment-sent', (data) => {
                        console.log('💰 Pago marcado como enviado:', data);
                        this.transactionStatus = 'payment_sent';
                        
                        // ✅ Notificación personalizada según tipo de transacción
                        @if($transaction->type === 'deposito')
                            @if(Auth::id() === $transaction->participant_id)
                                alert('El vendedor ha realizado el depósito. Verifica y confirma la recepción.');
                            @endif
                        @elseif($transaction->type === 'retiro')
                            @if(Auth::id() === $transaction->initiator_id)
                                alert('El cajero ha realizado el pago. Verifica y confirma la recepción.');
                            @endif
                        @endif
                        
                        // Recargar para mostrar botones actualizados
                        location.reload();
                    });

                    // Escuchar confirmación de pago
                    channel.bind('payment-confirmed', (data) => {
                        console.log('✅ Pago confirmado:', data);
                        alert('¡Transacción completada exitosamente!');
                        
                        // Esperar un momento para que el usuario lea el mensaje
                        setTimeout(() => {
                            window.location.href = '/dashboard';
                        }, 2000);
                    });

                    // Debug de conexión
                    pusher.connection.bind('connected', () => {
                        console.log('✅ Pusher conectado en chat');
                    });

                    pusher.connection.bind('error', (error) => {
                        console.error('❌ Error de Pusher en chat:', error);
                    });
                },

                // ✅ Función para obtener el texto del estado
                getStatusText() {
                    switch(this.transactionStatus) {
                        case 'accepted': 
                            if (this.transactionType === 'deposito') {
                                return 'Esperando pago del vendedor';
                            } else {
                                return 'Esperando pago del cajero';
                            }
                        case 'payment_sent': 
                            if (this.transactionType === 'deposito') {
                                return 'Pago enviado - Esperando confirmación del cajero';
                            } else {
                                return 'Pago enviado - Esperando confirmación del vendedor';
                            }
                        case 'completed': return 'Completada';
                        default: return 'En proceso';
                    }
                },

                sendMessage() {
                    if (this.newMessage.trim() === '') return;
                    
                    // TODO: Enviar mensaje real
                    console.log('Enviando mensaje:', this.newMessage);
                    this.newMessage = '';
                },

                handleFileUpload(event) {
                    const file = event.target.files[0];
                    if (file) {
                        // TODO: Subir archivo real
                        console.log('Subiendo archivo:', file.name);
                        this.receipts.push({
                            id: Date.now(),
                            name: file.name
                        });
                    }
                },

                async markPaymentSent() {
                    try {
                        const response = await fetch(`/transaction/${this.transactionId}/payment-sent`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const result = await response.json();

                        if (!response.ok) {
                            throw new Error(result.message || 'Error al marcar el pago');
                        }

                        // ✅ Mensaje personalizado según tipo
                        if (this.transactionType === 'deposito') {
                            alert('Depósito marcado como realizado. El cajero será notificado.');
                        } else {
                            alert('Pago marcado como realizado. El vendedor será notificado.');
                        }
                        
                        this.transactionStatus = 'payment_sent';
                        location.reload(); // Recargar para actualizar botones

                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error: ' + error.message);
                    }
                },

                async confirmPayment() {
                    // ✅ Confirmación personalizada según tipo
                    let confirmMessage;
                    if (this.transactionType === 'deposito') {
                        confirmMessage = '¿Confirmas que has recibido el depósito? Esta acción completará la transacción.';
                    } else {
                        confirmMessage = '¿Confirmas que has recibido el pago? Esta acción completará la transacción.';
                    }
                    
                    if (!confirm(confirmMessage)) {
                        return;
                    }

                    try {
                        const response = await fetch(`/transaction/${this.transactionId}/confirm-payment`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const result = await response.json();

                        if (!response.ok) {
                            throw new Error(result.message || 'Error al confirmar el pago');
                        }

                        alert('¡Transacción completada exitosamente!');
                        window.location.href = '/dashboard'; // Volver al dashboard

                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error: ' + error.message);
                    }
                }
            }
        }
    </script>
</x-app-layout>