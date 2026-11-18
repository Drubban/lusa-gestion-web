<?php

return [
    // Secreto compartido con la app para firmar peticiones (HMAC-SHA256). Vacio = desactivado.
    'firma_app_secreto' => env('APP_SIGNATURE_SECRET'),
    // Ventana de tolerancia en segundos para el timestamp de la firma (anti-replay)
    'firma_app_ventana' => (int) env('APP_SIGNATURE_WINDOW', 300),
    // Vigencia del token de la app en minutos
    'token_app_minutos' => (int) env('APP_TOKEN_MINUTES', 60 * 12),
    // Bloqueo de cuenta tras intentos fallidos
    'max_intentos_login' => 5,
    'minutos_bloqueo' => 15,
    // Una unidad solo puede estar dentro de un departamento a la vez
    'estancia_unica_por_unidad' => (bool) env('ESTANCIA_UNICA_POR_UNIDAD', true),
];
