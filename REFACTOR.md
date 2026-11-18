# Lusa Gestion (Laravel) - Refactorizacion

Revision y refactor del panel web y de la API que consume la app movil.

## 1. Problemas encontrados (resumen)

**Seguridad (criticos)**
- Todo `/admin/*` era publico: no habia login ni middleware en las rutas.
- PDFs de documentos accesibles sin sesion por id consecutivo (`/documentos-mantenimiento/{id}/pdf`).
- `routes/api.php` contenia codigo Dart (las rutas de la API se habian perdido).
- El login de la API escribia en el log el hash de la contrasena.
- Ruta `/csrf-token` de depuracion y excepciones de CSRF en rutas que modifican datos.
- Errores de la API devolvian mensaje interno, archivo y linea.
- Exportacion "Excel" armaba HTML sin escapar (XSS) y el CSV permitia inyeccion de formulas.
- `.env` con contrasena real de PostgreSQL y `APP_DEBUG=true` dentro del zip, y la carpeta `storage/debugbar` con volcados de peticiones.
- Contrasenas de seeder `12345678`, sin bloqueo por intentos.

**Base de datos (codigo espagueti)**
- Firmas guardadas de 3 formas a la vez (FK a `firmas_digitales`, columnas text y columnas longText).
- Documentos con `asignacion_id` + `unidad_id` + `operador_id`; el admin creaba asignaciones con operador nulo (falla por NOT NULL).
- `documento_capacitacion`: el modelo usaba columnas que no existian.
- `unidades.codigo_qr` sin uso y `equipo_telpo/gps/barras` duplicando `tecnologias`.
- Inventario con dos juegos de campos para lo mismo (`nombre_equipo/marca` vs `nombre_producto/marca_producto`).
- `ajustes.clave_operador` y `ajustes.zona` (texto) duplicados; `revisiones.nombre_unidad` duplicado.
- Dos modelos de usuario (`User` sin tabla y `UsuarioDepartamento`); los `created_by` del panel apuntaban a la tabla equivocada.
- Validacion `exists:unidads` (tabla inexistente) en revisiones.

**Codigo**
- Logica de asignar operador-unidad copiada 7 veces; motivos de no presentado copiados 4 veces; semaforo de mantenimiento duplicado (y dentro de un try/catch repetido).
- Dashboard con una consulta por unidad (N+1) y ~50 variables a la vista.
- Sin FormRequests; create/edit duplicados en cada vista; CSS y JS en linea por todas partes.

## 2. Que se hizo

### Base de datos (migraciones nuevas `2026_10_30_*`)
Las migraciones originales NO se tocaron (ya corrieron en el servidor). Las nuevas transforman el esquema conservando los datos:

| Migracion | Cambio |
|---|---|
| `000001_unificar_usuarios` | `usuarios_departamento` -> `usuarios` con `rol` (admin/supervisor/departamento), bloqueo por intentos, ultimo acceso. |
| `000002_normalizar_documentos_y_firmas` | Firmas migradas a `firmas_digitales` (con hash SHA-256); se eliminan `asignacion_id`, `rol` y columnas de firma; `uuid` para idempotencia; columnas faltantes de capacitacion. |
| `000003_normalizar_unidades` | Se eliminan `codigo_qr` y `equipo_*`; `token_qr` admite 64 caracteres (los QR impresos siguen validos). |
| `000004_normalizar_inventario` | Campos fusionados en `nombre, marca, modelo, numero_serie, cantidad, descripcion`. |
| `000005_normalizar_ajustes_y_revisiones` | `ajustes.zona_id` (FK), sin `clave_operador`; revisiones sin `nombre_unidad`. |
| `000006_movimientos_con_estancias` | `entrada_id` y `duracion_minutos` (cada salida apunta a su entrada), `uuid`, emparejamiento del historico. |
| `000007_crear_bitacora_accesos` | Bitacora de auditoria (quien, que, desde donde, web o app). |

El SQL de transformacion se probo contra PostgreSQL 16.

### Seguridad
- Login del panel con limite de intentos, bloqueo temporal de cuenta y regeneracion de sesion.
- Middleware `panel` (admin/supervisor) y `admin` (usuarios e importacion).
- **Hashids**: ningun id numerico viaja en URLs ni en la API. Cada modelo usa su propia sal (`App\Support\Hashid`, trait `HasHashid`); los formularios envian hashes y `FormularioBase` los decodifica antes de validar.
- **Firma de peticiones (HMAC-SHA256)** de la app con timestamp + nonce de un solo uso (anti-replay): `VerificarFirmaApp`.
- Tokens Sanctum con vencimiento y permisos (abilities) segun el perfil; un token por dispositivo.
- Cabeceras de seguridad y CSP sin scripts en linea (`CabecerasSeguridad`).
- PDFs con `isRemoteEnabled=false` (evita SSRF), CSV con proteccion contra formulas.
- QR: contiene solo un token aleatorio de 40 caracteres, regenerable desde la ficha de la unidad.

