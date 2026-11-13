# Feature: admin-cms — CMS administrativo completo del sitio

Objetivo: convertir el panel `/admin` en un CMS propio con dashboard real, configuración de la emisora persistida en base de datos (compatible con `RADIO_*`), CRUD de programación con horarios multi-día y locutores asociados, contenido de la página pública, galería de imágenes, redes sociales y contacto, y administración de cuentas administrativas. Sin métricas inventadas y sin botones decorativos.
Rama: `feature/admin-cms`. Entrega: commits por unidad de trabajo. Runner: `C:\php84\php.exe artisan test` (PHPUnit 12, SQLite en memoria). Build: `corepack pnpm run build`.

## Specs

- **S1 — Arquitectura por capas.** `App\Domain\Cms` contiene valores y reglas sin imports de Illuminate. `App\Application\Cms` contiene casos de uso, DTOs y contratos de repositorio. `App\Infrastructure\Persistence\Eloquent` implementa esos contratos. Los controladores de `App\Http\Controllers\Admin` validan, delegan y responden. Las migraciones son aditivas sobre SQLite (`database/database.sqlite`); no se cambia de motor ni se alteran tablas existentes.
- **S2 — Precedencia de configuración.** Orden explícito: (1) la configuración de transmisión guardada (URL, formato y nombre) cuando su URL es reproducible, (2) las variables de entorno `RADIO_STREAM_*` como bloque de respaldo cuando la base no aporta una URL reproducible, (3) estado «Transmisión no configurada». `RADIO_*` se conserva completo. Ninguna escritura administrativa puede dejar el reproductor roto en silencio: la URL se valida al guardar (`https` obligatorio; `http` solo en entorno `local`) y el proveedor vuelve a validar en lectura, cayendo al respaldo de entorno si el valor almacenado no es reproducible.
- **S3 — Módulo Emisora.** Formulario administrativo real sobre `station_settings` (fila única) con: nombre de la emisora, eslogan, descripción, logotipo (subida de imagen validada), URL del streaming, formato (`mp3`, `aac`, `hls`), nombre de la transmisión y estado (`activa`, `en mantenimiento`, `inactiva`). Toda modificación se persiste y se registra en el historial administrativo.
- **S4 — Estado de la emisora.** Con estado distinto de `activa` la página pública muestra un aviso explícito y el reproductor queda deshabilitado con un mensaje claro; nunca se muestra un control que falla al pulsarlo.
- **S5 — Programación (CRUD).** Programas con nombre, descripción, portada (imagen validada), estado (`borrador`, `publicado`, `inactivo`), orden de visualización, una o varias franjas horarias (día ISO 1–7, hora de inicio y de fin) y locutores asociados (N:M). Se puede crear, consultar, editar, publicar, desactivar y eliminar con confirmación explícita.
- **S6 — Validación de horarios.** Se rechazan franjas con fin menor o igual al inicio, días fuera de 1–7, franjas duplicadas del mismo programa y solapamientos con franjas de otros programas en el mismo día. La regla se aplica en el caso de uso, no solo en el formulario. Un programa puede emitirse varios días por semana.
- **S7 — Locutores.** Entidad mínima `hosts` (nombre, biografía, foto, activo) con CRUD administrativo y asociación múltiple a programas. El módulo ampliado de locutores queda para una entrega posterior; esta entrega solo cubre lo necesario para asociarlos.
- **S8 — Publicación pública.** La home muestra únicamente programas con estado `publicado`, agrupados por día, con horario y locutores. Sin programas publicados conserva el estado vacío actual y el aviso de que la parrilla no está confirmada. Los datos cargados por el seed de ejemplo se identifican como datos de prueba.
- **S9 — Dashboard.** Muestra nombre de la emisora, estado de la configuración de la transmisión, cantidad de programas publicados, cantidad de locutores activos, próximos programas calculados desde la programación real (día y hora actuales), accesos rápidos y últimas actualizaciones administrativas. No se muestran oyentes, reproducciones ni audiencia: no existe fuente de datos para esas métricas.
- **S10 — Seguridad.** Todas las rutas nuevas viven bajo el prefijo `admin` con middleware `admin.auth`, CSRF activo y validación de servidor. Las subidas aceptan solo `jpg`, `jpeg`, `png`, `webp` con tamaño máximo y nombre aleatorio en el disco `public`. Un listener no puede alcanzar ninguna ruta administrativa. No se guardan secretos en contenido público; la configuración pública solo expone nombre, eslogan, descripción, logotipo y datos de transmisión.
- **S11 — Identidad pública.** El nombre, eslogan, descripción y logotipo configurados se reflejan en la página pública (header, «Sobre la emisora», footer). Sin configuración se conservan exactamente los textos actuales.
- **S12 — Diseño y accesibilidad.** Se reutilizan los tokens de `resources/css/app.css` sin agregar colores. La navegación administrativa marca la sección activa, los formularios tienen `<label>` asociado, errores junto al campo, foco visible, objetivos táctiles de al menos 44 px y composición responsive. La confirmación de borrado funciona sin JavaScript en línea (compatible con la CSP de producción).
- **S13 — Calidad.** Pruebas de cada regla (precedencia de configuración, validación de horarios, separación listener/admin, persistencia real de cada módulo, publicación selectiva, subidas rechazadas), suite completa y `pint --test` en verde, `pnpm run build` correcto y sin archivos temporales dentro del repositorio.
- **S14 — Contenido de la página pública.** Módulo `/admin/contenido` sobre la tabla `site_contents` (fila única) para kicker, título e introducción del hero, título y texto de «Sobre la emisora», título e introducción de la galería, y título e introducción de contacto. Sin fila, o con un campo vacío, la home conserva exactamente los textos actuales: los valores por defecto viven en `App\Domain\Cms\SiteContent`. La escritura se registra en el historial administrativo.
- **S15 — Galería de imágenes.** CRUD `/admin/galeria` sobre `gallery_images` (título, imagen validada `jpg/jpeg/png/webp` de hasta 2 MB, texto alternativo, leyenda, orden, publicada). La home muestra solo las imágenes publicadas, en orden; sin imágenes publicadas conserva las cuatro composiciones gráficas actuales. Eliminar requiere confirmación explícita.
- **S16 — Redes sociales y contacto.** `/admin/contacto` persiste correo, teléfono y enlaces sociales (`social_links`: etiqueta, URL `https`, orden, activo). Precedencia: el valor guardado válido primero; si no hay ninguno guardado se usan `RADIO_CONTACT_EMAIL` y `RADIO_SOCIAL_*`; sin ninguno, el canal se oculta. Correos inválidos y URLs no `https` se rechazan al guardar y también en lectura. La escritura se registra en el historial.
- **S17 — Cuentas administrativas.** CRUD `/admin/cuentas`: crear (nombre, correo único, contraseña de al menos 8 caracteres con confirmación), editar (contraseña opcional) y eliminar con confirmación. Nunca se puede eliminar la propia cuenta ni la última cuenta administrativa. La contraseña se guarda hasheada con `PasswordHasher`; al cambiar la contraseña propia se regenera la sesión. Todas las escrituras se registran en el historial.
- **S18 — Navegación y dashboard completos.** El panel enlaza las ocho áreas y el dashboard ofrece accesos rápidos a todas ellas, además de los nuevos tipos de actividad. No se agregan métricas sin fuente de datos.

