<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

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
        
        \Log::info('Usuario actual:', [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role
        ]);
        
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

            \Log::info('Datos del cajero:', [
                'availableTransactions' => $availableTransactions->count(),
                'acceptedTransactions' => $acceptedTransactions->count()
            ]);

            // ✅ Retornar vista específica del cajero
            return view('cashier.dashboard', compact('availableTransactions', 'acceptedTransactions'));
            
        } else {
            // Para vendedores: usar vista específica del vendedor
            $activeTransactions = Transaction::where('initiator_id', $user->id)
                ->whereIn('status', ['pending_acceptance', 'accepted', 'payment_sent', 'completed'])
                ->with(['participant'])
                ->orderBy('created_at', 'desc')
                ->get();

            \Log::info('Datos del vendedor:', [
                'activeTransactions' => $activeTransactions->count()
            ]);

            // ✅ Retornar la vista original del dashboard (vendedor)
            return view('dashboard', compact('activeTransactions'));
        }
    }
}