{{-- Formulario mejorado para transacciones --}}
<x-app-layout>
    <div x-data="transactionFormData()" 
         :class="{'dark': darkMode === true}"
         class="bg-gray-100 dark:bg-gray-900 min-h-screen">

        <div class="max-w-4xl mx-auto px-4 py-6">
            
            {{-- Header --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Crear Nueva Transacción</h1>
                <p class="text-gray-600 dark:text-gray-400">Completa los datos para iniciar un depósito o retiro</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                {{-- Formulario --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <form @submit.prevent="submitTransaction()" class="space-y-6">
                        
                        {{-- Tipo de transacción --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Tipo de Transacción</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" 
                                           name="type" 
                                           value="deposito" 
                                           x-model="form.type"
                                           @change="calculatePreview()"
                                           class="sr-only">
                                    <div class="p-4 rounded-lg border-2 transition-all"
                                         :class="form.type === 'deposito' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-green-300'">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white">Depósito</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Recibir dinero</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                                
                                <label class="cursor-pointer">
                                    <input type="radio" 
                                           name="type" 
                                           value="retiro" 
                                           x-model="form.type"
                                           @change="calculatePreview()"
                                           class="sr-only">
                                    <div class="p-4 rounded-lg border-2 transition-all"
                                         :class="form.type === 'retiro' ? 'border-red-500 bg-red-50 dark:bg-red-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-red-300'">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white">Retiro</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Enviar dinero</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Monto --}}
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Monto</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-lg">$</span>
                                </div>
                                <input type="number" 
                                       step="0.01" 
                                       min="0.01"
                                       x-model="form.amount" 
                                       @input="calculatePreview()"
                                       placeholder="0.00" 
                                       class="block w-full pl-8 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                                       required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-sm">USD</span>
                                </div>
                            </div>
                        </div>

                        {{-- Tipo de comisión --}}
                        <div x-show="form.amount > 0">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Manejo de Comisión</label>
                            <div class="space-y-3">
                                <label class="cursor-pointer">
                                    <input type="radio" 
                                           name="commission_type" 
                                           value="deduct_from_total" 
                                           x-model="form.commission_type"
                                           @change="calculatePreview()"
                                           class="sr-only">
                                    <div class="p-3 rounded-lg border transition-all"
                                         :class="form.commission_type === 'deduct_from_total' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600'">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center"
                                                 :class="form.commission_type === 'deduct_from_total' ? 'border-blue-500' : 'border-gray-300'">
                                                <div x-show="form.commission_type === 'deduct_from_total'" class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-900 dark:text-white">Restar comisión del total recibido</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">El cliente paga el monto exacto, tú recibes menos la comisión</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                                
                                <label class="cursor-pointer">
                                    <input type="radio" 
                                           name="commission_type" 
                                           value="add_to_client" 
                                           x-model="form.commission_type"
                                           @change="calculatePreview()"
                                           class="sr-only">
                                    <div class="p-3 rounded-lg border transition-all"
                                         :class="form.commission_type === 'add_to_client' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600'">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center"
                                                 :class="form.commission_type === 'add_to_client' ? 'border-blue-500' : 'border-gray-300'">
                                                <div x-show="form.commission_type === 'add_to_client'" class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-900 dark:text-white">Agregar comisión al total del cliente</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">El cliente paga monto + comisión, tú recibes el monto completo</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Botones --}}
                        <div class="flex space-x-4 pt-4">
                            <button type="button" 
                                    @click="window.history.back()"
                                    class="flex-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 font-bold py-3 px-6 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" 
                                    :disabled="!isFormValid || isSubmitting"
                                    class="flex-1 bg-pink-600 hover:bg-pink-700 disabled:bg-gray-400 text-white font-bold py-3 px-6 rounded-lg transition-colors disabled:cursor-not-allowed flex items-center justify-center">
                                <span x-show="!isSubmitting">Crear Solicitud</span>
                                <span x-show="isSubmitting" class="flex items-center space-x-2">
                                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Procesando...</span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Preview de comisiones --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Vista Previa</h3>
                    
                    <div x-show="!preview" class="text-center py-8">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400">Ingresa un monto para ver el cálculo de comisiones</p>
                    </div>

                    <div x-show="preview" class="space-y-4">
                        {{-- Resumen principal --}}
                        <div class="bg-gradient-to-r from-pink-50 to-purple-50 dark:from-pink-900/20 dark:to-purple-900/20 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Monto original:</span>
                                <span class="font-semibold text-gray-900 dark:text-white" x-text="'$' + (preview?.original_amount || 0).toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Comisión total:</span>
                                <span class="font-semibold text-red-600" x-text="'$' + (preview?.total_commission || 0).toFixed(2)"></span>
                            </div>
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-900 dark:text-white">Monto final:</span>
                                    <span class="text-xl font-bold text-green-600" x-text="'$' + (preview?.final_amount || 0).toFixed(2)"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Distribución de comisiones --}}
                        <div x-show="preview?.breakdown">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-3">Distribución de Comisiones</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Admin (40%):</span>
                                    <span class="font-medium text-gray-900 dark:text-white" x-text="'$' + (preview?.breakdown?.admin || 0).toFixed(2)"></span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Cajero (30%):</span>
                                    <span class="font-medium text-gray-900 dark:text-white" x-text="'$' + (preview?.breakdown?.cashier || 0).toFixed(2)"></span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Vendedor (20%):</span>
                                    <span class="font-medium text-gray-900 dark:text-white" x-text="'$' + (preview?.breakdown?.seller || 0).toFixed(2)"></span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Referido (10%):</span>
                                    <span class="font-medium text-gray-900 dark:text-white" x-text="'$' + (preview?.breakdown?.referral || 0).toFixed(2)"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Explicación --}}
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3">
                            <p class="text-sm text-blue-700 dark:text-blue-300" x-show="form.commission_type === 'deduct_from_total'">
                                El cliente paga <strong x-text="'$' + (preview?.original_amount || 0).toFixed(2)"></strong> y tú recibes <strong x-text="'$' + (preview?.final_amount || 0).toFixed(2)"></strong>
                            </p>
                            <p class="text-sm text-blue-700 dark:text-blue-300" x-show="form.commission_type === 'add_to_client'">
                                El cliente paga <strong x-text="'$' + (preview?.final_amount || 0).toFixed(2)"></strong> y tú recibes <strong x-text="'$' + (preview?.original_amount || 0).toFixed(2)"></strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function transactionFormData() {
            return {
                darkMode: localStorage.getItem('darkMode') === 'true',
                isSubmitting: false,
                preview: null,
                
                form: {
                    type: 'deposito',
                    amount: '',
                    commission_type: 'deduct_from_total'
                },

                init() {
                    this.$watch('darkMode', val => localStorage.setItem('darkMode', val));
                },

                get isFormValid() {
                    return this.form.type && 
                           this.form.amount > 0 && 
                           this.form.commission_type;
                },

                async calculatePreview() {
                    if (!this.form.amount || this.form.amount <= 0) {
                        this.preview = null;
                        return;
                    }

                    try {
                        const response = await fetch('/transactions/preview-commissions', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                amount: parseFloat(this.form.amount),
                                type: this.form.type,
                                commission_type: this.form.commission_type
                            })
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.preview = result.data;
                            console.log('Preview calculado:', this.preview);
                        } else {
                            console.error('Error en preview:', result.message);
                            this.preview = null;
                        }
                    } catch (error) {
                        console.error('Error al calcular preview:', error);
                        this.preview = null;
                    }
                },

                async submitTransaction() {
                    if (!this.isFormValid || this.isSubmitting) return;

                    this.isSubmitting = true;

                    try {
                        const response = await fetch('/transactions', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                amount: parseFloat(this.form.amount),
                                type: this.form.type,
                                commission_type: this.form.commission_type
                            })
                        });

                        const result = await response.json();

                        if (result.success) {
                            // Mostrar éxito
                            this.showSuccessMessage(result);
                            
                            // Limpiar formulario
                            this.form = {
                                type: 'deposito',
                                amount: '',
                                commission_type: 'deduct_from_total'
                            };
                            this.preview = null;

                            // Redirigir al dashboard después de un momento
                            setTimeout(() => {
                                window.location.href = '/dashboard';
                            }, 2000);

                        } else {
                            throw new Error(result.message || 'Error al crear la transacción');
                        }

                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error al crear la transacción: ' + error.message);
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                showSuccessMessage(result) {
                    const commission = result.commission_data;
                    const transaction = result.transaction;

                    let message = `¡Transacción creada exitosamente!\n\n`;
                    message += `Tipo: ${transaction.type.toUpperCase()}\n`;
                    message += `Monto original: $${commission.commissions.total_commission ? (parseFloat(this.form.amount)).toFixed(2) : this.form.amount}\n`;
                    message += `Monto final: $${commission.final_amount.toFixed(2)}\n`;
                    message += `Comisión total: $${commission.commissions.total_commission.toFixed(2)}\n\n`;
                    message += `La solicitud está disponible para que los cajeros la acepten.`;

                    alert(message);
                },

                // Función para formatear números
                formatCurrency(amount) {
                    return new Intl.NumberFormat('es-US', {
                        style: 'currency',
                        currency: 'USD'
                    }).format(amount);
                }
            }
        }
    </script>
</x-app-layout>