## Tasks

| ID | Specs | Route | Estado | Evidencia |
|---|---|---|---|---|
| T1 | S1 | inline | Hecho | Estructura de capas `Domain\Cms`, `Application\Cms`, `Infrastructure\Persistence\Eloquent`. |
| T2 | S2, S3, S4, S9 (parcial), S12 | inline | Hecho | `StationSettingsTest` verde; precedencia base de datos → entorno → no configurada. |
| T3 | S7 | inline | Hecho | CRUD de locutores en `/admin/locutores` con foto y estado. |
| T4 | S5, S6 | inline | Hecho | `ProgramManagementTest` verde: multi-día, solapamientos y estados. |
| T5 | S9 | inline | Hecho | Dashboard con emisora, programación, locutores, próximos programas e historial. |
| T6 | S8, S11 | inline | Hecho | Home con parrilla publicada e identidad configurada. |
| T7 | S13 | inline | Hecho | Suite verde antes de esta entrega; 3 pruebas de home corregidas en T8. |
| T8 | S14 | inline | Hecho | `SiteContentManagementTest` (5): guardado, home pública, valores por defecto y validación. |
| T9 | S15 | inline | Hecho | `GalleryManagementTest` (6): subida, visibilidad pública, edición sin reemplazo, borrado con archivo, rechazo de no-imagen. |
| T10 | S16 | inline | Hecho | `ContactManagementTest` (8): guardado, render público, precedencia sobre entorno, https obligatorio, CRUD de enlaces y respaldo `RADIO_*`. |
| T11 | S17 | inline | Hecho | `AdminAccountManagementTest` (8) + `DeleteAdministratorTest` (3): alta/login, duplicados, edición con y sin contraseña, protecciones de borrado. |
| T12 | S18 | inline | Hecho | Nav de ocho áreas y dashboard con galería, redes y cuentas; `AdminCmsAccessTest` ampliado a todas las secciones. |
| T13 | S13 | inline | Hecho | Suite 212/212, `pint --test` y `pnpm run build` en verde; `docs/architecture.md` actualizado (§10). |


