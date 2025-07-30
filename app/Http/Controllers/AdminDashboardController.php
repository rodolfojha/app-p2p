<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\User;
use App\Models\CashierPaymentMethod;
use App\Models\CommissionSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Verificar que el usuario sea administrador
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Solo los administradores pueden acceder a esta sección');
        }

        // Estadísticas generales
        $stats = $this->getGeneralStats();
        
        // Transacciones recientes
        $recentTransactions = $this->getRecentTransactions();
        
        // Estadísticas de usuarios
        $userStats = $this->getUserStats();
        
        // Alertas y notificaciones
        $alerts = $this->getSystemAlerts();
        
        // Datos para gráficos
        $chartData = $this->getChartData();

        // ✅ NUEVAS ESTADÍSTICAS DE COMISIONES PARA EL ADMIN
        $adminCommissionStats = $this->getAdminCommissionStats();

        return view('admin.dashboard', compact(
            'stats', 
            'recentTransactions', 
            'userStats', 
            'alerts',
            'chartData',
            'adminCommissionStats' // ✅ NUEVO
        ));
    }

    /**
     * ✅ NUEVAS ESTADÍSTICAS DE COMISIONES DEL ADMINISTRADOR
     */
    private function getAdminCommissionStats()
    {
        $adminId = Auth::id();
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        return [
            // Ganancias por comisiones del admin
            'total_admin_earnings' => Transaction::where('status', 'completed')
                ->where('admin_id', $adminId)
                ->sum('admin_commission'),
            
            'admin_earnings_today' => Transaction::where('status', 'completed')
                ->where('admin_id', $adminId)
                ->whereDate('created_at', $today)
                ->sum('admin_commission'),
            
            'admin_earnings_this_month' => Transaction::where('status', 'completed')
                ->where('admin_id', $adminId)
                ->where('created_at', '>=', $thisMonth)
                ->sum('admin_commission'),
            
            'admin_earnings_last_month' => Transaction::where('status', 'completed')
                ->where('admin_id', $adminId)
                ->whereBetween('created_at', [
                    $lastMonth, 
                    $lastMonth->copy()->endOfMonth()
                ])
                ->sum('admin_commission'),

            // Distribución de comisiones por tipo de transacción
            'commission_by_type' => Transaction::where('status', 'completed')
                ->where('admin_id', $adminId)
                ->select('type')
                ->selectRaw('COUNT(*) as count')
                ->selectRaw('SUM(admin_commission) as total_commission')
                ->groupBy('type')
                ->get(),

            // Comisiones por mes (últimos 6 meses)
            'monthly_commissions' => Transaction::where('status', 'completed')
                ->where('admin_id', $adminId)
                ->where('created_at', '>=', Carbon::now()->subMonths(6))
                ->select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'))
                ->selectRaw('SUM(admin_commission) as total_commission')
                ->selectRaw('COUNT(*) as transaction_count')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get(),
        ];
    }

    /**
     * Estadísticas generales del sistema
     */
    private function getGeneralStats()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        return [
            // Transacciones
            'total_transactions' => Transaction::count(),
            'transactions_today' => Transaction::whereDate('created_at', $today)->count(),
            'transactions_this_month' => Transaction::where('created_at', '>=', $thisMonth)->count(),
            'transactions_last_month' => Transaction::whereBetween('created_at', [
                $lastMonth, 
                $lastMonth->copy()->endOfMonth()
            ])->count(),

            // Volumen de dinero
            'total_volume' => Transaction::where('status', 'completed')->sum('amount'),
            'volume_today' => Transaction::where('status', 'completed')
                ->whereDate('created_at', $today)->sum('amount'),
            'volume_this_month' => Transaction::where('status', 'completed')
                ->where('created_at', '>=', $thisMonth)->sum('amount'),

            // Comisiones
            'total_commissions' => Transaction::where('status', 'completed')->sum('total_commission'),
            'commissions_today' => Transaction::where('status', 'completed')
                ->whereDate('created_at', $today)->sum('total_commission'),
            'commissions_this_month' => Transaction::where('status', 'completed')
                ->where('created_at', '>=', $thisMonth)->sum('total_commission'),

            // Estados de transacciones
            'pending_transactions' => Transaction::where('status', 'pending_acceptance')->count(),
            'active_transactions' => Transaction::whereIn('status', ['accepted', 'payment_sent'])->count(),
            'completed_transactions' => Transaction::where('status', 'completed')->count(),
        ];
    }

    /**
     * Transacciones recientes para supervisión
     */
    private function getRecentTransactions()
    {
        return Transaction::with(['initiator', 'participant'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Estadísticas de usuarios
     */
    private function getUserStats()
    {
        return [
            'total_users' => User::count(),
            'sellers_count' => User::where('role', 'vendedor')->count(),
            'cashiers_count' => User::where('role', 'cashier')->count(),
            'admins_count' => User::where('role', 'admin')->count(),
            
            // Usuarios activos (que han hecho transacciones)
            'active_sellers' => User::where('role', 'vendedor')
                ->whereHas('initiatedTransactions')
                ->count(),
            'active_cashiers' => User::where('role', 'cashier')
                ->whereHas('participatedTransactions')
                ->count(),

            // Métodos de pago de cajeros
            'cashiers_with_payment_methods' => User::where('role', 'cashier')
                ->whereHas('paymentMethods')
                ->count(),
            'total_payment_methods' => CashierPaymentMethod::where('is_active', true)->count(),
        ];
    }

    /**
     * Alertas y notificaciones del sistema
     */
    private function getSystemAlerts()
    {
        $alerts = [];

        // Transacciones pendientes por mucho tiempo
        $oldPendingTransactions = Transaction::where('status', 'pending_acceptance')
            ->where('created_at', '<', Carbon::now()->subHours(24))
            ->count();

        if ($oldPendingTransactions > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Transacciones pendientes',
                'message' => "{$oldPendingTransactions} transacciones llevan más de 24 horas sin ser aceptadas",
                'action' => 'Ver transacciones',
                'url' => route('admin.transactions')
            ];
        }

        // Cajeros sin métodos de pago
        $cashiersWithoutPaymentMethods = User::where('role', 'cashier')
            ->whereDoesntHave('paymentMethods')
            ->count();

        if ($cashiersWithoutPaymentMethods > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Cajeros sin métodos de pago',
                'message' => "{$cashiersWithoutPaymentMethods} cajeros no han configurado métodos de pago",
                'action' => 'Ver cajeros',
                'url' => route('admin.users', ['role' => 'cashier'])
            ];
        }

        // Transacciones con pagos enviados pero no confirmados
        $unconfirmedPayments = Transaction::where('status', 'payment_sent')
            ->where('updated_at', '<', Carbon::now()->subHours(2))
            ->count();

        if ($unconfirmedPayments > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Pagos sin confirmar',
                'message' => "{$unconfirmedPayments} pagos llevan más de 2 horas sin confirmarse",
                'action' => 'Supervisar',
                'url' => route('admin.transactions', ['status' => 'payment_sent'])
            ];
        }

        return $alerts;
    }

    /**
     * Datos para gráficos del dashboard
     */
    private function getChartData()
    {
        // Transacciones por día (últimos 30 días)
        $transactionsByDay = Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as volume'),
                DB::raw('SUM(total_commission) as commissions')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Distribución por tipo de transacción
        $transactionsByType = Transaction::select('type')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(amount) as total_amount')
            ->groupBy('type')
            ->get();

        // Estados de transacciones
        $transactionsByStatus = Transaction::select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Top cajeros por volumen
        $topCashiers = User::where('role', 'cashier')
            ->withSum(['participatedTransactions' => function($query) {
                $query->where('status', 'completed');
            }], 'amount')
            ->withCount(['participatedTransactions' => function($query) {
                $query->where('status', 'completed');
            }])
            ->orderBy('participated_transactions_sum_amount', 'desc')
            ->limit(5)
            ->get();

        // Top vendedores por volumen
        $topSellers = User::where('role', 'vendedor')
            ->withSum(['initiatedTransactions' => function($query) {
                $query->where('status', 'completed');
            }], 'amount')
            ->withCount(['initiatedTransactions' => function($query) {
                $query->where('status', 'completed');
            }])
            ->orderBy('initiated_transactions_sum_amount', 'desc')
            ->limit(5)
            ->get();

        return [
            'transactions_by_day' => $transactionsByDay,
            'transactions_by_type' => $transactionsByType,
            'transactions_by_status' => $transactionsByStatus,
            'top_cashiers' => $topCashiers,
            'top_sellers' => $topSellers,
        ];
    }

    /**
     * Gestión de usuarios
     */
    public function users(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $query = User::query();

        // Filtros
        if ($request->role) {
            $query->where('role', $request->role);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->withCount(['initiatedTransactions', 'participatedTransactions'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users', compact('users'));
    }

    /**
     * Gestión de transacciones
     */
    public function transactions(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $query = Transaction::with(['initiator', 'participant']);

        // Filtros
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.transactions', compact('transactions'));
    }

    /**
     * ✅ NUEVA FUNCIÓN: Configuración del sistema con gestión de comisiones
     */
    public function settings()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $commissionSettings = CommissionSettings::orderBy('created_at', 'desc')->get();

        return view('admin.settings', compact('commissionSettings'));
    }

    /**
     * ✅ NUEVA FUNCIÓN: Actualizar configuración de comisiones
     */
    public function updateCommissionSettings(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'type' => 'required|in:deposito,retiro',
            'total_percentage' => 'required|numeric|min:0|max:100',
            'admin_percentage' => 'required|numeric|min:0|max:100',
            'cashier_percentage' => 'required|numeric|min:0|max:100',
            'seller_percentage' => 'required|numeric|min:0|max:100',
            'referral_percentage' => 'required|numeric|min:0|max:100',
        ]);

        // Verificar que la suma de porcentajes sea 100%
        $totalDistribution = $request->admin_percentage + $request->cashier_percentage + 
                           $request->seller_percentage + $request->referral_percentage;

        if ($totalDistribution != 100) {
            return back()->withErrors([
                'distribution' => 'La suma de porcentajes de distribución debe ser exactamente 100%'
            ]);
        }

        // Desactivar configuración anterior
        CommissionSettings::where('type', $request->type)
            ->update(['is_active' => false]);

        // Crear nueva configuración
        CommissionSettings::create([
            'type' => $request->type,
            'total_percentage' => $request->total_percentage,
            'admin_percentage' => $request->admin_percentage,
            'cashier_percentage' => $request->cashier_percentage,
            'seller_percentage' => $request->seller_percentage,
            'referral_percentage' => $request->referral_percentage,
            'is_active' => true
        ]);

        return back()->with('success', 'Configuración de comisiones actualizada exitosamente');
    }

    /**
     * ✅ NUEVA FUNCIÓN: Ver reportes detallados de comisiones
     */
    public function commissionReports(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $startDate = $request->start_date ?? Carbon::now()->startOfMonth();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth();

        // Reporte de comisiones por usuario
        $userCommissions = User::withSum(['initiatedTransactions' => function($query) use ($startDate, $endDate) {
                $query->where('status', 'completed')
                      ->whereBetween('created_at', [$startDate, $endDate]);
            }], 'seller_commission')
            ->withSum(['participatedTransactions' => function($query) use ($startDate, $endDate) {
                $query->where('status', 'completed')
                      ->whereBetween('created_at', [$startDate, $endDate]);
            }], 'cashier_commission')
            ->withSum(['referralTransactions' => function($query) use ($startDate, $endDate) {
                $query->where('status', 'completed')
                      ->whereBetween('created_at', [$startDate, $endDate]);
            }], 'referral_commission')
            ->having('initiated_transactions_sum_seller_commission', '>', 0)
            ->orHaving('participated_transactions_sum_cashier_commission', '>', 0)
            ->orHaving('referral_transactions_sum_referral_commission', '>', 0)
            ->get();

        // Comisiones del administrador
        $adminCommissions = Transaction::where('status', 'completed')
            ->where('admin_id', Auth::id())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('admin_commission');

        return view('admin.commission-reports', compact(
            'userCommissions', 
            'adminCommissions', 
            'startDate', 
            'endDate'
        ));
    }
}