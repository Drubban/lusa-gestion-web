<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Formato de Mantenimiento - <?php echo e($documento->asignacion->unidad->numero_economico); ?></title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10.5px;
            line-height: 1.45;
            color: #1a1a1a;
            margin: 0;
            padding: 18px 22px;
        }

        .container {
            width: 100%;
        }

        /* ====== ENCABEZADO ====== */
        .header {
            text-align: center;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 3px double #1e3a8a;
        }

        .header .empresa {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            letter-spacing: 1px;
            margin: 0;
        }

        .header .titulo {
            font-size: 13px;
            font-weight: bold;
            margin: 6px 0 2px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header .subtitulo {
            font-size: 10px;
            color: #555;
            margin: 0;
            font-style: italic;
        }

        .header .folio {
            margin-top: 8px;
            font-size: 10px;
            color: #333;
        }

        .header .folio strong {
            color: #b91c1c;
        }

        /* ====== SECCIONES ====== */
        .seccion-titulo {
            background: #1e3a8a;
            color: #fff;
            padding: 5px 10px;
            font-size: 10.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 12px 0 8px 0;
        }

        /* ====== DECLARACIÓN ====== */
        .declaracion {
            background: #f8fafc;
            border-left: 4px solid #1e3a8a;
            padding: 10px 14px;
            margin-bottom: 10px;
            font-size: 10.5px;
            text-align: justify;
            line-height: 1.55;
        }

        .declaracion p {
            margin: 0 0 6px 0;
        }

        .declaracion p:last-child {
            margin-bottom: 0;
        }

        .declaracion .destacado {
            font-weight: bold;
            color: #1e3a8a;
        }

        /* ====== TABLAS DE DATOS ====== */
        table.info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table.info td {
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
            font-size: 10px;
        }

        table.info td.label {
            font-weight: bold;
            width: 28%;
            background: #e2e8f0;
            color: #1e293b;
        }

        /* ====== ESTADO DE CÁMARAS ====== */
        table.camaras {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table.camaras th,
        table.camaras td {
            border: 1px solid #cbd5e1;
            padding: 5px;
            text-align: center;
            font-size: 10px;
        }

        table.camaras th {
            background: #e2e8f0;
            font-weight: bold;
            color: #1e293b;
        }

        table.camaras .ok {
            color: #15803d;
            font-weight: bold;
        }

        table.camaras .no {
            color: #b91c1c;
            font-weight: bold;
        }

        /* ====== DECLARACIÓN DE CONFORMIDAD ====== */
        .conformidad {
            border: 1.5px solid #1e3a8a;
            padding: 12px 15px;
            margin: 14px 0;
            background: #f0f9ff;
            text-align: justify;
            line-height: 1.55;
            font-size: 10.5px;
        }

        .conformidad .titulo-conf {
            font-weight: bold;
            color: #1e3a8a;
            font-size: 11px;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .conformidad .fecha-revision {
            background: #1e3a8a;
            color: #fff;
            padding: 2px 8px;
            font-weight: bold;
            margin: 0 2px;
        }

        /* ====== FIRMAS ====== */
        .firmas-titulo {
            text-align: center;
            font-weight: bold;
            font-size: 10.5px;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 18px 0 8px 0;
        }

        table.firma {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        table.firma td {
            text-align: center;
            vertical-align: bottom;
            width: 33.33%;
            padding: 0 10px;
        }

        table.firma .firma-space {
            height: 60px;
        }

        table.firma img {
            max-width: 150px;
            max-height: 55px;
        }

        table.firma .linea {
            border-top: 1.5px solid #000;
            width: 90%;
            margin: 4px auto;
        }

        table.firma .cargo {
            font-size: 9.5px;
            font-weight: bold;
            color: #1e293b;
        }

        table.firma .nombre {
            font-size: 9px;
            color: #475569;
            margin-top: 2px;
        }

        /* ====== PIE DE PÁGINA ====== */
        footer {
            text-align: center;
            font-size: 8.5px;
            color: #64748b;
            margin-top: 22px;
            padding-top: 10px;
            border-top: 1px solid #cbd5e1;
        }

        footer .aviso {
            font-style: italic;
            color: #94a3b8;
            font-size: 8px;
            margin-top: 3px;
        }
    </style>
</head>

<body>

    <div class="container">

        
        <div class="header">
            <p class="empresa">LINEAS UNIDAS MEXICO ZUMPANGO TEZONTEPEC PROGRESO HIDALGO Y ANEXAS FLECHA ROJA</p>
            <p class="titulo">Formato de Servicio, mantenimiento y Análisis de Equipos Tecnológicos</p>
            <p class="subtitulo">Instalados en las unidades de transporte</p>
            <div class="folio">
                <strong>FOLIO:</strong> MTO-<?php echo e(str_pad($documento->id, 6, '0', STR_PAD_LEFT)); ?>

                &nbsp;&nbsp;|&nbsp;&nbsp;
                <strong>FECHA DE EMISIÓN:</strong> <?php echo e(now()->format('d/m/Y H:i')); ?>

            </div>
        </div>

        
        <div class="declaracion">
            <p>
                Por medio del presente documento, el área de <span class="destacado">SISTEMAS</span>
                de <span class="destacado">LINEAS UNIDAS MEXICO ZUMPANGO TEZONTEPEC PROGRESO HIDALGO Y ANEXAS FLECHA ROJA</span> hace constar que se llevó a cabo la revisión,
                diagnóstico, mantenimento y, en su caso, la intervención técnica correspondiente a la unidad que se describe
                a continuación, con el fin de garantizar el correcto funcionamiento de los equipos tecnológicos
                instalados en la misma.
            </p>
        </div>

        
        <div class="seccion-titulo">I. Datos de la Unidad y del Servicio</div>

        <table class="info">
            <tr>
                <td class="label">Unidad (No. Económico):</td>
                <td><?php echo e($documento->asignacion->unidad->numero_economico); ?>

                    <?php if($documento->asignacion->unidad->nombre_unidad): ?>
                    - <?php echo e($documento->asignacion->unidad->nombre_unidad); ?>

                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td class="label">Rol / Zona:</td>
                <td><?php echo e($documento->rol ?? 'No especificado'); ?></td>
            </tr>
            <tr>
                <td class="label">Operador asignado:</td>
                <td><?php echo e($documento->asignacion->operador->nombre_completo ?? 'Sin operador asignado'); ?></td>
            </tr>
            <tr>
                <td class="label">Clave del operador:</td>
                <td><?php echo e($documento->asignacion->operador->clave_operador ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <td class="label">Fecha del servicio:</td>
                <td><?php echo e(\Carbon\Carbon::parse($documento->fecha)->format('d/m/Y')); ?></td>
            </tr>
            <tr>
                <td class="label">Hora del servicio:</td>
                <td><?php echo e($documento->hora); ?></td>
            </tr>
        </table>

        
        <div class="seccion-titulo">II. Diagnóstico Técnico y Equipos Reportados</div>

        <table class="info">
            <tr>
                <td class="label">Tecnología reportada:</td>
                <td><?php echo e($documento->tecnologia_reportada ?? 'Sin reporte específico'); ?></td>
            </tr>
            <tr>
                <td class="label">Prueba de barras Optocontrol:</td>
                <td>
                    <?php if($documento->prueba_barras === 'SI'): ?>
                    <span style="color:#15803d; font-weight:bold;">SÍ - Funcionando correctamente</span>
                    <?php elseif($documento->prueba_barras === 'NO'): ?>
                    <span style="color:#b91c1c; font-weight:bold;">NO - Requiere atención</span>
                    <?php else: ?>
                    <?php echo e($documento->prueba_barras ?? 'No aplica'); ?>

                    <?php endif; ?>
                </td>
            </tr>
            <?php if($documento->estado_camaras): ?>
            <tr>
                <td class="label">Estado de cámaras:</td>
                <td><?php echo e($documento->estado_camaras); ?></td>
            </tr>
            <?php endif; ?>
        </table>

        
        <table class="info">
            <tr>
                <td class="label">Observaciones / Comentarios:</td>
                <td><?php echo e($documento->comentarios ?? 'Ninguno'); ?></td>
            </tr>
            <?php if(isset($documento->veces_adeudo) && $documento->veces_adeudo > 0): ?>
            <tr>
                <td class="label">Adeudos registrados:</td>
                <td>
                    <span style="color:#b91c1c; font-weight:bold;">
                        <?php echo e($documento->veces_adeudo); ?> ocasión(es)
                    </span>
                    <?php if($documento->observaciones_adeudo): ?>
                    <br><small><?php echo e($documento->observaciones_adeudo); ?></small>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endif; ?>
        </table>

        
        <div class="seccion-titulo">III. Declaración de Conformidad del Servicio</div>

        <div class="conformidad">
            <div class="titulo-conf">Declaración de servicio realizado</div>
            <p>
                Se hace constar que el día
                <span class="fecha-revision"><?php echo e(\Carbon\Carbon::parse($documento->fecha)->format('d/m/Y')); ?></span>
                a las
                <span class="fecha-revision"><?php echo e($documento->hora); ?> hrs</span>,
                el personal de sistemas <strong>LINEAS UNIDAS MEXICO ZUMPANGO TEZONTEPEC PROGRESO HIDALGO Y ANEXAS FLECHA ROJA</strong> realizó la revisión,
                diagnóstico y servicio correspondiente a la unidad
                <strong><?php echo e($documento->asignacion->unidad->numero_economico); ?></strong>,
                verificando el estado de los equipos tecnológicos instalados, dejando constancia
                del estado en que se encontraban al momento del servicio, así como de las
                acciones realizadas y las recomendaciones pertinentes.
            </p>
            <p style="margin-top: 8px;">
                El operador de la unidad manifiesta su <strong>conformidad</strong> con el servicio
                recibido, así como con los hallazgos y observaciones registrados en el presente
                documento, comprometiéndose a dar seguimiento a las recomendaciones emitidas por
                el área técnica cuando así corresponda.
            </p>
        </div>

        
        <div class="firmas-titulo">IV. Firmas de Conformidad</div>

        <table class="firma">
            <tr>
                <td>
                    <div class="firma-space">
                        <?php if(!empty($documento->firma_operador)): ?>
                        <img src="data:image/png;base64,<?php echo e($documento->firma_operador); ?>" alt="Firma operador">
                        <?php endif; ?>
                    </div>
                    <div class="linea"></div>
                    <div class="cargo">Operador de la unidad</div>
                    <div class="nombre">
                        <?php echo e($documento->asignacion->operador->nombre_completo ?? 'Nombre del operador'); ?>

                    </div>
                    <div class="nombre">
                        Clave: <?php echo e($documento->asignacion->operador->clave_operador ?? 'N/A'); ?>

                    </div>
                </td>
                <td>
                    <div class="firma-space">
                        <?php if(!empty($documento->firma_ing)): ?>
                        <img src="data:image/png;base64,<?php echo e($documento->firma_ing); ?>" alt="Firma Ing.">
                        <?php endif; ?>
                    </div>
                    <div class="linea"></div>
                    <div class="cargo">Ingeniero a cargo</div>
                    <div class="nombre">Área de Tecnología y Mantenimiento</div>
                </td>
                <td>
                    <div class="firma-space">
                        <?php if(!empty($documento->firma_tabulacion)): ?>
                        <img src="data:image/png;base64,<?php echo e($documento->firma_tabulacion); ?>" alt="Firma tabulación">
                        <?php endif; ?>
                    </div>
                    <div class="linea"></div>
                    <div class="cargo">Tabulación / Verificación</div>
                    <div class="nombre">Control de servicios</div>
                </td>
            </tr>
        </table>

        
        <footer>
            <div>
                Documento generado por el Sistema de Gestión Lusa -
                <?php echo e(now()->format('d/m/Y H:i')); ?>

            </div>
            <div class="aviso">
                Este documento es un comprobante interno de servicio. No constituye un contrato
                legal vinculante, sino una constancia de las actividades realizadas sobre la unidad
                y del estado de los equipos tecnológicos al momento del servicio.
            </div>
            <div style="margin-top: 5px; font-size: 8px;">
                Folio: MTO-<?php echo e(str_pad($documento->id, 6, '0', STR_PAD_LEFT)); ?> |
                Unidad: <?php echo e($documento->asignacion->unidad->numero_economico); ?> |
                Página 1 de 1
            </div>
        </footer>

    </div>

</body>

</html><?php /**PATH C:\Users\hulis\lusa-gestion-web\resources\views/admin/documentos/plantilla_mantenimiento.blade.php ENDPATH**/ ?>