## Log

- **L1 (2026-11-05).** Pedido del usuario (verbatim):

  > De momento esto ya después de mando lo demás Proyecto Grupo Tecno Desarrollo — Desarrollo de CMS y panel administrativo
  >
  > Quiero evolucionar el panel administrativo actual de Grupo Tecno Desarrollo y convertirlo en un CMS (Content Management System) personalizado, que permita administrar el contenido y las configuraciones de la emisora desde una interfaz privada, sin tener que modificar archivos del código cada vez que necesite actualizar la página.
  >
  > El objetivo es que una persona administradora pueda gestionar la mayor parte del sitio web desde /admin.
  >
  > ## 1. Contexto actual del proyecto
  >
  > Antes de realizar cualquier cambio, inspecciona el proyecto y reconoce su arquitectura actual.
  >
  > Datos conocidos:
  >
  > Proyecto Laravel 13.
  > Base de datos SQLite.
  > El archivo de base de datos es database/database.sqlite.
  > Ya existe autenticación administrativa funcional.
  > La tabla administrativa es admin_users.
  > El modelo administrativo es AdminUser.
  > El guard administrativo es admin.
  > El login está en /admin/login.
  > El panel actual está en /admin.
  > La página pública ya tiene una estructura completa.
  > Existe un reproductor de audio funcional.
  > Existe una configuración de transmisión mediante variables RADIO_*.
  > El proyecto utiliza una arquitectura por capas. Debes respetarla y revisar cómo están organizadas las funcionalidades existentes antes de implementar otras.
  > El diseño visual actual utiliza una estética oscura con acentos naranja.
  > No migres a MySQL ni cambies el motor de base de datos.
  > No agregues un sistema de roles y permisos si la arquitectura actual no lo necesita. Por ahora existe un único tipo de administrador.
  >
  > ## 2. Objetivo general
  >
  > Crear un CMS que permita administrar las siguientes áreas:
  >
  > Dashboard.
  > Configuración de la emisora.
  > Programación.
  > Locutores.
  > Contenido de la página pública.
  > Galería de imágenes.
  > Redes sociales y contacto.
  > Administración de cuentas administrativas.
  >
  > Cada módulo debe contar con una interfaz administrativa coherente, operaciones funcionales y persistencia real en la base de datos.
  >
  > No quiero botones decorativos ni formularios que aparenten funcionar sin guardar información.
  >
  > ## 3. Dashboard administrativo
  >
  > Diseña un dashboard inicial que muestre información útil de la emisora.
  >
  > Debe incluir, cuando existan datos reales:
  >
  > Nombre de la emisora.
  > Estado de configuración de la transmisión.
  > Programas registrados.
  > Locutores registrados.
  > Próximos programas según la programación.
  > Accesos rápidos a las principales secciones.
  > Últimas actualizaciones administrativas, si existe una forma apropiada de registrarlas.
  >
  > No inventes estadísticas de oyentes, reproducciones o audiencia. Si todavía no existe una fuente de datos para esas métricas, no las presentes como información real.
  >
  > ## 4. Módulo de configuración de la emisora
  >
  > Crear una sección para administrar:
  >
  > Nombre de la emisora.
  > Descripción.
  > Eslogan.
  > Logotipo.
  > URL del streaming.
  > Formato del streaming.
  > Nombre de la transmisión.
  > Estado de la emisora.
  > Información adicional que realmente necesite la página pública.
  >
  > El reproductor público debe consumir la configuración almacenada en el sistema.
  >
  > IMPORTANTE:
  >
  > No elimines el soporte actual de variables RADIO_* sin analizar primero su funcionamiento.
  > Diseña una solución compatible con la configuración existente.
  > Define claramente la precedencia entre configuración de base de datos y variables de entorno.
  > Las credenciales y secretos nunca deben almacenarse como contenido público.
  > No permitas que una modificación administrativa provoque que el reproductor deje de funcionar silenciosamente.
  > La transmisión externa utilizada actualmente para pruebas debe continuar identificada como emisora de prueba. No debe presentarse como una transmisión propia de Grupo Tecno Desarrollo.
  >
  > ## 5. Módulo de programación
  >
  > Crear un CRUD completo para administrar programas de radio.
  >
  > Cada programa debería poder tener, según el diseño que resulte adecuado:
  >
  > Nombre.
  > Descripción.
  > Imagen o portada.
  > Locutor o locutores asociados.
  > Día de transmisión.
  > Hora de inicio.
  > Hora de finalización.
  > Estado: borrador, publicado o inactivo.
  > Orden de visualización.
  >
  > Funcionalidades:
  >
  > Crear programas.
  > Editarlos.
  > Consultarlos.
  > Publicarlos.
  > Desactivarlos.
  > Eliminarlos con confirmación.
  > Asociar locutores.
  > Mostrar los programas publicados en la página pública.
  >
  > Considera que un programa puede emitirse varios días por semana. No diseñes la base de datos suponiendo que cada programa solo puede tener un día.
  >
  > Valida horarios y datos para evitar registros inconsistentes.
  >
  > No inventes una programación oficial. Los datos de ejemplo deben identificarse como datos de prueba.

