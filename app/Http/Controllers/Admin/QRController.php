<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unidad;

/**
 * Codigos QR de las unidades. El QR se dibuja en el navegador (qrcode-generator)
 * para no depender de paquetes PHP ni de la extension imagick.
 * El QR contiene solo el token aleatorio de la unidad, nunca su id.
 */
class QRController extends Controller
{
    public function index()
    {
        return view('admin.qr.index', ['unidades' => Unidad::activas()->with('zona')->get()]);
    }

    /** Hoja lista para imprimir o guardar como PDF desde el navegador. */
    public function imprimir()
    {
        return view('admin.qr.imprimir', ['unidades' => Unidad::activas()->with('zona')->get()]);
    }
}