### Codigo
- Servicios: `AsignacionService`, `MovimientoService` (reglas de estancia), `DocumentoService`, `AgendamientoService`, `EstadoMantenimientoService`, `BitacoraService`.
- Enums: roles, estados, motivos, categorias, tipos de tecnologia, semaforo.
- FormRequests para todos los formularios (mismas reglas para panel y app).
- API Resources para respuestas JSON consistentes.
- Si una unidad u operador tiene historial, "eliminar" lo desactiva en lugar de borrar el historial.

### Diseno
Tema minimalista claro (`public/css/lusa.css`) con la paleta de la flota: azul Lusa `#1D5FAD`, cielo `#3A8FD9`, amarillo `#E8A814`, y verde `#2E9E6A`, morado `#6E4FB8` y negro `#1B2333` como colores de estado. Componentes Blade reutilizables (`resources/views/components`) y un solo `form.blade.php` por modulo para crear y editar. Capturas de referencia en `docs/capturas/`.

## 3. Instalacion

> Haz un respaldo completo de la base antes de migrar: `pg_dump -Fc transporte_db > respaldo.dump`

```bash
composer require hashids/hashids
# Requiere la extension bcmath o gmp de PHP (apt install php8.3-bcmath)

cp .env.example .env            # completar valores
php artisan key:generate
# HASHIDS_SALT y APP_SIGNATURE_SECRET: cadenas aleatorias largas
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"

php artisan migrate
php artisan db:seed --class=UsuarioSeeder   # crea el admin y muestra las contrasenas una sola vez
php artisan storage:link
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

Usuarios existentes: la migracion les asigna rol `departamento`. Para dar acceso al panel a alguien:
`UPDATE usuarios SET rol = 'admin' WHERE nombre_usuario = '...';` (o desde el panel una vez que exista un admin).

`bootstrap/app.php` se incluye completo (registro de middleware y manejo de errores). Si el tuyo tiene ajustes propios, combinalos.

### Urgente
1. Cambia la contrasena del usuario `postgres` (estaba en el `.env` del zip) y crea un usuario propio para la app.
2. Borra `storage/debugbar/` del servidor y desinstala debugbar en produccion.
3. Pon `APP_DEBUG=false`.

## 4. API para la app (`/api`)

Todas las rutas responden JSON, aceptan la firma HMAC y usan ids ofuscados.

| Metodo | Ruta | Descripcion |
|---|---|---|
| POST | `/login` | `nombre_usuario`, `password`, `dispositivo` -> token, vencimiento, usuario y permisos |
| POST | `/logout` | Revoca el token actual |
| GET | `/resumen` | Indicadores de inicio |
| GET | `/departamentos`, `/unidades`, `/operadores` | Catalogos (cacheables en la app) |
| POST | `/unidades/qr` | `token` del QR -> unidad, estancia abierta y tipo sugerido |
| GET | `/movimientos`, `/movimientos/dentro` | Historial y estancias abiertas del departamento |
| POST | `/movimientos` | Registro individual (`uuid` obligatorio, idempotente) |
| POST | `/movimientos/sincronizar` | Lote de hasta 100 movimientos offline con resultado por uuid |
| POST | `/documentos/mantenimiento`, `/documentos/capacitacion` | Documentos con firmas PNG base64 |
| GET/POST | `/agendamientos`, `/agendamientos/{id}/cumplir`, `/agendamientos/{id}/no-presentado` | Agenda de mantenimiento |
| GET/POST/PATCH | `/revisiones` | Revisiones optocontrol |
| GET | `/inventario` | Consulta de inventario |

Firma: `X-App-Signature = hex(HMAC_SHA256(secreto, METODO\n/ruta\ntimestamp\nnonce\nsha256(cuerpo)))`, con `X-App-Timestamp` y `X-App-Nonce`.

## 5. Pendiente / recomendaciones
- Este entorno no tiene Composer con acceso a Packagist, asi que el proyecto no se pudo ejecutar completo: se verifico sintaxis PHP de todos los archivos, rutas y vistas referenciadas, el SQL de migracion en PostgreSQL y la logica de hashids y firma. Probar en un entorno de pruebas antes de produccion.
- Las tablas `barras`, `telpos` y `gps` repiten `telefono` y `plan`; se puede unificar en una tabla de lineas/SIM en una siguiente etapa.
- Configurar HTTPS en el servidor; mientras tanto la app solo permite HTTP hacia las IPs internas listadas.
