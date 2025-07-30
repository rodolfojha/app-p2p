{{-- Reportes de Comisiones - Panel Administrativo --}}
<x-app-layout>
    <div x-data="commissionReportsData()" 
         :class="{'dark': darkMode === true}"
         class="bg-gray-100 dark:bg-gray-900 min-h-screen">

        <div class="max-w-7xl mx-auto px-4 py-6">
            
            {{-- Header --}}
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Reportes de Comisiones</h1>
                    <p class="text-gray-600 dark:text-gray-400">Análisis detallado de ganancias y distribución</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Volver al Dashboard</span>
                </a>
            </div>

            {{-- Filtros de Fecha --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Filtros de Período</h2>
                <form method="GET" action="{{ route('admin.commission-reports') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha Inicio</label>
                        <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha Fin</label>
                        <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                            Filtrar
                        </button>
                    </div>
                    <div class="flex items-end">
                        <button type="button" @click="setQuickFilter('month')" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                            Este Mes
                        </button>
                    </div>
                </form>
            </div>

            {{-- Resumen de Ganancias del Administrador --}}
            <div class="bg-gradient-to-r from-green-500 to-blue-600 rounded-2xl shadow-lg p-6 mb-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold mb-2">Mis Ganancias por Comisiones</h2>
                        <p class="text-green-100">Período: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-green-100 text-sm">Total Ganado</p>
                        <p class="text-4xl font-bold">${{ number_format($adminCommissions, 2) }}</p>
                    </div>
                </div>
            </div>

            {{-- Estadísticas por Tipo de Transacción --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                {{-- Comisiones por Depósitos --}}
                @php
                    $depositCommissions = \App\Models\Transaction::where('status', 'completed')
                        ->where('type', 'deposito')
                        ->where('admin_id', Auth::id())
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->sum('admin_commission');
                    
                    $depositCount = \App\Models\Transaction::where('status', 'completed')
                        ->where('type', 'deposito')
                        ->where('admin_id', Auth::id())
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->count();
                @endphp
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Comisiones Depósitos</h3>
                        <div class="p-2 bg-green-100 dark:bg-green-900 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Ganado</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">${{ number_format($depositCommissions, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Transacciones</p>
                            <p class="text-lg font-semibold text-gray-600 dark:text-gray-300">{{ $depositCount }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Promedio por Transacción</p>
                            <p class="text-lg font-semibold text-gray-600 dark:text-gray-300">
                                ${{ $depositCount > 0 ? number_format($depositCommissions / $depositCount, 2) : '0.00' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Comisiones por Retiros --}}
                @php
                    $withdrawalCommissions = \App\Models\Transaction::where('status', 'completed')
                        ->where('type', 'retiro')
                        ->where('admin_id', Auth::id())
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->sum('admin_commission');
                    
                    $withdrawalCount = \App\Models\Transaction::where('status', 'completed')
                        ->where('type', 'retiro')
                        ->where('admin_id', Auth::id())
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->count();
                @endphp
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Comisiones Retiros</h3>
                        <div class="p-2 bg-red-100 dark:bg-red-900 rounded-full">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Total Ganado</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">${{ number_format($withdrawalCommissions, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Transacciones</p>
                            <p class="text-lg font-semibold text-gray-600 dark:text-gray-300">{{ $withdrawalCount }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Promedio por Transacción</p>
                            <p class="text-lg font-semibold text-gray-600 dark:text-gray-300">
                                ${{ $withdrawalCount > 0 ? number_format($withdrawalCommissions / $withdrawalCount, 2) : '0.00' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top Usuarios por Comisiones Generadas --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                {{-- Top Vendedores --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Top Vendedores (Comisiones Generadas)</h3>
                    <div class="space-y-4">
                        @php
                            $topSellers = \App\Models\User::where('role', 'vendedor')
                                ->withSum(['initiatedTransactions' => function($query) use ($startDate, $endDate) {
                                    $query->where('status', 'completed')
                                          ->where('admin_id', Auth::id())
                                          ->whereBetween('created_at', [$startDate, $endDate]);
                                }], 'admin_commission')
                                ->having('initiated_transactions_sum_admin_commission', '>', 0)
                                ->orderBy('initiated_transactions_sum_admin_commission', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp
                        @forelse($topSellers as $index => $seller)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-bold text-green-600">{{ $index + 1 }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $seller->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $seller->email }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-800 dark:text-white">
                                    ${{ number_format($seller->initiated_transactions_sum_admin_commission ?? 0, 2) }}
                                </p>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">No hay datos para mostrar</p>
                        @endforelse
                    </div>
                </div>

                {{-- Top Cajeros --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Top Cajeros (Comisiones Generadas)</h3>
                    <div class="space-y-4">
                        @php
                            $topCashiers = \App\Models\User::where('role', 'cashier')
                                ->withSum(['participatedTransactions' => function($query) use ($startDate, $endDate) {
                                    $query->where('status', 'completed')
                                          ->where('admin_id', Auth::id())
                                          ->whereBetween('created_at', [$startDate, $endDate]);
                                }], 'admin_commission')
                                ->having('participated_transactions_sum_admin_commission', '>', 0)
                                ->orderBy('participated_transactions_sum_admin_commission', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp
                        @forelse($topCashiers as $index => $cashier)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-bold text-purple-600">{{ $index + 1 }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $cashier->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $cashier->email }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-800 dark:text-white">
                                    ${{ number_format($cashier->participated_transactions_sum_admin_commission ?? 0, 2) }}
                                </p>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">No hay datos para mostrar</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Detalle de Comisiones por Usuario --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Detalle de Comisiones por Usuario</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Usuario</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Rol</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Como Vendedor</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Como Cajero</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Como Referido</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Total Ganado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($userCommissions as $user)
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="py-3 px-4">
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-white">{{ $user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role === 'vendedor' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-sm font-medium text-gray-900 dark:text-white">
                                    ${{ number_format($user->initiated_transactions_sum_seller_commission ?? 0, 2) }}
                                </td>
                                <td class="py-3 px-4 text-sm font-medium text-gray-900 dark:text-white">
                                    ${{ number_format($user->participated_transactions_sum_cashier_commission ?? 0, 2) }}
                                </td>
                                <td class="py-3 px-4 text-sm font-medium text-gray-900 dark:text-white">
                                    ${{ number_format($user->referral_transactions_sum_referral_commission ?? 0, 2) }}
                                </td>
                                <td class="py-3 px-4 text-sm font-bold text-green-600">
                                    @php
                                        $totalEarned = ($user->initiated_transactions_sum_seller_commission ?? 0) + 
                                                      ($user->participated_transactions_sum_cashier_commission ?? 0) + 
                                                      ($user->referral_transactions_sum_referral_commission ?? 0);
                                    @endphp
                                    ${{ number_format($totalEarned, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                        <p class="text-gray-500 dark:text-gray-400 text-lg">No hay comisiones en este período</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Botones de Acción --}}
            <div class="flex justify-end space-x-3">
                <button @click="exportReport()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Exportar Reporte</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        function commissionReportsData() {
            return {
                darkMode: localStorage.getItem('darkMode') === 'true',

                init() {
                    this.$watch('darkMode', val => localStorage.setItem('darkMode', val));
                },

                setQuickFilter(period) {
                    const form = document.querySelector('form');
                    const startDateInput = form.querySelector('input[name="start_date"]');
                    const endDateInput = form.querySelector('input[name="end_date"]');
                    
                    const now = new Date();
                    
                    if (period === 'month') {
                        // Primer día del mes actual
                        const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
                        startDateInput.value = startOfMonth.toISOString().split('T')[0];
                        endDateInput.value = now.toISOString().split('T')[0];
                        
                        form.submit();
                    }
                },

                async exportReport() {
                    try {
                        alert('Función de exportación en desarrollo');
                        // Aquí implementarías la lógica de exportación
                    } catch (error) {
                        console.error('Error al exportar:', error);
                        alert('Error al exportar el reporte');
                    }
                }
            }
        }
    </script>
</x-app-layout>