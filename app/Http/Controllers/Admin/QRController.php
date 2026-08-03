<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unidad;
use SimpleSoftwareIO\QrCode\Facades\QrCode;   // ← Importante
use Barryvdh\DomPDF\Facade\Pdf;               // ← Para PDF (si usas laravel-dompdf)
use Illuminate\Http\Request;

class QRController extends Controller
{
    public function exportar()
    {
        $unidades = Unidad::where('activo', true)->get();
        return view('admin.qr.exportar', compact('unidades'));
    }

    public function generar($id)
    {
        $unidad = Unidad::findOrFail($id);
        $qr = QrCode::format('png')->size(300)->generate($unidad->token_qr);
        return response($qr)->header('Content-Type', 'image/png');
    }

    public function descargarTodos()
    {
        $unidades = Unidad::where('activo', true)->get();
        $pdf = Pdf::loadView('admin.qr.pdf', compact('unidades'))->setPaper('a4', 'portrait');
        return $pdf->download('codigos_qr_lusa.pdf');
    }
}