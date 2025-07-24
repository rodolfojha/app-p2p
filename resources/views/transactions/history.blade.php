{{-- Historial de transacciones --}}
<x-app-layout>
    <div x-data="transactionHistoryData()" 
         :class="{'dark': darkMode === true}"
         class="bg-gray-100 dark:bg-gray-900 min-h-screen">

        <div class="max-w-7xl mx-auto px-4 py-6">
            
            {{-- Header --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Historial de Transacciones</h1>
                        <p class="text-gray-600 dark:text-gray-400">Revisa todas tus transacciones y estadísticas</p>
                    </div>
                    <a href="{{ route('transactions.create') }}" 
                       class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-3 px-6 rounded-lg transition-colors flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Nueva Transacción</span>
                    </a>
                </div>
            </div>

            {{-- Estadísticas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Transacciones</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_transactions'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Completadas</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['completed_transactions'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Volumen Total</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($stats['total_volume'], 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Comisiones Ganadas</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($stats['total_commissions_earned'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filtros --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Filtros</h2>
                <form method="GET" action="{{ route('transactions.history') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estado</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500">
                            <option value="">Todos</option>
                            <option value="pending_acceptance" {{ request('status') === 'pending_acceptance' ? 'selected' : '' }}>Pendiente</option>
                            <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Aceptada</option>
                            <option value="payment_sent" {{ request('status') === 'payment_sent' ? 'selected' : '' }}>Pago Enviado</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completada</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo</label>
                        <select name="type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500">
                            <option value="">Todos</option>
                            <option value="deposito" {{ request('type') === 'deposito' ? 'selected' : '' }}>Depósito</option>
                            <option value="retiro" {{ request('type') === 'retiro' ? 'selected' : '' }}>Retiro</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Desde</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hasta</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500">
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>

            {{-- Lista de transacciones --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white">Transacciones</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Monto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Comisión</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Participantes</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($transactions as $transaction)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        #{{ $transaction->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transaction->type === 'deposito' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        ${{ number_format($transaction->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        ${{ number_format($transaction->total_commission, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'pending_acceptance' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                'accepted' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                'payment_sent' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                                'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$transaction->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ str_replace('_', ' ', ucfirst($transaction->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        <div class="flex flex-col space-y-1">
                                            <span class="text-xs text-gray-500">Vendedor: {{ $transaction->initiator->name }}</span>
                                            @if($transaction->participant)
                                                <span class="text-xs text-gray-500">Cajero: {{ $transaction->participant->name }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $transaction->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if(in_array($transaction->status, ['accepted', 'payment_sent']) && $transaction->participant_id)
                                            <a href="{{ route('transaction.chat', $transaction) }}" 
                                               class="text-pink-600 hover:text-pink-900 dark:text-pink-400 dark:hover:text-pink-300">
                                                Ver Chat
                                            </a>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400 text-lg">No hay transacciones</p>
                                            <p class="text-gray-400 dark:text-gray-500 text-sm">Crea tu primera transacción para comenzar</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                @if($transactions->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function transactionHistoryData() {
            return {
                darkMode: localStorage.getItem('darkMode') === 'true',

                init() {
                    this.$watch('darkMode', val => localStorage.setItem('darkMode', val));
                }
            }
        }
    </script>
</x-app-layout>