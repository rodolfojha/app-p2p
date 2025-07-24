{{-- Chat simplificado con imágenes --}}
<x-app-layout>
    <div x-data="chatData()" class="bg-gray-100 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 py-6">
            
           {{-- Header mejorado --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ ucfirst($transaction->type) }} por ${{ number_format($transaction->amount, 2) }}
                    </h1>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full" 
                             :class="isConnected ? 'bg-green-500' : 'bg-red-500'"></div>
                        <span class="text-sm text-gray-600" 
                              x-text="isConnected ? 'Conectado' : 'Desconectado'"></span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Vendedor:</p>
                        <p class="font-semibold">{{ $transaction->initiator->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Cajero:</p>
                        <p class="font-semibold">{{ $transaction->participant->name }}</p>
                    </div>
                </div>

                {{-- Indicador del flujo --}}
                <div class="mt-4 p-3 rounded-lg bg-blue-50 border border-blue-200">
                    @if($transaction->type === 'deposito')
                        <p class="text-sm text-blue-700">
                            <strong>Flujo de Depósito:</strong> El vendedor realiza el pago → El cajero confirma recepción
                        </p>
                    @else
                        <p class="text-sm text-blue-700">
                            <strong>Flujo de Retiro:</strong> El cajero realiza el pago → El vendedor confirma recepción
                        </p>
                    @endif
                </div>
            </div>

            {{-- Chat --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <h2 class="text-lg font-bold mb-4">Chat</h2>
                
                {{-- Mensajes --}}
                <div class="h-80 overflow-y-auto border rounded-lg p-4 mb-4" id="messages">
                    <div class="space-y-3" id="messages-list">
                        @forelse($messages as $message)
                            <div class="flex {{ $message->user_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-xs px-4 py-2 rounded-lg {{ $message->user_id === Auth::id() ? 'bg-pink-500 text-white' : 'bg-gray-200' }}">
                                    <p class="text-xs mb-1">{{ $message->user->name }}</p>
                                    @if($message->content)
                                        <p class="text-sm">{{ $message->content }}</p>
                                    @endif
                                    @if($message->image_path)
                                        <div class="mt-2">
                                            <img src="{{ Storage::url($message->image_path) }}" 
                                                 alt="Comprobante" 
                                                 class="max-w-full h-auto rounded cursor-pointer"
                                                 @click="showImage('{{ Storage::url($message->image_path) }}')">
                                            <p class="text-xs mt-1">📎 Comprobante</p>
                                        </div>
                                    @endif
                                    <p class="text-xs mt-1">{{ $message->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500" id="no-messages">No hay mensajes</p>
                        @endforelse
                    </div>
                </div>

                {{-- Preview de imagen --}}
                <div x-show="selectedImage" class="mb-3 p-3 bg-gray-100 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <img :src="imagePreview" alt="Preview" class="w-16 h-16 object-cover rounded">
                            <p class="text-sm" x-text="imageName"></p>
                        </div>
                        <button @click="removeImage()" class="text-red-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Input --}}
                <form @submit.prevent="sendMessage()">
                    <div class="flex space-x-2">
                        <input type="text" x-model="newMessage" placeholder="Escribe un mensaje..." 
                               class="flex-1 px-4 py-2 border rounded-lg">
                        
                        <input type="file" id="image-input" accept="image/*" class="hidden" @change="selectImage($event)">
                        <label for="image-input" class="bg-gray-500 text-white px-3 py-2 rounded-lg cursor-pointer">
                            📷
                        </label>

                       <button type="submit" 
        :disabled="(!newMessage.trim() && !selectedImage) || isSending"
        class="bg-pink-600 text-white px-6 py-2 rounded-lg hover:bg-pink-700 transition-colors disabled:opacity-50">
    <span x-show="!isSending">Enviar</span>
    <span x-show="isSending">...</span>
</button>
                    </div>
                </form>
            </div>

            {{-- Botones --}}
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex justify-between">
                    @if($transaction->type === 'deposito')
                        @if(Auth::id() === $transaction->initiator_id && $transaction->status === 'accepted')
                            <button @click="markPayment()" class="bg-green-600 text-white px-6 py-3 rounded-lg">
                                💰 Pago Realizado
                            </button>
                        @elseif(Auth::id() === $transaction->participant_id && $transaction->status === 'payment_sent')
                            <button @click="confirmPayment()" class="bg-blue-600 text-white px-6 py-3 rounded-lg">
                                ✅ Confirmar
                            </button>
                        @endif
                    @elseif($transaction->type === 'retiro')
                        @if(Auth::id() === $transaction->participant_id && $transaction->status === 'accepted')
                            <button @click="markPayment()" class="bg-green-600 text-white px-6 py-3 rounded-lg">
                                💰 Pago Realizado
                            </button>
                        @elseif(Auth::id() === $transaction->initiator_id && $transaction->status === 'payment_sent')
                            <button @click="confirmPayment()" class="bg-blue-600 text-white px-6 py-3 rounded-lg">
                                ✅ Confirmar
                            </button>
                        @endif
                    @endif

                    <a href="{{ route('dashboard') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg">
                        Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de imagen --}}
    <div x-show="showModal" @click="closeModal()" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50" style="display: none;">
        <div class="relative p-4" @click.stop>
            <button @click="closeModal()" class="absolute top-2 right-2 text-white bg-black bg-opacity-50 rounded-full p-2">
                ✕
            </button>
            <img :src="modalImage" alt="Imagen" class="max-w-full max-h-screen rounded">
        </div>
    </div>

    <script>
       function chatData() {
            return {
                newMessage: '',
                selectedImage: null,
                imagePreview: null,
                imageName: '',
                showModal: false,
                modalImage: '',
                isConnected: false, // ✅ Agregar estado de conexión
                isSending: false, // ✅ Agregar estado de envío
                transactionId: {{ $transaction->id }},
                currentUserId: {{ Auth::id() }},

                init() {
                    this.setupPusher();
                    this.scrollToBottom();
                },

                setupPusher() {
                    const pusher = new Pusher('f1b3a9569a8bd0f48b63', {
                        cluster: 'sa1',
                        forceTLS: true
                    });

                    const channel = pusher.subscribe('transaction.chat.' + this.transactionId);
                    
                    // ✅ Listener mejorado para nuevos mensajes
                    channel.bind('new.message', (data) => {
                        console.log('💬 Nuevo mensaje recibido:', data);
                        if (data.user_id !== this.currentUserId) {
                            this.addMessage(data);
                        }
                    });

                    // ✅ Listener para confirmar suscripción
                    channel.bind('pusher:subscription_succeeded', () => {
                        console.log('✅ Conectado al chat en tiempo real');
                        this.isConnected = true;
                    });

                    // ✅ Estados de conexión
                    pusher.connection.bind('connected', () => {
                        console.log('✅ Pusher conectado');
                        this.isConnected = true;
                    });

                    pusher.connection.bind('disconnected', () => {
                        console.log('❌ Pusher desconectado');
                        this.isConnected = false;
                    });

                    // ✅ Canal para transacciones
                    const transactionChannel = pusher.subscribe('transaction-' + this.transactionId);

                    transactionChannel.bind('payment-sent', (data) => {
                        console.log('💰 Pago enviado:', data);
                        alert('Estado de transacción actualizado');
                        location.reload();
                    });

                    transactionChannel.bind('payment-confirmed', (data) => {
                        console.log('✅ Pago confirmado:', data);
                        alert('¡Transacción completada exitosamente!');
                        setTimeout(() => {
                            window.location.href = '/dashboard';
                        }, 2000);
                    });
                },

                addMessage(data) {
                    const messagesList = document.getElementById('messages-list');
                    const noMessages = document.getElementById('no-messages');
                    
                    if (noMessages) noMessages.remove();

                    const isMyMessage = data.user_id === this.currentUserId;
                    const imageHtml = data.image_url ? `
                        <div class="mt-2">
                            <img src="${data.image_url}" alt="Comprobante" class="max-w-full h-auto rounded cursor-pointer hover:opacity-80 transition-opacity" onclick="showImageModal('${data.image_url}')">
                            <p class="text-xs mt-1">📎 Comprobante</p>
                        </div>
                    ` : '';

                    const messageHtml = `
                        <div class="flex ${isMyMessage ? 'justify-end' : 'justify-start'}">
                            <div class="max-w-xs px-4 py-2 rounded-lg shadow-sm ${isMyMessage ? 'bg-pink-500 text-white' : 'bg-gray-200'}">
                                <p class="text-xs mb-1 font-medium">${data.user_name}</p>
                                ${data.content ? `<p class="text-sm">${data.content}</p>` : ''}
                                ${imageHtml}
                                <p class="text-xs mt-1 opacity-75">${data.created_at}</p>
                            </div>
                        </div>
                    `;
                    
                    messagesList.insertAdjacentHTML('beforeend', messageHtml);
                    this.scrollToBottom();
                },

                selectImage(event) {
                    const file = event.target.files[0];
                    if (file) {
                        // ✅ Validaciones mejoradas
                        if (!file.type.startsWith('image/')) {
                            alert('Por favor selecciona un archivo de imagen válido');
                            return;
                        }

                        if (file.size > 2 * 1024 * 1024) {
                            alert('La imagen debe ser menor a 2MB');
                            return;
                        }

                        this.selectedImage = file;
                        this.imageName = file.name;
                        
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.imagePreview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                },

                removeImage() {
                    this.selectedImage = null;
                    this.imagePreview = null;
                    this.imageName = '';
                    document.getElementById('image-input').value = '';
                },

                showImage(url) {
                    this.modalImage = url;
                    this.showModal = true;
                    document.body.style.overflow = 'hidden'; // ✅ Prevenir scroll
                },

                closeModal() {
                    this.showModal = false;
                    this.modalImage = '';
                    document.body.style.overflow = 'auto'; // ✅ Restaurar scroll
                },

                scrollToBottom() {
                    setTimeout(() => {
                        const container = document.getElementById('messages');
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    }, 100);
                },

                async sendMessage() {
                    if ((!this.newMessage.trim() && !this.selectedImage) || this.isSending) return;

                    this.isSending = true; // ✅ Activar estado de envío
                    const messageToSend = this.newMessage.trim();

                    const formData = new FormData();
                    if (messageToSend) {
                        formData.append('content', messageToSend);
                    }
                    if (this.selectedImage) {
                        formData.append('image', this.selectedImage);
                    }

                    try {
                        const response = await fetch(`/transaction/${this.transactionId}/send-message`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        if (!response.ok) {
                            throw new Error(result.message);
                        }

                        // Limpiar formulario primero
                        this.newMessage = '';
                        this.removeImage();

                        // ✅ Agregar mi mensaje inmediatamente
                        this.addMessage({
                            user_id: this.currentUserId,
                            user_name: '{{ Auth::user()->name }}',
                            content: messageToSend,
                            image_url: result.image_url,
                            created_at: 'ahora'
                        });

                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error al enviar mensaje: ' + error.message);
                    } finally {
                        this.isSending = false; // ✅ Desactivar estado de envío
                    }
                },

                async markPayment() {
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

                        alert('Pago marcado como realizado');
                        location.reload();
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error: ' + error.message);
                    }
                },

                async confirmPayment() {
                    if (confirm('¿Confirmas que has recibido el pago? Esta acción completará la transacción.')) {
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
                            window.location.href = '/dashboard';
                        } catch (error) {
                            console.error('Error:', error);
                            alert('Error: ' + error.message);
                        }
                    }
                }
            }
        }

        function showImageModal(url) {
            const component = document.querySelector('[x-data*="chatData"]');
            if (component && component._x_dataStack) {
                component._x_dataStack[0].showImage(url);
            }
        }
    </script>
</x-app-layout>