@extends('layouts.app')
@section('title', 'Codigos QR')

@section('content')
<x-page-header title="Codigos QR" subtitle="Cada QR contiene un token aleatorio de la unidad, no su id">
    <x-slot:actions><a href="{{ route('admin.qr.imprimir') }}" class="btn btn-primary" target="_blank" rel="noopener"><i class="bi bi-printer me-1"></i>Imprimir todos</a></x-slot:actions>
</x-page-header>
<div class="row g-3">
    @forelse ($unidades as $u)
        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
            <a href="{{ route('admin.unidades.show', $u) }}" class="card qr-card h-100 text-reset"><div class="card-body p-3">
                <div class="qr-render mx-auto" data-qr="{{ $u->token_qr }}" role="img" aria-label="QR {{ $u->numero_economico }}"></div>
                <div class="fw-semibold mt-2">{{ $u->numero_economico }}</div>
                <div class="small text-muted">{{ ucfirst($u->zona?->nombre ?? '') }}</div>
            </div></a>
        </div>
    @empty
        <div class="col-12"><x-empty icon="bi-qr-code" text="No hay unidades activas." /></div>
    @endforelse
</div>
@endsection
@push('scripts')<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.js"></script>@endpush
