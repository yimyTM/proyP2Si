# FICCT Admisión — Sistema de Admisión del Curso Preuniversitario

Sistema web para gestionar el proceso de admisión del **Curso Preuniversitario (CUP)** de la Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones (**FICCT – UAGRM**): desde la inscripción del postulante (con pago en línea) y la postulación/contratación de docentes, hasta la conformación de grupos, el registro de calificaciones y la generación de reportes institucionales.

---

## 🧱 Stack tecnológico

| Capa | Tecnología |
|------|------------|
| Backend | PHP **8.2+**, Laravel **12** |
| Base de datos | **PostgreSQL** |
| Frontend | Blade + **Tailwind CSS** (CDN) / Vite + Tailwind 4 |
| Pagos | **Stripe** (`stripe/stripe-php`) — Stripe Checkout |
| PDF | **dompdf** (`barryvdh/laravel-dompdf`) |
| Autenticación | Sesiones de Laravel + RBAC propio (roles y permisos) |
| Correo | SMTP (Gmail) — recuperación de contraseña |

---

## ✅ Requisitos previos

- PHP 8.2 o superior (extensiones: `pdo_pgsql`, `zip`, `mbstring`, `fileinfo`)
- Composer 2
- PostgreSQL 14+
- Node.js 18+ (opcional; las vistas ya usan Tailwind por CDN)
- Una cuenta de **Stripe** en modo test (claves `pk_test_…` / `sk_test_…`)

---

## 🚀 Instalación

```bash
# 1. Clonar e instalar dependencias
git clone <repo>
cd proyP2Si
composer install

# 2. Configurar el entorno
cp .env.example .env          # o crea el .env y copia las variables de abajo
php artisan key:generate

# 3. Crear la base de datos en PostgreSQL
#    (por defecto el proyecto usa la BD "dbCup")
createdb dbCup                # o créala con tu cliente preferido

# 4. Migrar y poblar con datos semilla
php artisan migrate
php artisan db:seed --class=poblacionCompleta

# 5. (opcional) Compilar assets con Vite
npm install && npm run build

# 6. Levantar el servidor
php artisan serve
# → http://localhost:8000
```

---

## ⚙️ Variables de entorno clave (`.env`)

```env
APP_NAME="FICCT Admisión"
APP_URL=http://localhost:8000
APP_LOCALE=es

# Base de datos PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=dbCup
DB_USERNAME=postgres
DB_PASSWORD=********

# Sesión (la cookie muere al cerrar el navegador)
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_EXPIRE_ON_CLOSE=true

# Stripe (modo test)
STRIPE_KEY=pk_test_xxxxx
STRIPE_SECRET=sk_test_xxxxx
STRIPE_CURRENCY=usd                 # moneda del cobro
STRIPE_MONTO_INSCRIPCION=15000      # monto en centavos (15000 = 150.00 USD)

# Correo (recuperación de contraseña)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=tucorreo@gmail.com
MAIL_PASSWORD=clave_de_aplicacion   # App Password de Gmail, no la contraseña normal
MAIL_FROM_ADDRESS="tucorreo@gmail.com"
MAIL_FROM_NAME="Sistema de Admisión FICCT"
```

---

## 👥 Roles del sistema

El acceso se controla con **RBAC** (roles + permisos asignables desde el panel de *Roles y Permisos*).

| Rol | Acceso principal |
|-----|------------------|
| **Administrador** | Acceso total al sistema |
| **Autoridades** | Reportes y estadísticas (lectura) |
| **Coordinador** | Gestión académica: grupos, docentes, postulantes |
| **Docente** | Registro de asistencia, notas y carga horaria propia |
| **Postulante** | Inscripción, expediente, pago y consulta de sus notas |

### Credenciales de prueba (datos semilla)

| Rol | Correo | Contraseña |
|-----|--------|------------|
| Administrador | `yimyt771@gmail.com` | `tarqui231A@` |
| Postulante (con notas) | `dflores@estudiante.bo` | *definida en el seeder* |

> Las contraseñas de las demás cuentas están en `database/seeders/poblacionCompleta.php`. Si necesitas restablecer una, usa el panel de administración o `php artisan tinker`.

---

## 💳 Probar la pasarela de pago (Stripe test)

1. Regístrate como postulante desde **Inscribirme** y avanza hasta el **Paso 3 – Pago**.
2. Pulsa **Pagar con Stripe** y usa una tarjeta de prueba:

| Resultado | Tarjeta | Fecha | CVC |
|-----------|---------|-------|-----|
| ✅ Aprobado | `4242 4242 4242 4242` | cualquiera futura | cualquiera |
| 🔐 Requiere 3D Secure | `4000 0025 0000 3155` | " | " |
| ❌ Rechazada | `4000 0000 0000 9995` | " | " |

