{{-- Configuración del Sistema - Panel Administrativo --}}
<x-app-layout>
    <div x-data="adminSettingsData()" 
         :class="{'dark': darkMode === true}"
         class="bg-gray-100 dark:bg-gray-900 min-h-screen">

        <div class="max-w-7xl mx-auto px-4 py-6">
            
            {{-- Header --}}
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Configuración del Sistema</h1>
                    <p class="text-gray-600 dark:text-gray-400">Gestiona las comisiones y configuraciones generales</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Volver al Dashboard</span>
                </a>
            </div>

            {{-- Mensajes de feedback --}}
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
            @endif

            @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h4 class="font-medium">Errores encontrados:</h4>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            {{-- Configuración Actual de Comisiones --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                {{-- Configuración Depósitos --}}
                @php
                    $depositoSettings = $commissionSettings->where('type', 'deposito')->where('is_active', true)->first();
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Comisiones Depósitos</h2>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                            {{ $depositoSettings ? $depositoSettings->total_percentage : 0 }}% Total
                        </span>
                    </div>
                    
                    @if($depositoSettings)
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-gray-600 dark:text-gray-400">Administrador</span>
                            <span class="font-semibold text-gray-800 dark:text-white">{{ $depositoSettings->admin_percentage }}%</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-gray-600 dark:text-gray-400">Cajero</span>
                            <span class="font-semibold text-gray-800 dark:text-white">{{ $depositoSettings->cashier_percentage }}%</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-gray-600 dark:text-gray-400">Vendedor</span>
                            <span class="font-semibold text-gray-800 dark:text-white">{{ $depositoSettings->seller_percentage }}%</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-gray-600 dark:text-gray-400">Referido</span>
                            <span class="font-semibold text-gray-800 dark:text-white">{{ $depositoSettings->referral_percentage }}%</span>
                        </div>
                    </div>
                    @else
                    <p class="text-gray-500 dark:text-gray-400">No hay configuración activa</p>
                    @endif
                    
                    <button @click="showEditModal('deposito')" class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                        Modificar Comisiones
                    </button>
                </div>

                {{-- Configuración Retiros --}}
                @php
                    $retiroSettings = $commissionSettings->where('type', 'retiro')->where('is_active', true)->first();
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Comisiones Retiros</h2>
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                            {{ $retiroSettings ? $retiroSettings->total_percentage : 0 }}% Total
                        </span>
                    </div>
                    
                    @if($retiroSettings)
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-gray-600 dark:text-gray-400">Administrador</span>
                            <span class="font-semibold text-gray-800 dark:text-white">{{ $retiroSettings->admin_percentage }}%</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-gray-600 dark:text-gray-400">Cajero</span>
                            <span class="font-semibold text-gray-800 dark:text-white">{{ $retiroSettings->cashier_percentage }}%</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-gray-600 dark:text-gray-400">Vendedor</span>
                            <span class="font-semibold text-gray-800 dark:text-white">{{ $retiroSettings->seller_percentage }}%</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-gray-600 dark:text-gray-400">Referido</span>
                            <span class="font-semibold text-gray-800 dark:text-white">{{ $retiroSettings->referral_percentage }}%</span>
                        </div>
                    </div>
                    @else
                    <p class="text-gray-500 dark:text-gray-400">No hay configuración activa</p>
                    @endif
                    
                    <button @click="showEditModal('retiro')" class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                        Modificar Comisiones
                    </button>
                </div>
            </div>

            {{-- Simulador de Comisiones --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Simulador de Comisiones</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Monto de Transacción</label>
                        <input x-model="simulatorAmount" @input="calculateSimulation()" type="number" step="0.01" placeholder="1000.00" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipo de Transacción</label>
                        <select x-model="simulatorType" @change="calculateSimulation()" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="deposito">Depósito</option>
                            <option value="retiro">Retiro</option>
                        </select>
                    </div>
                </div>
                
                <div x-show="simulationResult" class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Resultado de la Simulación</h3>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Comisión Total</p>
                            <p class="font-bold text-gray-800 dark:text-white" x-text="simulationResult?.totalCommission || '0.00'"></p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Admin</p>
                            <p class="font-bold text-green-600" x-text="simulationResult?.adminCommission || '0.00'"></p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Cajero</p>
                            <p class="font-bold text-purple-600" x-text="simulationResult?.cashierCommission || '0.00'"></p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Vendedor</p>
                            <p class="font-bold text-blue-600" x-text="simulationResult?.sellerCommission || '0.00'"></p>
                        </div>
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Referido</p>
                            <p class="font-bold text-orange-600" x-text="simulationResult?.referralCommission || '0.00'"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Historial de Configuraciones --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Historial de Configuraciones</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Tipo</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Total %</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Admin %</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Cajero %</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Vendedor %</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Referido %</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Estado</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-500 dark:text-gray-400">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commissionSettings->take(10) as $setting)
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $setting->type === 'deposito' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($setting->type) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-sm font-medium text-gray-900 dark:text-white">{{ $setting->total_percentage }}%</td>
                                <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ $setting->admin_percentage }}%</td>
                                <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ $setting->cashier_percentage }}%</td>
                                <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ $setting->seller_percentage }}%</td>
                                <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ $setting->referral_percentage }}%</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $setting->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $setting->is_active ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ $setting->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Modal para Editar Comisiones --}}
        <div x-show="showModal" x-cloak 
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
             @click.outside="closeModal()">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-2xl p-6 m-4">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                        Configurar Comisiones - <span x-text="currentType === 'deposito' ? 'Depósitos' : 'Retiros'"></span>
                    </h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <form action="{{ route('admin.commission-settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" x-model="currentType">
                    
                    <div class="space-y-4">
                        {{-- Porcentaje Total --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Porcentaje Total de Comisión (%)
                            </label>
                            <input x-model="form.total_percentage" @input="calculateDistribution()" name="total_percentage" type="number" step="0.01" min="0" max="100" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Porcentaje que se cobrará sobre el monto de la transacción</p>
                        </div>

                        {{-- Distribución de Comisiones --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Administrador (%)
                                </label>
                                <input x-model="form.admin_percentage" @input="checkTotal()" name="admin_percentage" type="number" step="0.01" min="0" max="100" required
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Cajero (%)
                                </label>
                                <input x-model="form.cashier_percentage" @input="checkTotal()" name="cashier_percentage" type="number" step="0.01" min="0" max="100" required
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Vendedor (%)
                                </label>
                                <input x-model="form.seller_percentage" @input="checkTotal()" name="seller_percentage" type="number" step="0.01" min="0" max="100" required
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Referido (%)
                                </label>
                                <input x-model="form.referral_percentage" @input="checkTotal()" name="referral_percentage" type="number" step="0.01" min="0" max="100" required
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        {{-- Verificación del Total --}}
                        <div class="p-3 rounded-lg" :class="distributionTotal === 100 ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900'">
                            <p class="text-sm font-medium" :class="distributionTotal === 100 ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200'">
                                Total de distribución: <span x-text="distributionTotal"></span>%
                                <span x-show="distributionTotal !== 100"> - ⚠️ Debe sumar exactamente 100%</span>
                                <span x-show="distributionTotal === 100"> - ✅ Correcto</span>
                            </p>
                        </div>

                        {{-- Vista Previa --}}
                        <div x-show="form.total_percentage > 0" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h4 class="font-medium text-gray-800 dark:text-white mb-2">Vista Previa (Transacción de $1,000)</h4>
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-sm">
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Comisión Total</p>
                                    <p class="font-bold" x-text="'$' + (1000 * form.total_percentage / 100).toFixed(2)"></p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Admin</p>
                                    <p class="font-bold text-green-600" x-text="'$' + ((1000 * form.total_percentage / 100) * form.admin_percentage / 100).toFixed(2)"></p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Cajero</p>
                                    <p class="font-bold text-purple-600" x-text="'$' + ((1000 * form.total_percentage / 100) * form.cashier_percentage / 100).toFixed(2)"></p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Vendedor</p>
                                    <p class="font-bold text-blue-600" x-text="'$' + ((1000 * form.total_percentage / 100) * form.seller_percentage / 100).toFixed(2)"></p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Referido</p>
                                    <p class="font-bold text-orange-600" x-text="'$' + ((1000 * form.total_percentage / 100) * form.referral_percentage / 100).toFixed(2)"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="closeModal()" class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="distributionTotal !== 100" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white rounded-lg transition-colors">
                            Guardar Configuración
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function adminSettingsData() {
            return {
                darkMode: localStorage.getItem('darkMode') === 'true',
                showModal: false,
                currentType: 'deposito',
                distributionTotal: 0,
                simulatorAmount: 1000,
                simulatorType: 'deposito',
                simulationResult: null,
                
                form: {
                    total_percentage: 3.00,
                    admin_percentage: 40.00,
                    cashier_percentage: 30.00,
                    seller_percentage: 20.00,
                    referral_percentage: 10.00
                },

                init() {
                    this.$watch('darkMode', val => localStorage.setItem('darkMode', val));
                    this.calculateSimulation();
                },

                showEditModal(type) {
                    this.currentType = type;
                    this.loadCurrentSettings(type);
                    this.showModal = true;
                },

                closeModal() {
                    this.showModal = false;
                },

                loadCurrentSettings(type) {
                    // Aquí puedes cargar la configuración actual desde el backend
                    // Por ahora usamos valores por defecto
                    const settings = @json($commissionSettings);
                    const currentSetting = settings.find(s => s.type === type && s.is_active);
                    
                    if (currentSetting) {
                        this.form = {
                            total_percentage: parseFloat(currentSetting.total_percentage),
                            admin_percentage: parseFloat(currentSetting.admin_percentage),
                            cashier_percentage: parseFloat(currentSetting.cashier_percentage),
                            seller_percentage: parseFloat(currentSetting.seller_percentage),
                            referral_percentage: parseFloat(currentSetting.referral_percentage)
                        };
                    }
                    
                    this.checkTotal();
                },

                checkTotal() {
                    this.distributionTotal = parseFloat(this.form.admin_percentage || 0) + 
                                          parseFloat(this.form.cashier_percentage || 0) + 
                                          parseFloat(this.form.seller_percentage || 0) + 
                                          parseFloat(this.form.referral_percentage || 0);
                },

                calculateDistribution() {
                    // Función para calcular distribución automática si lo deseas
                    this.checkTotal();
                },

                calculateSimulation() {
                    if (!this.simulatorAmount || this.simulatorAmount <= 0) {
                        this.simulationResult = null;
                        return;
                    }

                    const settings = @json($commissionSettings);
                    const currentSetting = settings.find(s => s.type === this.simulatorType && s.is_active);
                    
                    if (!currentSetting) {
                        this.simulationResult = null;
                        return;
                    }

                    const totalCommission = (this.simulatorAmount * currentSetting.total_percentage) / 100;
                    
                    this.simulationResult = {
                        totalCommission: ' + totalCommission.toFixed(2),
                        adminCommission: ' + ((totalCommission * currentSetting.admin_percentage) / 100).toFixed(2),
                        cashierCommission: ' + ((totalCommission * currentSetting.cashier_percentage) / 100).toFixed(2),
                        sellerCommission: ' + ((totalCommission * currentSetting.seller_percentage) / 100).toFixed(2),
                        referralCommission: ' + ((totalCommission * currentSetting.referral_percentage) / 100).toFixed(2)
                    };
                }
            }
        }
    </script>
</x-app-layout>