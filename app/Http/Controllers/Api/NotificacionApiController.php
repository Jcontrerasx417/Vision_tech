<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionApiController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->notificaciones()->latest()->paginate(15);
    }

    public function marcarLeida(Request $request, Notificacion $notificacion)
    {
        abort_unless($notificacion->user_id === $request->user()->id, 403);
        $notificacion->update(['leida_en' => now()]);

        return $notificacion;
    }
}
