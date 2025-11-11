<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\AuthorizesPermissions;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    use AuthorizesPermissions;

    /**
     * Display a listing of audit logs.
     */
    public function index(Request $request)
    {
        $this->authorizePermission('audit.view', 'No tienes permiso para ver auditorías.');

        $query = AuditLog::with('user');

        // Filtro por usuario
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtro por acción
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filtro por tipo de modelo
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filtro por rango de fechas
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Búsqueda en descripción
        if ($request->filled('search')) {
            $query->where('description', 'like', '%'.$request->search.'%');
        }

        $auditLogs = $query->latest()->paginate(20);
        $users = User::orderBy('name')->get();

        // Obtener acciones únicas
        $actions = AuditLog::select('action')
            ->distinct()
            ->pluck('action');

        // Obtener tipos de modelo únicos
        $modelTypes = AuditLog::select('model_type')
            ->distinct()
            ->whereNotNull('model_type')
            ->pluck('model_type');

        return view('audit.index', compact('auditLogs', 'users', 'actions', 'modelTypes'));
    }

    /**
     * Display the specified audit log.
     */
    public function show(AuditLog $auditLog)
    {
        $this->authorizePermission('audit.view', 'No tienes permiso para ver detalles de auditoría.');

        $auditLog->load('user');

        return view('audit.show', compact('auditLog'));
    }
}