Al confirmarse el pago se registra el **Pago + Comprobante** y la inscripción pasa a estado **Habilitado**.

---

## 📦 Módulos / Casos de uso implementados

- **Autenticación** — login con bloqueo progresivo por intentos fallidos (30 s → 2 min → 15 min) y recuperación de contraseña por correo.
- **Roles y Permisos** — asignación dinámica de permisos por rol agrupados en **5 módulos** (Seguridad, Inscripción, Planificación, Evaluación, Reportes) mediante acordeón; las vistas se adaptan según los permisos.
- **Landing pública** — inscripción de postulantes y **postulación de docentes**.
- **Inscripción de postulantes** — datos personales + **foto** → documentos → **pago con Stripe** → inscrito.
- **Postulación / Contratación de docentes (CU15)** — registro de formación y requisitos; el administrador valida y **contrata** (recién ahí se crea la cuenta del docente).
- **Carga masiva por CSV** — de postulantes (con **cálculo automático de grupos**, máx. 70 por grupo) y de personal/docentes; con vista previa y confirmación.
- **Grupos, turnos, aulas y asignación docente (CU06/07)** — un docente solo puede asignarse a un grupo si está **contratado** en la gestión.
- **Admisión por carrera (CU13)** — distribución automática: mejores promedios van a su **1ª opción** (Admitido), luego a la **2ª opción** (Reubicado); quienes no alcanzan cupo o puntaje quedan como **Reprobado**.
- **Calificaciones y resultados (CU11/CU12)** — regla académica única: **cada materia debe alcanzar 60**; si una nota es menor, el postulante **reprueba**.
- **Panel de reportes (CU14)** — selector de 8 reportes con filtros (gestión / reporte / grupo) y exportación a **PDF** y **CSV**:
  1. Lista general de postulantes · 2. Aprobados · 3. Reprobados · 4. Promedios generales · 5. Grupos habilitados · 6. Estadísticas por materia · 7. Docentes por grupos · 8. Grupos con más aprobados.
- **Resultados del postulante (CU16)** — el estudiante consulta sus notas por materia y parcial desde su panel.
- **Expedientes digitales** — el admin/coordinador puede validar documentos de cada postulante individualmente o con **selección múltiple** (acción masiva), y descargar la foto y cada archivo del expediente directamente desde el panel.
- **Bitácora** — registro de eventos relevantes del sistema.

---

## 🗂️ Estructura relevante

```
app/
 ├─ Http/Controllers/        # Controladores por módulo (Admin/, Docente/, …)
 ├─ Http/Middleware/         # CheckRole (RBAC), NoCacheHeaders
 ├─ Models/                  # Modelos Eloquent
 └─ Services/                # ResultadoAcademicoService, SpreadsheetParser,
                             #   CuentaProvisionaService, BitacoraService
config/services.php          # Configuración de Stripe
database/
 ├─ migrations/              # Esquema de la BD
 └─ seeders/poblacionCompleta.php   # Datos semilla (estado congelado)
resources/views/             # Vistas Blade (admin/, docente/, postulante/, registro/, …)
routes/web.php               # Rutas de la aplicación
```

---

## 🛠️ Comandos útiles

```bash
php artisan serve                 # Servidor de desarrollo
php artisan migrate:fresh --seed  # Reconstruir BD + datos semilla
php artisan db:seed --class=poblacionCompleta
php artisan storage:link          # Exponer storage/app/public como public/storage (fotos y documentos)
php artisan config:clear          # Limpiar caché de configuración (tras editar .env)
php artisan config:cache          # Cachear configuración (producción)
php artisan route:cache           # Cachear rutas (producción)
php artisan route:list            # Ver todas las rutas
php artisan tinker                # Consola interactiva
```

---

## 📝 Notas

- El **esquema de base de datos y los datos semilla están congelados** (`poblacionCompleta`); las funcionalidades se construyen sobre esa base.
- El proyecto usa **Tailwind por CDN** en las vistas, por lo que `npm run build` es opcional para desarrollo.
- Tras editar el `.env`, ejecuta `php artisan config:clear` para que los cambios tomen efecto.
- Los **archivos subidos** (fotos y documentos de expedientes) se almacenan en `storage/app/public/`. Es obligatorio ejecutar `php artisan storage:link` una sola vez tras el despliegue para que sean accesibles desde el navegador.
- En producción (**EC2 con EBS**) el storage local persiste entre reinicios. Si en el futuro se necesita escalar a múltiples instancias, migrar a `FILESYSTEM_DISK=s3` en el `.env` y configurar las credenciales de AWS.

---

> Proyecto académico — Sistemas de Información, 2º Parcial. FICCT, UAGRM.
