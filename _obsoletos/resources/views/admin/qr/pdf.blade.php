<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8">
<style>
    @page { margin: 24px; }
    body { font-family: "DejaVu Sans", sans-serif; color: #1b2333; }
    table { width: 100%; border-collapse: collapse; }
    td { width: 25%; text-align: center; padding: 10px 6px 16px; border: 1px dashed #c3cad6; }
    img { width: 140px; height: 140px; }
    .eco { font-size: 16px; font-weight: bold; color: #1d5fad; }
    .zona { font-size: 10px; color: #7a8496; }
    .marca { font-style: italic; font-weight: bold; color: #1d5fad; font-size: 10px; }
</style></head>
<body>
<table>
@foreach ($unidades->chunk(4) as $fila)
    <tr>
    @foreach ($fila as $item)
        <td><div class="marca">LUSA</div><img src="data:image/png;base64,{{ $item['qr'] }}" alt=""><div class="eco">{{ $item['unidad']->numero_economico }}</div><div class="zona">{{ ucfirst($item['unidad']->zona?->nombre ?? '') }}</div></td>
    @endforeach
    @for ($i = $fila->count(); $i < 4; $i++)<td></td>@endfor
    </tr>
@endforeach
</table>
</body></html>
