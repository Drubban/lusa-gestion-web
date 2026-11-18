@props(['name', 'label', 'actual' => null])
<div class="mb-3">
    <label class="form-label">{{ $label }}</label>
    @if ($actual)
        <div class="mb-2 small text-muted">Firma registrada: <img src="{{ $actual }}" alt="Firma" class="firma-img align-middle"> <span class="ms-1">Dibuja abajo solo si quieres reemplazarla.</span></div>
    @endif
    <div class="signature-box" data-firma>
        <canvas aria-label="{{ $label }}"></canvas>
        <input type="hidden" name="{{ $name }}">
        <div class="d-flex justify-content-between align-items-center px-2 pb-2">
            <span class="small text-muted">Firme dentro del recuadro</span>
            <button type="button" class="btn btn-ghost btn-sm" data-firma-limpiar><i class="bi bi-eraser me-1"></i>Limpiar</button>
        </div>
    </div>
</div>
