<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // ✅ REDIRECCIONAR SEGÚN EL ROL DEL USUARIO
        if ($user->isAdmin()) {
            // REDIRIGIR A DASHBOARD ADMINISTRATIVO
            return redirect()->route('admin.dashboard');
        } elseif ($user->isCashier()) {
            // DASHBOARD PARA CAJEROS
            return $this->cashierDashboard();
        } else {
            // DASHBOARD PARA VENDEDORES
            return $this->sellerDashboard();
        }
    }

    /**
     * ✅ Dashboard específico para cajeros
     */
    private function cashierDashboard()
    {
        $user = Auth::user();

        // Obtener transacciones disponibles para aceptar (pending_acceptance)
        $availableTransactions = Transaction::where('status', 'pending_acceptance')
            ->whereNull('participant_id') // Solo las que no han sido aceptadas
            ->with(['initiator']) // Cargar información del vendedor
            ->orderBy('created_at', 'desc')
            ->get();

        // Obtener transacciones que este cajero ya aceptó
        $acceptedTransactions = Transaction::where('participant_id', $user->id)
            ->whereIn('status', ['accepted', 'payment_sent', 'completed'])
            ->with(['initiator'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('cashier.dashboard', compact('availableTransactions', 'acceptedTransactions'));
    }

    /**
     * ✅ Dashboard específico para vendedores
     */
    private function sellerDashboard()
    {
        $user = Auth::user();

        // Obtener transacciones activas del vendedor (no completadas)
        $activeTransactions = Transaction::where('initiator_id', $user->id)
            ->whereIn('status', ['pending_acceptance', 'accepted', 'payment_sent'])
            ->with(['participant'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard', compact('activeTransactions'));
    }
}