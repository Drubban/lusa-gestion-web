@props(['cancel', 'submit' => 'Guardar'])
<div class="d-flex justify-content-end gap-2 pt-2">
    <a href="{{ $cancel }}" class="btn btn-light">Cancelar</a>
    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i>{{ $submit }}</button>
</div>
