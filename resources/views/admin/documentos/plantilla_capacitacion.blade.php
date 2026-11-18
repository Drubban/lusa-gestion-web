<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 28px 32px; }
    body { font-family: "DejaVu Sans", sans-serif; font-size: 11px; color: #1b2333; }
    .head { border-bottom: 3px solid #1d5fad; padding-bottom: 8px; margin-bottom: 14px; }
    .marca { font-size: 22px; font-weight: bold; font-style: italic; color: #1d5fad; }
    .franja { height: 4px; background: #e8a814; width: 90px; margin-top: 2px; }
    .titulo { font-size: 14px; font-weight: bold; margin: 0; }
    .folio { float: right; text-align: right; color: #4a5468; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    th, td { border: 1px solid #e3e7ee; padding: 6px 8px; text-align: left; }
    th { background: #eaf1fa; color: #164a87; width: 30%; }
    .texto { line-height: 1.6; margin: 14px 0 24px; }
    .firmas td { text-align: center; height: 110px; vertical-align: bottom; border: 0; }
    .firmas img { max-height: 70px; max-width: 180px; }
    .linea { border-top: 1px solid #1b2333; padding-top: 4px; font-size: 10px; }
</style>
</head>
<body>
<div class="head">
    <div class="folio">Folio <strong>{{ $documento->folio }}</strong><br>{{ $documento->fecha?->format('d/m/Y') }} {{ substr((string) $documento->hora, 0, 5) }}</div>
    <div class="marca">LUSA</div><div class="franja"></div>
    <p class="titulo">Conformidad de capacitacion</p>
</div>
<table>
    <tr><th>Operador</th><td>{{ $documento->operador?->nombre_completo ?? '-' }} ({{ $documento->operador?->clave_operador }})</td></tr>
    <tr><th>Unidad</th><td>{{ $documento->unidad?->etiqueta }}</td></tr>
    <tr><th>Zona</th><td>{{ ucfirst($documento->unidad?->zona?->nombre ?? '-') }}</td></tr>
    <tr><th>Tipo de capacitacion</th><td>{{ $documento->tipo_capacitacion ?? '-' }}</td></tr>
    <tr><th>Instructor</th><td>{{ $documento->instructor ?? '-' }}</td></tr>
    <tr><th>Duracion</th><td>{{ $documento->duracion_horas ? $documento->duracion_horas . ' horas' : '-' }}</td></tr>
</table>
<p class="texto">El operador manifiesta haber recibido la capacitacion descrita y estar conforme con la misma.
@if($documento->observaciones)<br>Observaciones: {{ $documento->observaciones }}@endif</p>
<table class="firmas"><tr>
    @foreach (\App\Models\DocumentoCapacitacion::FIRMAS as $tipo => $etiqueta)
        <td>@if($f = $documento->firma($tipo))<img src="{{ $f->data_uri }}" alt=""><br>@endif<div class="linea">{{ $etiqueta }}</div></td>
    @endforeach
</tr></table>
</body>
</html>