- **L2. Alcance de esta entrega.** El pedido continúa en una segunda parte («ya después te mando lo demás»). Esta entrega cubre las secciones 1 a 5 del mensaje: base del CMS, dashboard, configuración de la emisora y programación (con los locutores mínimos que la programación necesita para asociar). Contenido de la página pública, galería, redes/contacto y cuentas administrativas quedan para la siguiente entrega; el esquema y las capas se diseñan para recibirlos sin reescritura.

- **L3. Precedencia resuelta (decisión).** Base de datos primero, entorno como respaldo campo por campo, y validación reproducida en lectura. Motivo: el administrador debe poder cambiar la transmisión sin tocar el servidor, pero un valor guardado inválido nunca debe apagar el reproductor mientras existe una configuración de entorno válida. `RADIO_*` se conserva completo, sin eliminar variables.

- **L4. Solapamientos (decisión).** Se validan contra todos los programas, no solo los publicados. Motivo: si se permitiera el solapamiento entre borradores, publicar después fallaría sin que el administrador entienda por qué; es preferible el rechazo inmediato y determinista. Se prohíbe cruzar la medianoche en una franja; una franja nocturna se registra como dos franjas (fin de día y comienzo del siguiente).

- **L5. Trabajo previo sin confirmar.** El árbol traía cambios sin confirmar de la entrega anterior de seeders de desarrollo (`README.md`, `database/seeders/*`, `config/seeding.php`, `tests/Feature/Security/DevelopmentSeedersTest.php`). No se tocan ni se incluyen en los commits de esta entrega.

