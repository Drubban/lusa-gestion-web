# 1. Verificar estado
git status

# 2. Añadir TODOS los archivos modificados/nuevos
git add .

# 3. Hacer commit con mensaje descriptivo
git commit -m "fix: descripción de lo que hiciste"

# 4. Verificar en qué rama estás
git branch --show-current

# 5. Subir a la rama actual en el remoto
git push origin NOMBRE_DE_LA_RAMA

# Ejemplo si estás en Fixeds:
git push origin Fixeds

# 6. Subir también los tags (si usas versiones)
git push --tags origin



------------------------------------------------------
------------------------------------------------------
# 1. Cambiar a main
git checkout main

# 2. Traer lo último del remoto
git pull origin main

# 3. Fusionar Fixeds en main
git merge Fixeds

# 4. Subir main actualizado
git push origin main

# 5. (Opcional) Volver a Fixeds
git checkout Fixeds


------------------
Backups
# Laptop
# Primero, asegúrate de estar en la carpeta de PostgreSQL
cd "C:\Program Files\PostgreSQL\17\bin"

# Exportar en formato custom (recomendado - más rápido y comprimido)
.\pg_dump -U postgres -d transporte_db -F c -b -v -f "C:\backup\transporte_db.backup"

# Si prefieres formato SQL (legible)
.\pg_dump -U postgres -d transporte_db -F p -b -v -f "C:\backup\transporte_db.sql"

# Verificar que el archivo existe y su tamaño
dir C:\backup\transporte_db.backup

# O si usaste SQL:
dir C:\backup\transporte_db.sql


# Servidor
# Conectar a PostgreSQL y crear la BD
"C:\Program Files\PostgreSQL\17\bin\psql" -U postgres -c "CREATE DATABASE transporte_db;"
# Listar todas las bases de datos
"C:\Program Files\PostgreSQL\17\bin\psql" -U postgres -c "\l"

# Restaurar backup (formato custom)
"C:\Program Files\PostgreSQL\17\bin\pg_restore" -U postgres -d transporte_db -v "C:\backup\mi_bd.backup"
"C:\Program Files\PostgreSQL\17\bin\psql" -U postgres -d transporte_db -f "C:\backup\mi_bd.sql"


# Listar todas las tablas
"C:\Program Files\PostgreSQL\17\bin\psql" -U postgres -d transporte_db -c "\dt"

# Contar cuántas tablas hay
"C:\Program Files\PostgreSQL\17\bin\psql" -U postgres -d transporte_db -c "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='public';"

# Reiniciar Postgres
Restart-Service postgresql-x64-17

# Verificar
netstat -an | findstr :5432

# Desde tu laptop (reemplaza con la contraseña del servidor)
"C:\Program Files\PostgreSQL\17\bin\psql" -U postgres -h 100.88.212.32 -d transporte_db -c "SELECT version();"

# Verificar que los datos existen (cambia 'users' por tu tabla real)
"C:\Program Files\PostgreSQL\17\bin\psql" -U postgres -h 100.88.212.32 -d transporte_db -c "SELECT COUNT(*) FROM users;"