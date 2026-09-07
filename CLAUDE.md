# CLAUDE.md

## Contexto del proyecto
Tema de WordPress construido 100% desde cero (sin Elementor, Divi ni ningún
constructor visual) como pieza de portafolio para demostrar dominio de
maquetación nativa en WordPress.

## Reglas de código
- No usar page builders ni sus shortcodes/bloques.
- Seguir la Template Hierarchy estándar de WordPress.
- Encolar estilos y scripts con wp_enqueue_style / wp_enqueue_script en
  functions.php — nunca <script> o <link> a mano en las plantillas.
- Usar Advanced Custom Fields (versión gratuita) para campos personalizados.
- HTML semántico y accesible.
- Comentarios de código en español.

## Stack y entorno local
- WordPress + MySQL local vía Docker Compose (docker-compose.yml en la raíz).
- El código del tema vive en una carpeta del repo, montada como volumen en
  wp-content/themes/ dentro del contenedor — los cambios se ven en vivo.
- Docker Engine nativo en WSL2 Ubuntu 24.04 (no Docker Desktop).
- Todo pensado para reproducirse igual en un VPS/VM en la nube más adelante.
