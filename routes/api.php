class ApiEndpoints {
  static const String baseUrl = 'http://100.94.214.0:8000';
  static const String apiPrefix = '/api';

  // Auth
  static const String login = '$apiPrefix/login';
  static const String logout = '$apiPrefix/logout';
  static const String perfil = '$apiPrefix/perfil';

  // Catalogos (solo GET)
  static const String unidades = '$apiPrefix/unidades';
  static const String operadores = '$apiPrefix/operadores';
  static const String asignaciones = '$apiPrefix/asignaciones';

  // Documentos (solo POST)
  static const String documentosMantenimiento = '$apiPrefix/documentos-mantenimiento';
  static const String documentosCapacitacion = '$apiPrefix/documentos-capacitacion';

  // Movimientos (solo POST)
  static const String movimientos = '$apiPrefix/movimientos';

  // Health
  static const String health = '$apiPrefix/health';
}