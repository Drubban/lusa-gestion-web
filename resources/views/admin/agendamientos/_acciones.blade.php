{{-- Acciones rapidas sobre un agendamiento pendiente --}}
@if ($a->estado === \App\Enums\EstadoAgendamiento::Pendiente)
    <form method="POST" action="{{ route('admin.agendamientos.cumplir', $a) }}" class="d-inline">@csrf
        <button class="btn btn-ghost btn-icon btn-sm text-success" title="Marcar cumplido"><i class="bi bi-check2-circle"></i></button>
    </form>
    <button type="button" class="btn btn-ghost btn-icon btn-sm" title="No se presento" data-bs-toggle="modal" data-bs-target="#modalNoPresentado"
        data-modal-action="{{ route('admin.agendamientos.no-presentado', $a) }}" data-modal-titulo="No presentado: {{ $a->unidad?->numero_economico }}"><i class="bi bi-x-circle"></i></button>
    <button type="button" class="btn btn-ghost btn-icon btn-sm" title="Reagendar" data-bs-toggle="modal" data-bs-target="#modalReagendar"
        data-modal-action="{{ route('admin.agendamientos.reagendar', $a) }}" data-modal-titulo="Reagendar {{ $a->unidad?->numero_economico }}"><i class="bi bi-calendar2-week"></i></button>
@endif
