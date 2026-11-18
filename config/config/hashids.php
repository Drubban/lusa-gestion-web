<?php

return [
    // Sal secreta para ofuscar ids. Cambiarla invalida los enlaces existentes.
    'salt' => env('HASHIDS_SALT', env('APP_KEY')),
    'min_length' => (int) env('HASHIDS_MIN_LENGTH', 10),
    'alphabet' => 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789',
];
