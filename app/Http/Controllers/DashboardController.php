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
    
    if ($user->role === 'cashier') {
        // Para cajeros: usar vista específica del cajero
        $availableTransactions = Transaction::where('status', 'pending_acceptance')
            ->where('initiator_id', '!=', $user->id)
            ->with('initiator')
            ->orderBy('created_at', 'desc')
            ->get();

        $acceptedTransactions = Transaction::where('participant_id', $user->id)
            ->whereIn('status', ['accepted', 'payment_sent', 'completed'])
            ->with(['initiator', 'participant'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Retornar vista específica del cajero (tu primera vista)
        return view('cashier.dashboard', compact('availableTransactions', 'acceptedTransactions'));
        
    } else {
        // Para vendedores: usar vista específica del vendedor
        $activeTransactions = Transaction::where('initiator_id', $user->id)
            ->whereIn('status', ['pending_acceptance', 'accepted', 'payment_sent', 'completed'])
            ->with(['participant'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Retornar la vista original del dashboard
        return view('dashboard', compact('activeTransactions'));
    }
}
}