- **L6 (2026-11-05). Línea base.** `C:\php84\php.exe artisan test` → 143 pruebas, 581 aserciones, todas OK antes de empezar.

- **L7 (2026-11-07). Ampliación de alcance (verbatim del pedido nuevo).**

  > revisa que esto este hecho y si hace falta algo entonces implementalo porfavor
  > Quiero evolucionar el panel administrativo actual de Grupo Tecno Desarrollo y convertirlo en un CMS (Content Management System) personalizado, que permita administrar el contenido y las configuraciones de la emisora desde una interfaz privada, sin tener que modificar archivos del código cada vez que necesite actualizar la página.
  > El objetivo es que una persona administradora pueda gestionar la mayor parte del sitio web desde /admin.
  > [... el resto de la sección 1 a 5 repite L1 ...]
  > Crear un CMS que permita administrar las siguientes áreas:
  > 1. Dashboard.
  > 2. Configuración de la emisora.
  > 3. Programación.
  > 4. Locutores.
  > 5. Contenido de la página pública.
  > 6. Galería de imágenes.
  > 7. Redes sociales y contacto.
  > 8. Administración de cuentas administrativas.
  > Cada módulo debe contar con una interfaz administrativa coherente, operaciones funcionales y persistencia real en la base de datos.
  > No quiero botones decorativos ni formularios que aparenten funcionar sin guardar información.

- **L8 (2026-11-07). Revisión del estado previo.** La entrega anterior estaba implementada pero sin confirmar. `artisan test` → 175 pruebas, 172 verdes, 3 fallas: `BrandAssetsTest` (2) y `ExampleTest` (1) reciben 500 porque la home ahora consulta `programs` sin migrar la base en esas pruebas. Se corrigieron agregando `RefreshDatabase`. Los cuatro módulos faltantes son contenido público, galería, redes/contacto y cuentas administrativas.

- **L9 (2026-11-07). Decisiones de la ampliación.** (a) Contenido público en fila única `site_contents` con los textos actuales como valores por defecto en el dominio, para no inventar contenido ni romper la home sin configuración. (b) Galería en tabla propia: si no hay imágenes publicadas se conservan las composiciones gráficas actuales. (c) Contacto con precedencia base de datos → `RADIO_*` → canal oculto, reutilizando `ContactChannels` y validando en lectura. (d) Cuentas administrativas sin roles: un solo tipo de administrador, con protección de la última cuenta y de la propia cuenta. (e) La ruta elegida es inline en todos los módulos porque comparten `routes/admin.php`, el layout administrativo, `AppServiceProvider` y el dashboard: los edit surfaces no son disjuntos.
- **L10 (2026-11-07). Cierre de la ampliación.** Se completó el cableado del dashboard (galería, redes, cuentas), se amplió la navegación a las ocho áreas y se agregaron pruebas de los cuatro módulos nuevos y del CRUD de locutores que no tenía cobertura. También se completó `InMemoryAdminAccountRepository` con los métodos nuevos del contrato, que era la causa del error fatal de la suite. Verificación: `artisan test` → 212 pruebas, 980 aserciones, todas OK; `pint --test` OK; `pnpm run build` OK. Documentación: `docs/architecture.md` §6 y §10. `README.md` no se tocó porque su cambio pendiente pertenece a la entrega de seeders (L5). No se agregaron métricas de audiencia.