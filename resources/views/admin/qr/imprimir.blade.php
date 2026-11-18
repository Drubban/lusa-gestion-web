@extends('layouts.base')
@section('title', 'Imprimir codigos QR')

@section('body')
<div class="no-print d-flex justify-content-between align-items-center p-3 border-bottom bg-white">
    <span class="text-muted small">{{ $unidades->count() }} unidades. Para PDF elige "Guardar como PDF" en el dialogo de impresion.</span>
    <button type="button" class="btn btn-primary" data-imprimir><i class="bi bi-printer me-1"></i>Imprimir</button>
</div>
<div class="qr-hoja">
    @foreach ($unidades as $u)
        <div class="qr-etiqueta">
            <span class="brand-mark" style="font-size:.9rem">LUSA</span>
            <div class="qr-render" data-qr="{{ $u->token_qr }}"></div>
            <div class="fw-bold" style="color:var(--lusa-blue);font-size:1.1rem">{{ $u->numero_economico }}</div>
            <div class="small text-muted">{{ ucfirst($u->zona?->nombre ?? '') }}</div>
        </div>
    @endforeach
</div>
@endsection
@push('scripts')<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.js"></script>@endpush
