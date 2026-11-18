@extends('admin.layouts.app')
@section('content')
<h1>Exportar Códigos QR</h1>
<div class="row">
    @foreach($unidades as $unidad)
    <div class="col-md-3 text-center mb-4">
        <div class="card" style="width: 9cm; height: 9cm; margin: auto;">
            <div class="card-body">
                <img src="{{ route('admin.qr.generar', $unidad->id) }}" style="width: 7cm; height: 7cm;">
                <p class="mt-2">{{ $unidad->numero_economico }}<br>{{ $unidad->nombre_unidad }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>
<a href="{{ route('admin.qr.descargar-pdf') }}" class="btn btn-success">Descargar todos (PDF)</a>
@endsection