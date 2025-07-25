{{-- Panel de gestión de métodos de pago para cajeros --}}
<x-app-layout>
    <div x-data="paymentMethodsData()" 
         :class="{'dark': darkMode === true}"
         class="bg-gray-100 dark:bg-gray-900 min-h-screen">

        <div class="max-w-6xl mx-auto px-4 py-6">
            
            {{-- Header --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Mis Métodos de Pago</h1>
                        <p class="text-gray-600 dark:text-gray-400">Gestiona tus cuentas bancarias para recibir depósitos</p>
                    </div>
                    <button @click="openAddModal()" 
                            class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-3 px-6 rounded-lg transition-colors flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Agregar Método</span>
                    </button>
                </div>
            </div>

            {{-- Lista de métodos de pago --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <template x-for="method in paymentMethods" :key="method.id">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 relative">
                        {{-- Badge de método principal --}}
                        <div x-show="method.is_primary" 
                             class="absolute -top-2 -right-2 bg-pink-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                            Principal
                        </div>

                        {{-- Información del banco --}}
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg"
                                 :style="'background-color: ' + (method.bank?.color || '#6B7280')">
                                <span x-text="method.bank_name.charAt(0)"></span>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 dark:text-white" x-text="method.bank_name"></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="method.account_type.charAt(0).toUpperCase() + method.account_type.slice(1)"></p>
                            </div>
                        </div>

                        {{-- Detalles de la cuenta --}}
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">
                                    <span x-show="['nequi', 'daviplata'].includes(method.account_type)">Teléfono:</span>
                                    <span x-show="!['nequi', 'daviplata'].includes(method.account_type)">Cuenta:</span>
                                </span>
                                <span class="font-medium text-gray-900 dark:text-white font-mono" x-text="method.account_number"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Titular:</span>
                                <span class="font-medium text-gray-900 dark:text-white" x-text="method.account_holder_name"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">WhatsApp:</span>
                                <span class="font-medium text-gray-900 dark:text-white" x-text="method.whatsapp_number"></span>
                            </div>
                        </div>

                        {{-- Botones de acción --}}
                        <div class="flex space-x-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                            <button x-show="!method.is_primary" 
                                    @click="makePrimary(method.id)"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 px-3 rounded-lg transition-colors">
                                Hacer Principal
                            </button>
                            <button @click="editMethod(method)" 
                                    class="flex-1 bg-gray-600 hover:bg-gray-700 text-white text-xs font-bold py-2 px-3 rounded-lg transition-colors">
                                Editar
                            </button>
                            <button @click="deleteMethod(method.id)" 
                                    class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold py-2 px-3 rounded-lg transition-colors">
                                🗑️
                            </button>
                        </div>
                    </div>
                </template>

                {{-- Estado vacío --}}
                <div x-show="paymentMethods.length === 0" 
                     class="col-span-full text-center py-12">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">No tienes métodos de pago</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Agrega al menos un método de pago para poder recibir depósitos</p>
                    <button @click="openAddModal()" 
                            class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                        Agregar Primer Método
                    </button>
                </div>
            </div>

            {{-- Botón para volver al dashboard --}}
            <div class="text-center">
                <a href="{{ route('dashboard') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition-colors inline-flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Volver al Dashboard</span>
                </a>
            </div>
        </div>

        {{-- Modal para agregar/editar método de pago --}}
        <div x-show="showModal" x-cloak 
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
             @click.outside="closeModal()">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md p-6 m-4">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white" x-text="isEditing ? 'Editar Método de Pago' : 'Agregar Método de Pago'"></h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form @submit.prevent="submitMethod()" class="space-y-4">
                    {{-- Selección de banco --}}
                    <div x-show="!isEditing">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Banco</label>
                        <div class="grid grid-cols-2 gap-2">
                            <template x-for="bank in availableBanks" :key="bank.code">
                                <label class="cursor-pointer">
                                    <input type="radio" :value="bank.code" x-model="form.bank_code" @change="loadBankInfo()" class="sr-only">
                                    <div class="p-2 rounded-lg border-2 transition-all text-center"
                                         :class="form.bank_code === bank.code ? 'border-2' : 'border-gray-200 dark:border-gray-600'"
                                         :style="form.bank_code === bank.code ? 'border-color: ' + bank.color + '; background-color: ' + bank.color + '15;' : ''">
                                        <div class="w-8 h-8 mx-auto mb-1 rounded-full flex items-center justify-center text-white font-bold text-sm"
                                             :style="'background-color: ' + bank.color">
                                            <span x-text="bank.name.charAt(0)"></span>
                                        </div>
                                        <p class="font-medium text-gray-900 dark:text-white text-xs" x-text="bank.name"></p>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>

                    {{-- Información bancaria --}}
                    <div x-show="form.bank_code || isEditing" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    <span x-show="selectedBankInfo?.is_digital_wallet">Número de teléfono</span>
                                    <span x-show="!selectedBankInfo?.is_digital_wallet && !isEditing">Número de cuenta</span>
                                    <span x-show="isEditing">Número de cuenta/teléfono</span>
                                </label>
                                <input type="text" x-model="form.account_number" 
                                       :placeholder="selectedBankInfo?.is_digital_wallet ? '3001234567' : '1234567890'"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500" required>
                            </div>
                            
                            <div x-show="!isEditing">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de cuenta</label>
                                <select x-model="form.account_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500" required>
                                    <option value="">Selecciona</option>
                                    <template x-for="accountType in selectedBankInfo?.account_types || []" :key="accountType.value">
                                        <option :value="accountType.value" x-text="accountType.label"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">WhatsApp</label>
                                <input type="text" x-model="form.whatsapp_number" placeholder="3001234567"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre del titular</label>
                                <input type="text" x-model="form.account_holder_name" placeholder="Juan Pérez"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500" required>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número de identificación</label>
                            <input type="text" x-model="form.account_holder_id" placeholder="12345678"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500" required>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" x-model="form.is_primary" id="is_primary" 
                                   class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                            <label for="is_primary" class="ml-2 block text-sm text-gray-900 dark:text-white">
                                Establecer como método principal
                            </label>
                        </div>
                    </div>

                    {{-- Botones --}}
                    <div class="flex space-x-3 pt-4">
                        <button type="button" @click="closeModal()"
                                class="flex-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 font-bold py-2 px-4 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="isSubmitting"
                                class="flex-1 bg-pink-600 hover:bg-pink-700 disabled:bg-gray-400 text-white font-bold py-2 px-4 rounded-lg transition-colors disabled:cursor-not-allowed">
                            <span x-show="!isSubmitting" x-text="isEditing ? 'Actualizar' : 'Agregar'"></span>
                            <span x-show="isSubmitting">Procesando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function paymentMethodsData() {
            return {
                darkMode: localStorage.getItem('darkMode') === 'true',
                showModal: false,
                isEditing: false,
                isSubmitting: false,
                selectedBankInfo: null,
                
                paymentMethods: @json($paymentMethods),
                availableBanks: @json($availableBanks),
                
                form: {
                    bank_code: '',
                    account_number: '',
                    account_type: '',
                    whatsapp_number: '',
                    account_holder_name: '',
                    account_holder_id: '',
                    is_primary: false
                },
                
                editingId: null,

                init() {
                    this.$watch('darkMode', val => localStorage.setItem('darkMode', val));
                },

                openAddModal() {
                    this.isEditing = false;
                    this.editingId = null;
                    this.resetForm();
                    this.showModal = true;
                },

                editMethod(method) {
                    this.isEditing = true;
                    this.editingId = method.id;
                    this.form = {
                        bank_code: method.bank_code,
                        account_number: method.account_number,
                        account_type: method.account_type,
                        whatsapp_number: method.whatsapp_number,
                        account_holder_name: method.account_holder_name,
                        account_holder_id: method.account_holder_id,
                        is_primary: method.is_primary
                    };
                    this.showModal = true;
                },

                closeModal() {
                    this.showModal = false;
                    this.isEditing = false;
                    this.editingId = null;
                    this.resetForm();
                },

                resetForm() {
                    this.form = {
                        bank_code: '',
                        account_number: '',
                        account_type: '',
                        whatsapp_number: '',
                        account_holder_name: '',
                        account_holder_id: '',
                        is_primary: false
                    };
                    this.selectedBankInfo = null;
                },

                async loadBankInfo() {
                    if (!this.form.bank_code) {
                        this.selectedBankInfo = null;
                        return;
                    }

                    try {
                        const response = await fetch('/transactions/bank-info', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ bank_code: this.form.bank_code })
                        });

                        const result = await response.json();
                        if (result.success) {
                            this.selectedBankInfo = result.data;
                        }
                    } catch (error) {
                        console.error('Error loading bank info:', error);
                    }
                },

                async submitMethod() {
                    if (this.isSubmitting) return;

                    this.isSubmitting = true;

                    try {
                        const url = this.isEditing 
                            ? `/cashier/payment-methods/${this.editingId}`
                            : '/cashier/payment-methods';
                        
                        const method = this.isEditing ? 'PUT' : 'POST';

                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(this.form)
                        });

                        const result = await response.json();

                        if (result.success) {
                            if (this.isEditing) {
                                // Actualizar método existente
                                const index = this.paymentMethods.findIndex(m => m.id === this.editingId);
                                if (index !== -1) {
                                    this.paymentMethods[index] = result.payment_method;
                                }
                            } else {
                                // Agregar nuevo método
                                this.paymentMethods.push(result.payment_method);
                            }
                            
                            alert(result.message);
                            this.closeModal();
                        } else {
                            throw new Error(result.message);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error: ' + error.message);
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                async makePrimary(methodId) {
                    try {
                        const response = await fetch(`/cashier/payment-methods/${methodId}/make-primary`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            // Actualizar estado en la vista
                            this.paymentMethods.forEach(method => {
                                method.is_primary = method.id === methodId;
                            });
                            
                            alert(result.message);
                        } else {
                            throw new Error(result.message);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error: ' + error.message);
                    }
                },

                async deleteMethod(methodId) {
                    if (!confirm('¿Estás seguro de que quieres eliminar este método de pago?')) {
                        return;
                    }

                    try {
                        const response = await fetch(`/cashier/payment-methods/${methodId}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            // Remover de la vista
                            this.paymentMethods = this.paymentMethods.filter(method => method.id !== methodId);
                            alert(result.message);
                        } else {
                            throw new Error(result.message);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error: ' + error.message);
                    }
                }
            }
        }
    </script>
</x-app-layout>