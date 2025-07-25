{{-- Chat con información bancaria completa --}}
<x-app-layout>
    <div x-data="chatData()" class="bg-gray-100 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 py-6">
            
           {{-- Header mejorado con información bancaria --}}
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
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Información de participantes --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Participantes</h3>
                        <div class="space-y-2">
                            <div>
                                <p class="text-sm text-gray-500">Vendedor:</p>
                                <p class="font-semibold text-gray-800">{{ $transaction->initiator->name }}</p>
                            </div>
                            @if($transaction->participant)
                                <div>
                                    <p class="text-sm text-gray-500">Cajero:</p>
                                    <p class="font-semibold text-gray-800">{{ $transaction->participant->name }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ✅ Información bancaria según el tipo de transacción --}}
@if($transaction->bank_name)
<div>
    @if($transaction->type === 'deposito')
        {{-- DEPÓSITO: Mostrar datos del cajero al vendedor --}}
        @if(Auth::id() === $transaction->initiator_id)
            <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                Datos para tu Depósito
            </h3>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 space-y-3">
                <p class="text-sm text-green-800 font-medium">
                    <strong>Realiza tu depósito a la siguiente cuenta del cajero:</strong>
                </p>
        @else
            <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                Tu Cuenta para Recibir el Depósito
            </h3>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 space-y-3">
                <p class="text-sm text-blue-800 font-medium">
                    <strong>El vendedor depositará a tu cuenta:</strong>
                </p>
        @endif
    @else
        {{-- RETIRO: Mostrar datos del vendedor al cajero --}}
        @if(Auth::id() === $transaction->participant_id)
            <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                Datos para la Transferencia
            </h3>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 space-y-3">
                <p class="text-sm text-yellow-800 font-medium">
                    <strong>Realiza la transferencia a la siguiente cuenta del vendedor:</strong>
                </p>
        @else
            <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                Tu Cuenta para Recibir la Transferencia
            </h3>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 space-y-3">
                <p class="text-sm text-green-800 font-medium">
                    <strong>El cajero transferirá a tu cuenta:</strong>
                </p>
        @endif
    @endif

    {{-- Información del banco --}}
    <div class="flex items-center space-x-3">
        @php
            $bankColors = [
                'nequi' => '#FF006E',
                'daviplata' => '#ED1C24', 
                'bancolombia' => '#FFC72C',
                'dale' => '#00A651'
            ];
            $bankColor = $bankColors[$transaction->bank_code] ?? '#6B7280';
        @endphp
        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold"
             style="background-color: {{ $bankColor }}">
            {{ substr($transaction->bank_name, 0, 1) }}
        </div>
        <div>
            <p class="font-semibold text-gray-800">{{ $transaction->bank_name }}</p>
            <p class="text-sm text-gray-600">{{ ucfirst($transaction->account_type) }}</p>
        </div>
    </div>
    
    {{-- Datos de la cuenta --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
        <div>
            <p class="text-gray-600">
                @if(in_array($transaction->account_type, ['nequi', 'daviplata']))
                    Número de teléfono:
                @else
                    Número de cuenta:
                @endif
            </p>
            <p class="font-semibold text-gray-800 font-mono">{{ $transaction->account_number }}</p>
        </div>
        
        <div>
            <p class="text-gray-600">WhatsApp:</p>
            <p class="font-semibold text-gray-800 font-mono">
                <a href="https://wa.me/57{{ $transaction->whatsapp_number }}" 
                   target="_blank" 
                   class="text-green-600 hover:text-green-700 flex items-center">
                    {{ $transaction->whatsapp_number }}
                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.108"/>
                    </svg>
                </a>
            </p>
        </div>
        
        <div>
            <p class="text-gray-600">Titular:</p>
            <p class="font-semibold text-gray-800">{{ $transaction->account_holder_name }}</p>
        </div>
        
        <div>
            <p class="text-gray-600">Identificación:</p>
            <p class="font-semibold text-gray-800 font-mono">{{ $transaction->account_holder_id }}</p>
        </div>
    </div>

    {{-- Botones de acción rápida --}}
    <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-200">
        <button @click="copyToClipboard('{{ $transaction->account_number }}')" 
                class="text-xs bg-blue-600 text-white px-3 py-1 rounded-full hover:bg-blue-700 transition-colors flex items-center">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            Copiar cuenta
        </button>
        
        <button @click="copyToClipboard('{{ $transaction->account_holder_name }}')" 
                class="text-xs bg-blue-600 text-white px-3 py-1 rounded-full hover:bg-blue-700 transition-colors flex items-center">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            Copiar nombre
        </button>
        
        <button @click="copyToClipboard('{{ number_format($transaction->amount, 2) }}')" 
                class="text-xs bg-green-600 text-white px-3 py-1 rounded-full hover:bg-green-700 transition-colors flex items-center">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            Copiar monto
        </button>
    </div>
    </div>
</div>
@endif
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

            {{-- ✅ Instrucciones según el tipo de transacción y usuario --}}
@if($transaction->bank_name && $transaction->status === 'accepted')

    {{-- DEPÓSITO: Instrucciones para el VENDEDOR (quien hace el depósito) --}}
    @if($transaction->type === 'deposito' && Auth::id() === $transaction->initiator_id)
    <div class="bg-green-50 border border-green-200 rounded-2xl shadow-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-green-800 mb-3 flex items-center">
            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Cómo Realizar tu Depósito
        </h3>
        
        <div class="bg-white rounded-lg p-4 border border-green-200">
            @if(in_array($transaction->account_type, ['nequi', 'daviplata']))
                {{-- Instrucciones para billeteras digitales --}}
                <div class="space-y-3">
                    <h4 class="font-semibold text-gray-800">Pasos para depositar en {{ $transaction->bank_name }}:</h4>
                    <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
                        <li>Abre la app de <strong>{{ $transaction->bank_name }}</strong></li>
                        <li>Selecciona <strong>"Enviar dinero"</strong> o <strong>"Transferir"</strong></li>
                        <li>Ingresa el número: <strong class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $transaction->account_number }}</strong></li>
                        <li>Verifica que el nombre sea: <strong>{{ $transaction->account_holder_name }}</strong></li>
                        <li>Ingresa el monto: <strong class="text-green-600">${{ number_format($transaction->amount, 2) }}</strong></li>
                        <li>Confirma la transferencia</li>
                        <li>Toma captura de pantalla del comprobante</li>
                        <li>Sube la imagen aquí y marca como <strong>"Pago Realizado"</strong></li>
                    </ol>
                </div>
            @else
                {{-- Instrucciones para bancos tradicionales --}}
                <div class="space-y-3">
                    <h4 class="font-semibold text-gray-800">Pasos para depositar en {{ $transaction->bank_name }}:</h4>
                    <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
                        <li>Ingresa a tu app bancaria o PSE</li>
                        <li>Selecciona <strong>"Transferencias"</strong></li>
                        <li>Elige <strong>{{ $transaction->bank_name }}</strong> como banco destino</li>
                        <li>Tipo de cuenta: <strong>{{ ucfirst($transaction->account_type) }}</strong></li>
                        <li>Número de cuenta: <strong class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $transaction->account_number }}</strong></li>
                        <li>Verifica que el titular sea: <strong>{{ $transaction->account_holder_name }}</strong></li>
                        <li>Ingresa el monto: <strong class="text-green-600">${{ number_format($transaction->amount, 2) }}</strong></li>
                        <li>Confirma la transferencia</li>
                        <li>Toma captura del comprobante</li>
                        <li>Sube la imagen aquí y marca como <strong>"Pago Realizado"</strong></li>
                    </ol>
                </div>
            @endif
            
            <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-sm text-blue-800">
                    <strong>💡 Tip:</strong> Puedes contactar al cajero vía WhatsApp: 
                    <a href="https://wa.me/57{{ $transaction->whatsapp_number }}" 
                       target="_blank" 
                       class="text-green-600 hover:text-green-700 font-semibold">
                        {{ $transaction->whatsapp_number }}
                    </a>
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- RETIRO: Instrucciones para el CAJERO (quien hace la transferencia) --}}
    @if($transaction->type === 'retiro' && Auth::id() === $transaction->participant_id)
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl shadow-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-yellow-800 mb-3 flex items-center">
            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Instrucciones para la Transferencia
        </h3>
        
        <div class="bg-white rounded-lg p-4 border border-yellow-200">
            @if(in_array($transaction->account_type, ['nequi', 'daviplata']))
                {{-- Instrucciones para billeteras digitales --}}
                <div class="space-y-3">
                    <h4 class="font-semibold text-gray-800">Pasos para transferir a {{ $transaction->bank_name }}:</h4>
                    <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
                        <li>Abre la app de <strong>{{ $transaction->bank_name }}</strong></li>
                        <li>Selecciona <strong>"Enviar dinero"</strong> o <strong>"Transferir"</strong></li>
                        <li>Ingresa el número: <strong class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $transaction->account_number }}</strong></li>
                        <li>Verifica que el nombre sea: <strong>{{ $transaction->account_holder_name }}</strong></li>
                        <li>Ingresa el monto: <strong class="text-green-600">${{ number_format($transaction->amount, 2) }}</strong></li>
                        <li>Confirma la transferencia</li>
                        <li>Toma captura de pantalla del comprobante</li>
                        <li>Sube la imagen aquí y marca como <strong>"Pago Realizado"</strong></li>
                    </ol>
                </div>
            @else
                {{-- Instrucciones para bancos tradicionales --}}
                <div class="space-y-3">
                    <h4 class="font-semibold text-gray-800">Pasos para transferir a {{ $transaction->bank_name }}:</h4>
                    <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
                        <li>Ingresa a tu app bancaria o PSE</li>
                        <li>Selecciona <strong>"Transferencias"</strong></li>
                        <li>Elige <strong>{{ $transaction->bank_name }}</strong> como banco destino</li>
                        <li>Tipo de cuenta: <strong>{{ ucfirst($transaction->account_type) }}</strong></li>
                        <li>Número de cuenta: <strong class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $transaction->account_number }}</strong></li>
                        <li>Verifica que el titular sea: <strong>{{ $transaction->account_holder_name }}</strong></li>
                        <li>Ingresa el monto: <strong class="text-green-600">${{ number_format($transaction->amount, 2) }}</strong></li>
                        <li>Confirma la transferencia</li>
                        <li>Toma captura del comprobante</li>
                        <li>Sube la imagen aquí y marca como <strong>"Pago Realizado"</strong></li>
                    </ol>
                </div>
            @endif
            
            <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-sm text-blue-800">
                    <strong>💡 Tip:</strong> Puedes contactar al vendedor vía WhatsApp: 
                    <a href="https://wa.me/57{{ $transaction->whatsapp_number }}" 
                       target="_blank" 
                       class="text-green-600 hover:text-green-700 font-semibold">
                        {{ $transaction->whatsapp_number }}
                    </a>
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Información para quien recibe el dinero --}}
    @if(($transaction->type === 'deposito' && Auth::id() === $transaction->participant_id) || 
        ($transaction->type === 'retiro' && Auth::id() === $transaction->initiator_id))
    <div class="bg-blue-50 border border-blue-200 rounded-2xl shadow-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-blue-800 mb-3 flex items-center">
            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
            @if($transaction->type === 'deposito')
                Esperando tu Depósito
            @else
                Esperando tu Transferencia
            @endif
        </h3>
        
        <div class="bg-white rounded-lg p-4 border border-blue-200">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-gray-600 font-medium">Banco:</p>
                    <p class="text-gray-800 font-semibold">{{ $transaction->bank_name }}</p>
                </div>
                
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-gray-600 font-medium">Tu cuenta:</p>
                    <p class="text-gray-800 font-semibold font-mono">{{ $transaction->account_number }}</p>
                </div>
                
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-gray-600 font-medium">Monto esperado:</p>
                    <p class="text-green-600 font-bold text-lg">${{ number_format($transaction->amount, 2) }}</p>
                </div>
                
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-gray-600 font-medium">Estado:</p>
                    <p class="text-blue-600 font-semibold">
                        @if($transaction->type === 'deposito')
                            Esperando depósito del vendedor
                        @else
                            Esperando transferencia del cajero
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="mt-4 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                <p class="text-sm text-yellow-800">
                    <strong>📱 Mantente atento:</strong> Revisa tu app bancaria o billetera para confirmar cuando recibas 
                    @if($transaction->type === 'deposito')
                        el depósito.
                    @else
                        la transferencia.
                    @endif
                </p>
            </div>
        </div>
    </div>
    @endif

