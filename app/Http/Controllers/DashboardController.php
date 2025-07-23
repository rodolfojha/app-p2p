<?php

namespace App\Http\Controllers; // <-- AQUÍ ESTABA EL ERROR

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction; // Importamos el modelo Transaction

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard correcto según el rol del usuario.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Si el usuario es un vendedor, le mostramos su dashboard
        if ($user->role === 'vendedor') {
            // Buscamos las transacciones iniciadas por el vendedor
            $activeTransactions = Transaction::where('initiator_id', $user->id)
                                            ->whereNotIn('status', ['completed', 'cancelled'])
                                            ->latest()
                                            ->get();

            return view('dashboard', [
                'activeTransactions' => $activeTransactions
            ]);
        }

        // Si el usuario es un cajero, le mostramos un dashboard diferente
        if ($user->role === 'cajero') {
            // Buscamos todas las transacciones que están pendientes de ser aceptadas por cualquier cajero.
            $availableTransactions = Transaction::where('status', 'pending_acceptance')
                                                ->with('initiator') // Precargamos la relación para evitar N+1 queries
                                                ->latest()
                                                ->get();

            // También buscamos las transacciones que este cajero ya ha aceptado.
            $acceptedTransactions = Transaction::where('participant_id', $user->id)
                                               ->whereNotIn('status', ['completed', 'cancelled'])
                                               ->latest()
                                               ->get();

            return view('cashier.dashboard', [
                'availableTransactions' => $availableTransactions,
                'acceptedTransactions' => $acceptedTransactions
            ]);
        }

        // Si es otro rol (como admin), por ahora lo mandamos al dashboard normal.
        return view('dashboard', [
            'activeTransactions' => collect() // Una colección vacía por defecto
        ]);
    }
}
