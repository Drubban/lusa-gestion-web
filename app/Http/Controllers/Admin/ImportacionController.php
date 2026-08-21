<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AsignacionOperadorUnidad;
use App\Models\Operador;
use App\Models\Unidad;
use App\Models\Zona;
use App\Models\Barra;
use App\Models\Telpo;
use App\Models\Gps;
use App\Models\Mdvr;
use App\Models\Tecnologia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportacionController extends Controller
{
    /**
     * Limpiar texto para UTF-8 válido
     */
    private function limpiarUtf8($texto)
    {
        if ($texto === null) {
            return '';
        }
        $texto = mb_convert_encoding($texto, 'UTF-8', 'UTF-8');
        $texto = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $texto);

        return trim($texto);
    }

    public function index()
    {
        return view('admin.importacion.index');
    }

    public function importarUnidades(Request $request)
    {
        Log::info('Iniciando importación de unidades');

        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $archivo = $request->file('archivo')->getRealPath();
        Log::info('Archivo temporal: ' . $archivo);

        if (! file_exists($archivo) || ! is_readable($archivo)) {
            Log::error('Archivo no existe o no se puede leer');

            return back()->with('error', 'No se pudo leer el archivo. Verifica permisos.');
        }

        $handle = fopen($archivo, 'r');
        if ($handle === false) {
            Log::error('No se pudo abrir el archivo con fopen');

            return back()->with('error', 'Error al abrir el archivo.');
        }

        $cabeceras = fgetcsv($handle, 0, ',');
        if (! $cabeceras) {
            fclose($handle);
            Log::error('El CSV no tiene cabeceras');

            return back()->with('error', 'El archivo CSV está vacío o no tiene cabeceras.');
        }

        $cabeceras = array_map(function ($col) {
            return strtolower(trim($col, " \t\n\r\0\x0B\xEF\xBB\xBF"));
        }, $cabeceras);
        Log::info('Cabeceras detectadas: ', $cabeceras);

        $requeridas = ['numero_economico', 'zona'];
        foreach ($requeridas as $req) {
            if (! in_array($req, $cabeceras)) {
                fclose($handle);
                Log::error("Falta la columna requerida: {$req}");

                return back()->with('error', "El archivo debe contener la columna '{$req}'. Columnas detectadas: " . implode(', ', $cabeceras));
            }
        }

        $importadas = 0;
        $errores = [];
        $linea = 1;
        $zonasPermitidas = ['reyes', 'apaxco', 'citrus'];

        while (($datos = fgetcsv($handle, 0, ',')) !== false) {
            $linea++;
            if (count($datos) < count($cabeceras)) {
                $errores[] = "Línea {$linea}: número de columnas insuficiente (esperado " . count($cabeceras) . ', obtenido ' . count($datos) . ').';

                continue;
            }

            $fila = array_combine($cabeceras, array_slice($datos, 0, count($cabeceras)));

            if (empty($fila['numero_economico'])) {
                $errores[] = "Línea {$linea}: número económico vacío.";

                continue;
            }

            if (Unidad::where('numero_economico', $fila['numero_economico'])->exists()) {
                $errores[] = "Línea {$linea}: unidad {$fila['numero_economico']} ya existe. Se omite.";

                continue;
            }

            // Buscar o crear zona (insensible a mayúsculas)
            $zonaNombre = trim($fila['zona']);
            $zonaNombreLower = strtolower($zonaNombre);

            if (! in_array($zonaNombreLower, $zonasPermitidas)) {
                $errores[] = "Línea {$linea}: zona '{$fila['zona']}' no válida. Debe ser reyes, apaxco o citrus.";

                continue;
            }

            $zona = Zona::whereRaw('LOWER(nombre) = ?', [$zonaNombreLower])->first();
            if (! $zona) {
                $zona = Zona::create([
                    'nombre' => $zonaNombreLower,
                    'activo' => true,
                ]);
                Log::info("Zona '{$zonaNombreLower}' creada automáticamente.");
            }

            try {
                Unidad::create([
                    'numero_economico' => $this->limpiarUtf8($fila['numero_economico']),
                    'nombre_unidad' => isset($fila['nombre_unidad']) ? $this->limpiarUtf8($fila['nombre_unidad']) : null,
                    'zona_id' => $zona->id,
                    'activo' => isset($fila['activo']) ? filter_var($fila['activo'], FILTER_VALIDATE_BOOLEAN) : true,
                    'codigo_qr' => (string) Str::uuid(),
                    'token_qr' => Str::random(20),
                ]);
                $importadas++;
            } catch (\Exception $e) {
                $errores[] = "Línea {$linea}: error al guardar - " . $e->getMessage();
            }
        }

        fclose($handle);

        Log::info('Importadas: ' . $importadas);
        Log::info('Errores: ', $errores);

        if ($importadas > 0) {
            return back()->with('success', "Se importaron {$importadas} unidades.")->with('errores', $errores);
        } else {
            return back()->with('error', 'No se importó ninguna unidad. Revisa los errores.')->with('errores', $errores);
        }
    }

    public function importarOperadores(Request $request)
    {
        Log::info('=== INICIANDO IMPORTACIÓN DE OPERADORES ===');

        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $archivo = $request->file('archivo');
        Log::info('Archivo recibido: ' . $archivo->getClientOriginalName());
        Log::info('Tamaño: ' . $archivo->getSize() . ' bytes');

        // Leer primera línea para detectar separador
        $contenido = file_get_contents($archivo->getRealPath());

        // Detectar separador (coma, punto y coma, o TAB)
        $primeraLinea = strtok($contenido, "\n");
        $separador = $this->detectarSeparador($primeraLinea);
        Log::info('Separador detectado: ' . ($separador === "\t" ? 'TAB' : ($separador === ';' ? 'PUNTO Y COMA' : 'COMA')));

        // Guardar archivo temporal para debugging
        $tempPath = storage_path('logs/import_operadores_' . date('Ymd_His') . '.csv');
        file_put_contents($tempPath, $contenido);
        Log::info('Archivo guardado para depuración: ' . $tempPath);

        // Leer líneas
        $lineas = preg_split('/\r\n|\r|\n/', $contenido);
        if (empty($lineas)) {
            return back()->with('error', 'El archivo está vacío.');
        }

        // Procesar cabeceras con el separador detectado
        $cabeceras = str_getcsv(trim($lineas[0]), $separador);
        $cabeceras = array_map(function ($col) {
            $col = preg_replace('/^\xEF\xBB\xBF/', '', $col);
            return strtolower(trim($col));
        }, $cabeceras);

        Log::info('Cabeceras detectadas:', $cabeceras);

        // Mapeo de columnas alternativas
        $mapaColumnas = [
            'clave_operador' => ['clave_operador', 'clave', 'codigo', 'id_operador'],
            'nombre_completo' => ['nombre_completo', 'nombre', 'operador', 'nombre_operador'],
            'unidad' => ['unidad', 'numero_economico', 'unidad_id', 'economico'],
            'zona_nombre' => ['zona_nombre', 'zona', 'area'],
            'activo' => ['activo', 'estado', 'status'],
        ];

        // Encontrar las columnas reales
        $columnaClave = null;
        $columnaNombre = null;
        $columnaUnidad = null;
        $columnaZona = null;
        $columnaActivo = null;

        foreach ($cabeceras as $col) {
            if (in_array($col, $mapaColumnas['clave_operador'])) $columnaClave = $col;
            if (in_array($col, $mapaColumnas['nombre_completo'])) $columnaNombre = $col;
            if (in_array($col, $mapaColumnas['unidad'])) $columnaUnidad = $col;
            if (in_array($col, $mapaColumnas['zona_nombre'])) $columnaZona = $col;
            if (in_array($col, $mapaColumnas['activo'])) $columnaActivo = $col;
        }

        // Validar columnas requeridas (solo clave y nombre son obligatorios)
        $faltantes = [];
        if (!$columnaClave) $faltantes[] = 'clave_operador';
        if (!$columnaNombre) $faltantes[] = 'nombre_completo';

        if (!empty($faltantes)) {
            return back()->with('error', 'El archivo debe contener las columnas: ' . implode(', ', $faltantes) .
                '. Detectadas: ' . implode(', ', $cabeceras));
        }

        $importadas = 0;
        $errores = [];
        $lineaNumero = 1;

        // Procesar cada línea
        for ($i = 1; $i < count($lineas); $i++) {
            $lineaNumero++;
            $linea = trim($lineas[$i]);
            if (empty($linea)) {
                continue;
            }

            $datos = str_getcsv($linea, $separador);

            if (count($datos) < count($cabeceras)) {
                $errores[] = "Línea {$lineaNumero}: Número de columnas insuficiente.";
                continue;
            }

            // Crear array asociativo
            $fila = [];
            for ($j = 0; $j < count($cabeceras); $j++) {
                $fila[$cabeceras[$j]] = $datos[$j] ?? '';
            }

            // Obtener valores usando las columnas detectadas
            $clave = trim($fila[$columnaClave] ?? '');
            $nombreCompleto = trim($fila[$columnaNombre] ?? '');
            $numeroEconomico = $columnaUnidad ? trim($fila[$columnaUnidad] ?? '') : '';
            $zonaNombre = $columnaZona ? trim($fila[$columnaZona] ?? '') : '';

            Log::info("Procesando línea {$lineaNumero}: Clave='{$clave}', Nombre='{$nombreCompleto}', Unidad='{$numeroEconomico}', Zona='{$zonaNombre}'");

            // Validar clave (4 o 5 dígitos)
            if (empty($clave)) {
                $errores[] = "Línea {$lineaNumero}: Clave de operador vacía.";
                continue;
            }

            if (!preg_match('/^\d{4,5}$/', $clave)) {
                $errores[] = "Línea {$lineaNumero}: Clave '{$clave}' debe tener 4 o 5 dígitos.";
                continue;
            }

            if (Operador::where('clave_operador', $clave)->exists()) {
                $errores[] = "Línea {$lineaNumero}: Operador con clave {$clave} ya existe.";
                continue;
            }

            // Validar nombre
            if (empty($nombreCompleto)) {
                $errores[] = "Línea {$lineaNumero}: Nombre completo vacío.";
                continue;
            }

            // Buscar unidad por número económico (si se proporcionó)
            $unidad = null;
            if (!empty($numeroEconomico)) {
                $unidad = Unidad::where('numero_economico', $numeroEconomico)->first();
                if (!$unidad) {
                    $errores[] = "Línea {$lineaNumero}: Unidad '{$numeroEconomico}' no existe. El operador se creará sin unidad asignada.";
                    // No continuamos, permitimos crear operador sin unidad
                }
            }

            // Validar zona (si se proporciona y se puede validar)
            if (!empty($zonaNombre)) {
                $zonaNombreLower = strtolower($zonaNombre);
                $zonasPermitidas = ['reyes', 'apaxco', 'citrus'];
                if (!in_array($zonaNombreLower, $zonasPermitidas)) {
                    $errores[] = "Línea {$lineaNumero}: Zona '{$zonaNombre}' no válida. Debe ser reyes, apaxco o citrus.";
                    continue;
                }
            }

            // Activo (si existe la columna)
            $activo = true;
            if ($columnaActivo && isset($fila[$columnaActivo])) {
                $valor = strtolower(trim($fila[$columnaActivo]));
                $activo = in_array($valor, ['1', 'true', 'si', 'activo', 'sí', 'yes']);
            }

            try {
                $operador = Operador::create([
                    'clave_operador' => $clave,
                    'nombre_completo' => $nombreCompleto,
                    'activo' => $activo,
                ]);

                Log::info("✓ Operador creado ID: {$operador->id}, Clave: {$clave}");

                // Asignar unidad solo si existe y se proporcionó
                if ($unidad) {
                    // Finalizar asignaciones anteriores del operador
                    AsignacionOperadorUnidad::where('operador_id', $operador->id)
                        ->where('vigente', true)
                        ->update(['fecha_fin' => now(), 'vigente' => false]);

                    AsignacionOperadorUnidad::create([
                        'operador_id' => $operador->id,
                        'unidad_id' => $unidad->id,
                        'fecha_inicio' => now(),
                        'vigente' => true,
                    ]);
                    Log::info("✓ Operador {$clave} asignado a unidad {$numeroEconomico}");
                } else {
                    Log::info("✓ Operador {$clave} creado sin unidad asignada");
                }

                $importadas++;
            } catch (\Exception $e) {
                $errores[] = "Línea {$lineaNumero}: Error al guardar - " . $e->getMessage();
                Log::error("Error línea {$lineaNumero}: " . $e->getMessage());
            }
        }

        Log::info("=== IMPORTACIÓN FINALIZADA: {$importadas} operadores importados, " . count($errores) . " errores ===");

        $mensaje = "Se importaron {$importadas} operadores.";
        if (!empty($errores)) {
            $mensaje .= " Hubo " . count($errores) . " errores.";
            return back()->with('success', $mensaje)->with('errores', $errores);
        }

        return back()->with('success', $mensaje);
    }

    public function importarTecnologias(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('archivo');
        $handle = fopen($file->getPathname(), 'r');

        // Leer cabeceras
        $headers = fgetcsv($handle, 0, ',');
        if (!$headers) {
            fclose($handle);
            return back()->withErrors(['error' => 'El archivo CSV está vacío o no tiene cabeceras.']);
        }

        // Limpiar cabeceras
        $headersLower = array_map(function ($h) {
            return strtolower(trim($h));
        }, $headers);

        Log::info('📋 Cabeceras detectadas:', $headersLower);

        // Validar columnas mínimas
        $minimos = ['numero_economico', 'tipo'];
        $faltan = array_diff($minimos, $headersLower);
        if (!empty($faltan)) {
            fclose($handle);
            return back()->withErrors([
                'error' => 'El archivo debe contener las columnas: ' . implode(', ', $faltan)
            ]);
        }

        $importados = 0;
        $errores = [];
        $filaNumero = 1;
        $userId = 1; // Usuario Drubban

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                $filaNumero++;

                // Saltar filas vacías
                if (empty(array_filter($row))) {
                    continue;
                }

                // Asegurar que la fila tenga suficientes columnas
                $data = [];
                foreach ($headersLower as $index => $key) {
                    $data[$key] = isset($row[$index]) ? trim($row[$index]) : '';
                }

                // Limpiar datos
                $numeroEconomico = $data['numero_economico'] ?? '';
                $tipo = strtolower($data['tipo'] ?? '');

                Log::info("📌 Procesando fila {$filaNumero}: Unidad={$numeroEconomico}, Tipo={$tipo}");

                // Validar número económico
                if (empty($numeroEconomico)) {
                    $errores[] = "Fila {$filaNumero}: Número económico vacío";
                    continue;
                }

                // Buscar unidad
                $unidad = Unidad::where('numero_economico', $numeroEconomico)->first();
                if (!$unidad) {
                    $errores[] = "Fila {$filaNumero}: Unidad '{$numeroEconomico}' no encontrada";
                    continue;
                }

                // Validar tipo
                $tiposValidos = ['barras', 'telpo', 'gps', 'mdvr'];
                if (!in_array($tipo, $tiposValidos)) {
                    $errores[] = "Fila {$filaNumero}: Tipo '{$tipo}' no válido. Debe ser: " . implode(', ', $tiposValidos);
                    continue;
                }

                // Verificar duplicado
                $existe = Tecnologia::where('unidad_id', $unidad->id)
                    ->where('tipo', $tipo)
                    ->exists();

                if ($existe) {
                    $errores[] = "Fila {$filaNumero}: La unidad '{$numeroEconomico}' ya tiene tecnología tipo '{$tipo}'";
                    continue;
                }

                // 🔥 CREAR TECNOLOGÍA
                $tecnologia = Tecnologia::create([
                    'unidad_id' => $unidad->id,
                    'tipo' => $tipo,
                    'nombre' => !empty($data['nombre']) ? $data['nombre'] : null,
                    'activo' => isset($data['activo']) ? filter_var($data['activo'], FILTER_VALIDATE_BOOLEAN) : true,
                    'created_by' => $userId,
                ]);

                Log::info("✅ Tecnología creada ID: {$tecnologia->id} - Tipo: {$tipo}");

                // 🔥 CREAR DATOS ESPECÍFICOS SEGÚN TIPO
                $this->crearDatosEspecificos($tecnologia, $data, $tipo, $filaNumero, $errores);

                $importados++;
            }

            DB::commit();
            fclose($handle);

            $mensaje = "✅ Se importaron {$importados} tecnologías correctamente.";
            if (!empty($errores)) {
                $mensaje .= " ⚠️ Con " . count($errores) . " errores.";
                return back()->with('success', $mensaje)->with('errores', $errores);
            }

            return redirect()->route('admin.importar.index')->with('success', $mensaje);
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            Log::error('❌ Error al importar tecnologías: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return back()->withErrors([
                'error' => 'Error al importar: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 🔥 CREAR DATOS ESPECÍFICOS - VERSIÓN MEJORADA
     */
    private function crearDatosEspecificos($tecnologia, $data, $tipo, $filaNumero, &$errores)
    {
        try {
            switch ($tipo) {
                case 'barras':
                    $barraData = ['tecnologia_id' => $tecnologia->id];
                    $tieneDatos = false;

                    if (!empty($data['id_barra'])) {
                        $barraData['id_barra'] = $data['id_barra'];
                        $tieneDatos = true;
                    }
                    if (!empty($data['barras'])) {
                        $barraData['barras'] = $data['barras'];
                        $tieneDatos = true;
                    }
                    if (!empty($data['telefono_barras'])) {
                        $barraData['telefono'] = $data['telefono_barras'];
                        $tieneDatos = true;
                    }
                    if (!empty($data['plan_barras'])) {
                        $barraData['plan'] = $data['plan_barras'];
                        $tieneDatos = true;
                    }

                    if ($tieneDatos) {
                        Barra::create($barraData);
                        Log::info("✅ Datos de Barras creados para tecnología ID: {$tecnologia->id}");
                    } else {
                        Log::info("⚠️ Sin datos de Barras para tecnología ID: {$tecnologia->id}");
                    }
                    break;

                case 'telpo':
                    $telpoData = ['tecnologia_id' => $tecnologia->id];
                    $tieneDatos = false;

                    if (!empty($data['imei_antes'])) {
                        $telpoData['imei_antes'] = $data['imei_antes'];
                        $tieneDatos = true;
                    }
                    if (!empty($data['v_apk'])) {
                        $telpoData['v_apk'] = $data['v_apk'];
                        $tieneDatos = true;
                    }
                    if (!empty($data['telpo'])) {
                        $telpoData['telpo'] = $data['telpo'];
                        $tieneDatos = true;
                    }
                    if (!empty($data['imei_telpo'])) {
                        $telpoData['imei_telpo'] = $data['imei_telpo'];
                        $tieneDatos = true;
                    }
                    if (!empty($data['telefono_telpo'])) {
                        $telpoData['telefono'] = $data['telefono_telpo'];
                        $tieneDatos = true;
                    }
                    if (!empty($data['plan_telpo'])) {
                        $telpoData['plan'] = $data['plan_telpo'];
                        $tieneDatos = true;
                    }
                    if (!empty($data['costo_plan'])) {
                        $telpoData['costo_plan'] = floatval($data['costo_plan']);
                        $tieneDatos = true;
                    }

                    if ($tieneDatos) {
                        Telpo::create($telpoData);
                        Log::info("✅ Datos de Telpo creados para tecnología ID: {$tecnologia->id}");
                    } else {
                        Log::info("⚠️ Sin datos de Telpo para tecnología ID: {$tecnologia->id}");
                    }
                    break;

                case 'gps':
                    $gpsData = ['tecnologia_id' => $tecnologia->id];
                    $tieneDatos = false;

                    // 🔥 Manejar IMEI en formato científico
                    if (!empty($data['imei_gps'])) {
                        $imei = $data['imei_gps'];
                        // Si es formato científico (ej: 8.64893E+14), convertirlo
                        if (is_numeric($imei) && strpos($imei, 'E') !== false) {
                            $imei = number_format(floatval($imei), 0, '.', '');
                        }
                        $gpsData['imei_gps'] = $imei;
                        $tieneDatos = true;
                    }
                    if (!empty($data['telefono_gps'])) {
                        $gpsData['telefono'] = $data['telefono_gps'];
                        $tieneDatos = true;
                    }
                    if (!empty($data['plan_gps'])) {
                        $gpsData['plan'] = $data['plan_gps'];
                        $tieneDatos = true;
                    }

                    if ($tieneDatos) {
                        Gps::create($gpsData);
                        Log::info("✅ Datos de GPS creados para tecnología ID: {$tecnologia->id}");
                    } else {
                        Log::info("⚠️ Sin datos de GPS para tecnología ID: {$tecnologia->id}");
                    }
                    break;

                case 'mdvr':
                    $mdvrData = ['tecnologia_id' => $tecnologia->id];
                    $tieneDatos = false;

                    if (!empty($data['dvr'])) {
                        $mdvrData['dvr'] = $data['dvr'];
                        $tieneDatos = true;
                    }
                    if (!empty($data['modelo'])) {
                        $mdvrData['modelo'] = $data['modelo'];
                        $tieneDatos = true;
                    }
                    if (!empty($data['camaras'])) {
                        $mdvrData['camaras'] = $data['camaras'];
                        $tieneDatos = true;
                    }
                    if (!empty($data['memoria'])) {
                        $mdvrData['memoria'] = $data['memoria'];
                        $tieneDatos = true;
                    }

                    if ($tieneDatos) {
                        Mdvr::create($mdvrData);
                        Log::info("✅ Datos de MDVR creados para tecnología ID: {$tecnologia->id}");
                    } else {
                        Log::info("⚠️ Sin datos de MDVR para tecnología ID: {$tecnologia->id}");
                    }
                    break;
            }
        } catch (\Exception $e) {
            $errores[] = "Fila {$filaNumero}: Error al guardar datos específicos - " . $e->getMessage();
            Log::error("❌ Error en fila {$filaNumero}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Descargar plantilla de tecnologías
     */
    public function descargarPlantillaTecnologias()
    {
        $headers = [
            'numero_economico',
            'tipo',
            'nombre',
            'activo',
            'id_barra',
            'barras',
            'telefono_barras',
            'plan_barras',
            'imei_antes',
            'v_apk',
            'telpo',
            'imei_telpo',
            'telefono_telpo',
            'plan_telpo',
            'costo_plan',
            'imei_gps',
            'telefono_gps',
            'plan_gps',
            'dvr',
            'modelo',
            'camaras',
            'memoria',
        ];

        $ejemplos = [
            [
                'ECO-001',
                'barras',
                'Barras Ejemplo',
                '1',
                'BAR-001',
                'OptoControl',
                '525591958672',
                'Plan Empresarial',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ],
            [
                'ECO-002',
                'telpo',
                'Telpo Ejemplo',
                '1',
                '',
                '',
                '',
                '',
                '123456789012345',
                '4.9.2',
                'T20',
                '987654321098765',
                '5512345678',
                'Plan Premium',
                '749',
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ],
            [
                'ECO-003',
                'gps',
                'GPS Ejemplo',
                '1',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '123456789012346',
                '5512345679',
                'Plan Básico',
                '',
                '',
                '',
                ''
            ],
            [
                'ECO-004',
                'mdvr',
                'MDVR Ejemplo',
                '1',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'DVR-001',
                'Modelo X',
                '4',
                '256GB'
            ],
        ];

        $handle = fopen('php://memory', 'w');
        fputcsv($handle, $headers);
        foreach ($ejemplos as $ejemplo) {
            fputcsv($handle, $ejemplo);
        }
        fseek($handle, 0);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="plantilla_tecnologias.csv"',
        ]);
    }

    /**
     * Detecta el separador de una línea CSV
     */
    private function detectarSeparador($linea)
    {
        $separadores = [',', ';', "\t"];
        $cuentas = [];
        foreach ($separadores as $sep) {
            $cuentas[$sep] = substr_count($linea, $sep);
        }
        arsort($cuentas);
        return key($cuentas);
    }
}
