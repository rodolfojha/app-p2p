{{-- 
    Dashboard Principal para el rol de Cajero.
    Muestra las solicitudes disponibles y las que ya ha aceptado.
--}}

<x-app-layout>
    <div x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" 
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

                    {{-- Sección de Solicitudes Disponibles --}}
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Solicitudes Disponibles</h2>
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                            <div class="space-y-4">
                                @forelse ($availableTransactions as $transaction)
                                    <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <div class="flex items-center space-x-4">
                                            <div class="p-2 rounded-full {{ $transaction->type == 'deposito' ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }}">
                                                @if($transaction->type == 'deposito')
                                                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                @else
                                                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800 dark:text-white">
                                                    Solicitud de {{ $transaction->type }} por ${{ number_format($transaction->amount, 2) }}
                                                </p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    Iniciada por: {{ $transaction->initiator->name }}
                                                </p>
                                            </div>
                                        </div>
                                        {{-- Este botón será el siguiente paso a implementar --}}
                                        <button class="bg-pink-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-pink-700 transition-colors">
                                            Aceptar
                                        </button>
                                    </div>
                                @empty
                                    <p class="text-center text-gray-500 dark:text-gray-400">No hay solicitudes disponibles en este momento.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Aquí podrías añadir la lista de transacciones ya aceptadas por este cajero --}}

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
