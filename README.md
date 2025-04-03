# Red Social

Una red social simple desarrollada con PHP y MySQL.

## Características

- Registro de usuarios
- Inicio de sesión seguro
- Creación de publicaciones
- Visualización de publicaciones recientes
- Interfaz amigable con Bootstrap

## Requisitos

- PHP 7.0 o superior
- MySQL 5.6 o superior
- Servidor web (Apache, Nginx, etc.)

## Instalación

1. Clonar o descargar el repositorio
2. Crear una base de datos MySQL
3. Importar el archivo `database.sql` en tu base de datos:
   ```
   mysql -u usuario -p nombre_base_datos < database.sql
   ```
4. Configurar la conexión a la base de datos en `config.php`:
   ```php
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'tu_usuario');
   define('DB_PASSWORD', 'tu_contraseña');
   define('DB_NAME', 'nombre_base_datos');
   ```
5. Colocar los archivos en el directorio de tu servidor web

## Estructura del Proyecto

- `config.php` - Configuración de la base de datos
- `register.php` - Página de registro de usuarios
- `login.php` - Página de inicio de sesión
- `index.php` - Página principal con publicaciones
- `logout.php` - Cierre de sesión
- `database.sql` - Estructura de la base de datos

## Uso

1. Acceder a la página de registro para crear una cuenta
2. Iniciar sesión con tus credenciales
3. Crear y ver publicaciones
4. Cerrar sesión cuando termines

## Seguridad

- Contraseñas encriptadas con password_hash()
- Protección contra SQL injection
- Validación de entrada de datos
- Sesiones seguras

## Personalización

Puedes personalizar la apariencia modificando los estilos CSS en cada archivo PHP o agregando tu propio archivo de estilos. 