@endif

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
            isConnected: false,
            isSending: false,
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
                
                channel.bind('new.message', (data) => {
                    console.log('💬 Nuevo mensaje recibido:', data);
                    if (data.user_id !== this.currentUserId) {
                        this.addMessage(data);
                    }
                });

                channel.bind('pusher:subscription_succeeded', () => {
                    console.log('✅ Conectado al chat en tiempo real');
                    this.isConnected = true;
                });

                pusher.connection.bind('connected', () => {
                    console.log('✅ Pusher conectado');
                    this.isConnected = true;
                });

                pusher.connection.bind('disconnected', () => {
                    console.log('❌ Pusher desconectado');
                    this.isConnected = false;
                });

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
                document.body.style.overflow = 'hidden';
            },

            closeModal() {
                this.showModal = false;
                this.modalImage = '';
                document.body.style.overflow = 'auto';
            },

            // ✅ NUEVA: Función para copiar al portapapeles
            async copyToClipboard(text) {
                try {
                    await navigator.clipboard.writeText(text);
                    this.showNotification('Copiado al portapapeles: ' + text);
                } catch (err) {
                    console.error('Error al copiar:', err);
                    
                    // Fallback para navegadores que no soportan clipboard API
                    const textArea = document.createElement('textarea');
                    textArea.value = text;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    
                    this.showNotification('Copiado: ' + text);
                }
            },

            // ✅ NUEVA: Mostrar notificación temporal
            showNotification(message) {
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity duration-300';
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        if (document.body.contains(notification)) {
                            document.body.removeChild(notification);
                        }
                    }, 300);
                }, 3000);
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

                this.isSending = true;
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

                    // Agregar mi mensaje inmediatamente
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
                    this.isSending = false;
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