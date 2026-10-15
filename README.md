# Portafolio: tema de WordPress desde cero

[![Hablemos](https://img.shields.io/badge/Hablemos-alexrope.dev-2563eb?style=for-the-badge)](https://alexrope.dev/?utm_source=github&utm_medium=readme&utm_campaign=portfolio-wp-2026#contacto)

Tema de WordPress construido **sin page builders** (ni Elementor ni Divi, ni sus bloques) para mi portafolio profesional.

**En producción:** [alexrope.dev](https://alexrope.dev)

## Qué incluye

- **Plantillas propias** según la jerarquía de plantillas de WordPress:
  - `front-page.php`
  - `archive-proyectos.php`
  - `single-proyectos.php`
  - `taxonomy-categoria_proyecto.php`
  - `template-parts/`
- **Contenido editable desde el panel**, sin tocar código:
  - tipo de contenido propio *Proyectos* con su taxonomía *Categorías de proyecto*
  - campos de Advanced Custom Fields (versión gratuita) definidos en código
- **SEO técnico sin plugins** (`inc/seo.php`):
  - título y meta descripción dinámicos
  - Open Graph y Twitter Card
  - favicon en varios tamaños y web manifest
  - datos estructurados JSON-LD: `Person` y `WebSite` en la portada, `CreativeWork` en cada proyecto
- **Rendimiento** (`inc/rendimiento.php`, `bin/minificar.py`):
  - CSS y JS minificados sin herramientas de build
  - versión de los archivos según su fecha de modificación (`filemtime`), para que el navegador no use copias viejas
  - sin el script de emojis de WordPress
- **Privacidad y seguridad**:
  - el nombre de usuario de WordPress no se expone por API REST, archivos de autor, sitemaps ni oEmbed (`inc/privacidad.php`)
  - contraseñas de aplicación desactivadas (`inc/sitio.php`)
- **Sitemap limpio**: el blog nativo no se usa, así que sus categorías, etiquetas y archivos por fecha redirigen a la portada y no aparecen en el sitemap (`inc/sitio.php`).
- **HTML semántico y accesible.** Comentarios de código en español.

## Estructura

```
docker-compose.yml   Entorno local (WordPress + MySQL)
.env.example         Variables del entorno local (copiar a .env)
portafolio/          El tema (se monta en wp-content/themes/portafolio)
  inc/               SEO, rendimiento, privacidad y ajustes del sitio
  template-parts/    Componentes reutilizables
  js/  img/  bin/    Scripts, imágenes y el minificador
```

## Entorno local

Requisitos: Docker Engine y Docker Compose (desarrollado en WSL2 con Ubuntu 24.04).

```bash
cp .env.example .env      # y completar las credenciales
docker compose up -d
# abrir http://localhost:8080
```

Después de editar `style.css` o algún `js/*.js`, regenerar los minificados:

```bash
cd portafolio && python3 bin/minificar.py
```

## Producción

El mismo tema corre en una VM Oracle Cloud Always Free (Ubuntu 24.04, ARM).

**Stack:** Docker Compose con WordPress, MySQL, n8n y Uptime Kuma, detrás de Nginx como reverse proxy con caché y HTTPS de Let's Encrypt.

**Seguridad y operación:**
- UFW, Fail2Ban, SSH solo por llave
- respaldos diarios a Oracle Object Storage
- monitoreo con alertas en Telegram ([repositorio del monitoreo](https://github.com/alexcitos/monitoreo-n8n-kuma-telegram))

## ¿Necesitas algo así para tu negocio?

Desarrollo sitios WordPress a la medida, sin constructores visuales, con su infraestructura, monitoreo y automatizaciones con n8n.

👉 **[Escríbeme en alexrope.dev](https://alexrope.dev/?utm_source=github&utm_medium=readme&utm_campaign=portfolio-wp-2026#contacto)** · [LinkedIn](https://www.linkedin.com/in/alexrodriguezp) · [Más proyectos](https://github.com/alexcitos)

## Licencia

GPL v2 o posterior, igual que WordPress.
