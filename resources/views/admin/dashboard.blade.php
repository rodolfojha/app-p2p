{{-- Dashboard Administrativo - MultiPagos --}}
<x-app-layout>
    <div x-data="adminDashboardData()" 
         :class="{'dark': darkMode === true}"
         class="bg-gray-100 dark:bg-gray-900 min-h-screen">

        <div class="flex">
            {{-- Sidebar Administrativo --}}
            <aside class="hidden md:flex md:flex-col md:w-64 bg-white dark:bg-gray-800 shadow-lg min-h-screen">
                <div class="px-6 py-4 border-b dark:border-gray-700">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Admin Panel</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">MultiPagos</p>
                </div>
                <nav class="flex-1 px-4 py-4 space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="nav-item nav-item-active">
                        <svg class="nav-icon text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.users') }}" class="nav-item">
                        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        <span>Usuarios</span>
                    </a>
                    <a href="{{ route('admin.transactions') }}" class="nav-item">
                        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span>Transacciones</span>
                    </a>
                    <a href="{{ route('admin.settings') }}" class="nav-item">
                        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Configuración</span>
                    </a>
                    {{-- ✅ NUEVA OPCIÓN: Reportes de Comisiones --}}
                    <a href="{{ route('admin.commission-reports') }}" class="nav-item">
                        <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span>Reportes</span>
                    </a>
                </nav>
            </aside>

            {{-- Contenido Principal --}}
            <div class="flex-1">
                <div class="max-w-7xl mx-auto px-4 py-6">
                    
                    {{-- Header --}}
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Dashboard Administrativo</h1>
                            <p class="text-gray-600 dark:text-gray-400">Resumen general del sistema MultiPagos</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button @click="refreshData()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h5M20 20v-5h-5M4 20l5-5M20 4l-5 5"/>
                                </svg>
                                <span>Actualizar</span>
                            </button>
                            <button @click="exportData()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Exportar</span>
                            </button>
                        </div>
                    </div>

                    {{-- ✅ NUEVAS ESTADÍSTICAS DE GANANCIAS DEL ADMINISTRADOR --}}
                    <div class="bg-gradient-to-r from-green-500 to-blue-600 rounded-2xl shadow-lg p-6 mb-8 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-bold mb-2">Mis Ganancias por Comisiones</h2>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <p class="text-green-100 text-sm">Total Acumulado</p>
                                        <p class="text-2xl font-bold">${{ number_format($adminCommissionStats['total_admin_earnings'], 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-green-100 text-sm">Hoy</p>
                                        <p class="text-xl font-semibold">${{ number_format($adminCommissionStats['admin_earnings_today'], 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-green-100 text-sm">Este Mes</p>
                                        <p class="text-xl font-semibold">${{ number_format($adminCommissionStats['admin_earnings_this_month'], 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-green-100 text-sm">Mes Anterior</p>
                                        <p class="text-xl font-semibold">${{ number_format($adminCommissionStats['admin_earnings_last_month'], 2) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="hidden md:block">
                                <svg class="w-16 h-16 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Alertas del Sistema --}}
                    @if(count($alerts) > 0)
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">Alertas del Sistema</h2>
                        <div class="space-y-3">
                            @foreach($alerts as $alert)
                            <div class="border-l-4 p-4 rounded-lg {{ $alert['type'] === 'warning' ? 'border-yellow-400 bg-yellow-50 dark:bg-yellow-900/20' : 'border-blue-400 bg-blue-50 dark:bg-blue-900/20' }}">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-semibold {{ $alert['type'] === 'warning' ? 'text-yellow-800 dark:text-yellow-200' : 'text-blue-800 dark:text-blue-200' }}">
                                            {{ $alert['title'] }}
                                        </h3>
                                        <p class="{{ $alert['type'] === 'warning' ? 'text-yellow-700 dark:text-yellow-300' : 'text-blue-700 dark:text-blue-300' }}">
                                            {{ $alert['message'] }}
                                        </p>
                                    </div>
                                    <a href="{{ $alert['url'] }}" class="text-sm font-medium {{ $alert['type'] === 'warning' ? 'text-yellow-800 hover:text-yellow-900' : 'text-blue-800 hover:text-blue-900' }} underline">
                                        {{ $alert['action'] }}
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Estadísticas Principales --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        {{-- Total Transacciones --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Transacciones</p>
                                    <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ number_format($stats['total_transactions']) }}</p>
                                    <p class="text-sm text-green-600 dark:text-green-400">+{{ $stats['transactions_today'] }} hoy</p>
                                </div>
                                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Volumen Total --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Volumen Total</p>
                                    <p class="text-3xl font-bold text-gray-800 dark:text-white">${{ number_format($stats['total_volume'], 2) }}</p>
                                    <p class="text-sm text-green-600 dark:text-green-400">${{ number_format($stats['volume_today'], 2) }} hoy</p>
                                </div>
                                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Comisiones --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Comisiones</p>
                                    <p class="text-3xl font-bold text-gray-800 dark:text-white">${{ number_format($stats['total_commissions'], 2) }}</p>
                                    <p class="text-sm text-green-600 dark:text-green-400">${{ number_format($stats['commissions_today'], 2) }} hoy</p>
                                </div>
                                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 0a3 3 0 110 6H9l3 3-3-3h1m-1 0h6m-5 0a3 3 0 110 6H9l3 3-3-3h1m-1 0h6"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Usuarios Activos --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Usuarios Totales</p>
                                    <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ number_format($userStats['total_users']) }}</p>
                                    <p class="text-sm text-blue-600 dark:text-blue-400">{{ $userStats['active_sellers'] + $userStats['active_cashiers'] }} activos</p>
                                </div>
                                <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ✅ NUEVA SECCIÓN: Análisis de Comisiones por Tipo --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        {{-- Comisiones por Tipo de Transacción --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Mis Comisiones por Tipo</h3>
                            <div class="space-y-4">
                                @foreach($adminCommissionStats['commission_by_type'] as $typeData)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 {{ $typeData->type === 'deposito' ? 'bg-green-500' : 'bg-blue-500' }} rounded-full"></div>
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-white">{{ ucfirst($typeData->type) }}</p>
                                            <p class="text-sm text-gray-500">{{ $typeData->count }} transacciones</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-gray-800 dark:text-white">${{ number_format($typeData->total_commission, 2) }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Evolución Mensual de Comisiones --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Evolución Mensual (Últimos 6 meses)</h3>
                            <div class="space-y-3">
                                @foreach($adminCommissionStats['monthly_commissions'] as $monthData)
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-white">
                                            {{ \Carbon\Carbon::createFromDate($monthData->year, $monthData->month, 1)->format('M Y') }}
                                        </p>
                                        <p class="text-sm text-gray-500">{{ $monthData->transaction_count }} transacciones</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-gray-800 dark:text-white">${{ number_format($monthData->total_commission, 2) }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Estados de Transacciones --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Estados de Transacciones</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Pendientes</span>
                                    </div>
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ $stats['pending_transactions'] }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">En Proceso</span>
                                    </div>
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ $stats['active_transactions'] }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Completadas</span>
                                    </div>
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ $stats['completed_transactions'] }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Distribución de Usuarios --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Distribución de Usuarios</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Vendedores</span>
                                    </div>
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ $userStats['sellers_count'] }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Cajeros</span>
                                    </div>
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ $userStats['cashiers_count'] }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Administradores</span>
                                    </div>
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ $userStats['admins_count'] }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Métodos de Pago --}}
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Métodos de Pago</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Cajeros con métodos</span>
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ $userStats['cashiers_with_payment_methods'] }}/{{ $userStats['cashiers_count'] }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Total métodos activos</span>
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ $userStats['total_payment_methods'] }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    @php
                                        $percentage = $userStats['cashiers_count'] > 0 ? ($userStats['cashiers_with_payment_methods'] / $userStats['cashiers_count']) * 100 : 0;
                                    @endphp
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500">{{ number_format($percentage, 1) }}% cajeros configurados</p>
                            </div>
                        </div>
                    </div>

                    {{-- Transacciones Recientes --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Transacciones Recientes</h3>
                            <a href="{{ route('admin.transactions') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Ver todas</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">ID</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Tipo</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Monto</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Vendedor</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Cajero</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Mi Comisión</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Estado</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $transaction)
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <td class="py-3 px-4 text-sm font-medium text-gray-900 dark:text-white">#{{ $transaction->id }}</td>
                                        <td class="py-3 px-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transaction->type === 'deposito' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($transaction->type) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-sm font-medium text-gray-900 dark:text-white">${{ number_format($transaction->amount, 2) }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ $transaction->initiator->name }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $transaction->participant ? $transaction->participant->name : 'Sin asignar' }}
                                        </td>
                                        <td class="py-3 px-4 text-sm font-medium text-green-600 dark:text-green-400">
                                            ${{ number_format($transaction->admin_commission ?? 0, 2) }}
                                        </td>
                                        <td class="py-3 px-4">
                                            @php
                                                $statusColors = [
                                                    'pending_acceptance' => 'bg-yellow-100 text-yellow-800',
                                                    'accepted' => 'bg-blue-100 text-blue-800',
                                                    'payment_sent' => 'bg-purple-100 text-purple-800',
                                                    'completed' => 'bg-green-100 text-green-800'
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$transaction->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ str_replace('_', ' ', ucfirst($transaction->status)) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Top Performers --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Top Cajeros --}}
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Top Cajeros por Volumen</h3>
                            <div class="space-y-4">
                                @foreach($chartData['top_cashiers'] as $index => $cashier)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-bold text-purple-600">{{ $index + 1 }}</span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-white">{{ $cashier->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $cashier->participated_transactions_count }} transacciones</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-800 dark:text-white">${{ number_format($cashier->participated_transactions_sum_amount ?? 0, 2) }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Top Vendedores --}}
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Top Vendedores por Volumen</h3>
                            <div class="space-y-4">
                                @foreach($chartData['top_sellers'] as $index => $seller)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-bold text-green-600">{{ $index + 1 }}</span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-white">{{ $seller->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $seller->initiated_transactions_count }} transacciones</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-800 dark:text-white">${{ number_format($seller->initiated_transactions_sum_amount ?? 0, 2) }}</p>
                                    </div>
                                </div>
                                @endforeach
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
            @apply h-5 w-5 mr-3;
        }
    </style>

    {{-- JavaScript para funcionalidad admin --}}
    <script>
        function adminDashboardData() {
            return {
                darkMode: localStorage.getItem('darkMode') === 'true',

                init() {
                    this.$watch('darkMode', val => localStorage.setItem('darkMode', val));
                    console.log('Admin Dashboard cargado');
                },

                async refreshData() {
                    try {
                        // Mostrar indicador de carga
                        const button = event.target.closest('button');
                        const originalText = button.innerHTML;
                        button.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Actualizando...';
                        
                        // Simular actualización (puedes hacer una petición AJAX real aquí)
                        await new Promise(resolve => setTimeout(resolve, 1000));
                        
                        // Recargar la página para obtener datos frescos
                        window.location.reload();
                        
                    } catch (error) {
                        console.error('Error al actualizar:', error);
                        alert('Error al actualizar los datos');
                    }
                },

                async exportData() {
                    try {
                        // Aquí implementarías la lógica de exportación
                        alert('Función de exportación en desarrollo');
                        
                        // Ejemplo de exportación CSV
                        // const response = await fetch('/admin/export', {
                        //     method: 'POST',
                        //     headers: {
                        //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        //         'Accept': 'application/json'
                        //     }
                        // });
                        
                        // if (response.ok) {
                        //     const blob = await response.blob();
                        //     const url = window.URL.createObjectURL(blob);
                        //     const a = document.createElement('a');
                        //     a.href = url;
                        //     a.download = 'reporte_multipagos.csv';
                        //     a.click();
                        // }
                        
                    } catch (error) {
                        console.error('Error al exportar:', error);
                        alert('Error al exportar los datos');
                    }
                }
            }
        }
    </script>
</x-app-layout>