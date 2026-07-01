<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatConversationController extends Controller
{
    /**
     * Mostrar listado de conversaciones
     */
    public function index(Request $request)
    {
        $query = ChatConversation::withCount('messages')
            ->with(['messages' => function($q) {
                $q->latest()->first();
            }]);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('session_id', 'like', "%{$search}%")
                    ->orWhere('visitor_name', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhereHas('messages', function($msg) use ($search) {
                        $msg->where('message', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Estadísticas
        $stats = [
            'total'          => ChatConversation::count(),
            'active'         => ChatConversation::where('status', 'active')->count(),
            'resolved'       => ChatConversation::where('status', 'resolved')->count(),
            'messages_today' => ChatMessage::whereDate('created_at', today())->count(),
            'total_messages' => ChatMessage::count(),
        ];

        $conversations = $query->latest()->paginate(100);

        return view('conversaciones', compact('conversations', 'stats'));
    }

    /**
     * Mostrar detalles de una conversación
     */
    public function show($id)
    {
        $conversation = ChatConversation::with(['messages' => function($q) {
            $q->orderBy('created_at', 'asc');
        }])->findOrFail($id);

        return view('conversacionesShow', compact('conversation'))->render();
    }

    /**
     * Actualizar estado de la conversación
     */
    public function updateStatus(Request $request, $id)
    {
        $conversation = ChatConversation::findOrFail($id);

        $request->validate([
            'status' => 'required|in:active,resolved,pending'
        ]);

        $conversation->update([
            'status' => $request->status,
            'ended_at' => $request->status === 'resolved' ? now() : $conversation->ended_at
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente'
        ]);
    }

    /**
     * Eliminar conversación
     */
    public function destroy($id)
    {
        $conversation = ChatConversation::findOrFail($id);

        // Eliminar mensajes relacionados
        $conversation->messages()->delete();
        $conversation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Conversación eliminada correctamente'
        ]);
    }

    /**
     * Exportar conversaciones
     */
    public function export(Request $request)
    {
        $query = ChatConversation::with('messages');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $conversations = $query->get();

        $filename = 'conversaciones_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = ['ID', 'Visitante', 'Session ID', 'IP', 'Estado', 'Inicio', 'Fin', 'Mensajes', 'Último mensaje'];

        $callback = function() use ($conversations, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($conversations as $conv) {
                $row = [
                    $conv->id,
                    $conv->visitor_name ?? 'Anónimo',
                    $conv->session_id,
                    $conv->ip_address,
                    $conv->status,
                    $conv->created_at->format('Y-m-d H:i:s'),
                    $conv->ended_at ? $conv->ended_at->format('Y-m-d H:i:s') : 'Activa',
                    $conv->messages->count(),
                    $conv->messages->last()?->message ?? 'Sin mensajes'
                ];
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Obtener estadísticas para gráficas
     */
    public function stats()
    {
        $conversationsByDay = ChatConversation::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as total')
        )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $messagesByHour = ChatMessage::select(
            DB::raw('HOUR(created_at) as hour'),
            DB::raw('count(*) as total')
        )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return response()->json([
            'by_day' => $conversationsByDay,
            'by_hour' => $messagesByHour
        ]);
    }
}
