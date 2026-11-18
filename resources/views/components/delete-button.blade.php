@props(['action', 'confirm' => 'Esta accion no se puede deshacer. Continuar?', 'label' => null, 'icon' => 'bi-trash3'])
<form method="POST" action="{{ $action }}" class="d-inline" data-confirm="{{ $confirm }}">
    @csrf @method('DELETE')
    <button type="submit" {{ $attributes->class($label ? 'btn btn-ghost-danger' : 'btn btn-ghost-danger btn-icon btn-sm') }} title="Eliminar">
        <i class="bi {{ $icon }}"></i>@if($label)<span class="ms-1">{{ $label }}</span>@endif
    </button>
</form>
