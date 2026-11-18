{{-- Modales compartidos: reportar no presentado y reagendar. La accion la asigna lusa.js --}}
<div class="modal fade" id="modalNoPresentado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><form method="POST" class="modal-content">
        @csrf
        <div class="modal-header"><h5 class="modal-title" data-modal-titulo>Reportar no presentado</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
        <div class="modal-body">
            <x-form.select name="motivo" label="Motivo" :options="$motivos" required />
            <x-form.input type="date" name="fecha_reprogramada" label="Reprogramar para (opcional)" :min="today()->format('Y-m-d')" />
            <x-form.textarea name="observaciones" label="Observaciones" rows="2" wrapper="mb-0" />
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-primary">Guardar</button></div>
    </form></div>
</div>
<div class="modal fade" id="modalReagendar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><form method="POST" class="modal-content">
        @csrf
        <div class="modal-header"><h5 class="modal-title" data-modal-titulo>Reagendar</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
        <div class="modal-body">
            <x-form.input type="date" name="nueva_fecha" label="Nueva fecha" required :min="today()->format('Y-m-d')" />
            <x-form.input name="motivo" label="Motivo" maxlength="500" wrapper="mb-0" />
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-primary">Reagendar</button></div>
    </form></div>
</div>
