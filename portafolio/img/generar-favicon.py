#!/usr/bin/env python3
"""
Genera los assets del favicon + la imagen OG de respaldo del tema, sin
depender de Pillow/ImageMagick (no disponibles en este entorno): un
codificador de PNG mínimo (zlib + struct, stdlib) más un renderizador de
círculos con supermuestreo para el antialiasing.

Marca: dos esferas superpuestas (violeta + azul) con un pequeño brillo
lavanda, el mismo lenguaje visual que .hero-panel-eco / las esferas
flotantes de .portada-oscura en style.css, sobre el fondo oscuro del sitio.
Colores tomados literalmente de :root en style.css.

Uso: python3 img/generar-favicon.py (desde la raíz del tema). Solo hace
falta volver a correrlo si cambia la marca o la paleta de colores; los PNG
e ICO generados ya quedan versionados en el repo.
"""
import os
import struct
import zlib

FONDO = (3, 7, 17)  # --color-fondo-oscuro
FONDO_OG_A = (6, 13, 26)  # --color-vidrio (esquina sup. izq. del degradado)
FONDO_OG_B = (37, 37, 65)  # --color-vidrio-trasero (esquina inf. der.)

CIRCULOS_MARCA = [
    # (cx, cy, r, color, alpha) en coordenadas normalizadas 0-1
    (0.40, 0.62, 0.30, (37, 99, 235), 0.90),  # --color-primario (azul)
    (0.60, 0.42, 0.34, (163, 93, 228), 0.92),  # --color-acento-violeta
    (0.53, 0.32, 0.11, (151, 152, 249), 0.55),  # --color-acento-lavanda, brillo
]

OUT_DIR = os.path.dirname(os.path.abspath(__file__))


def write_png(path, width, height, get_pixel):
    """get_pixel(x, y) -> (r, g, b) por píxel final (ya resuelto)."""
    rows = []
    for y in range(height):
        row = bytearray(width * 3)
        for x in range(width):
            r, g, b = get_pixel(x, y)
            row[x * 3] = r
            row[x * 3 + 1] = g
            row[x * 3 + 2] = b
        rows.append(bytes(row))

    def chunk(tag, data):
        return struct.pack(">I", len(data)) + tag + data + struct.pack(
            ">I", zlib.crc32(tag + data) & 0xFFFFFFFF
        )

    sig = b"\x89PNG\r\n\x1a\n"
    ihdr = struct.pack(">IIBBBBB", width, height, 8, 2, 0, 0, 0)
    raw = b"".join(b"\x00" + row for row in rows)
    idat = zlib.compress(raw, 9)

    with open(path, "wb") as f:
        f.write(sig)
        f.write(chunk(b"IHDR", ihdr))
        f.write(chunk(b"IDAT", idat))
        f.write(chunk(b"IEND", b""))


def lerp(a, b, t):
    return a + (b - a) * t


def mezclar(base, color, alpha):
    return (
        round(lerp(base[0], color[0], alpha)),
        round(lerp(base[1], color[1], alpha)),
        round(lerp(base[2], color[2], alpha)),
    )


def render_icono(size, ss=4):
    """Cuadrado `size`x`size` con la marca centrada, fondo sólido oscuro."""
    big = size * ss
    lado = min(big, big)
    buf = [[FONDO for _ in range(big)] for _ in range(big)]

    for (ncx, ncy, nr, color, alpha) in CIRCULOS_MARCA:
        cx, cy, r = ncx * lado, ncy * lado, nr * lado
        x0, x1 = max(0, int(cx - r - 2)), min(big, int(cx + r + 2))
        y0, y1 = max(0, int(cy - r - 2)), min(big, int(cy + r + 2))
        r2 = r * r
        for y in range(y0, y1):
            dy = y + 0.5 - cy
            fila = buf[y]
            for x in range(x0, x1):
                dx = x + 0.5 - cx
                if dx * dx + dy * dy <= r2:
                    fila[x] = mezclar(fila[x], color, alpha)

    # Downsample (promedio de bloques ss x ss) para antialiasing.
    def get_pixel(x, y):
        tr = tg = tb = 0
        for j in range(ss):
            fila = buf[y * ss + j]
            for i in range(ss):
                p = fila[x * ss + i]
                tr += p[0]
                tg += p[1]
                tb += p[2]
        n = ss * ss
        return (tr // n, tg // n, tb // n)

    return get_pixel


def render_og(width, height, ss=2):
    """Rectángulo `width`x`height` con la marca centrada, fondo en degradado."""
    big_w, big_h = width * ss, height * ss
    lado = min(big_w, big_h)
    off_x = (big_w - lado) / 2  # centra el cuadrado de la marca en el ancho

    buf = [[None] * big_w for _ in range(big_h)]
    for y in range(big_h):
        ty = y / (big_h - 1)
        for x in range(big_w):
            tx = x / (big_w - 1)
            t = (tx + ty) / 2
            buf[y][x] = (
                round(lerp(FONDO_OG_A[0], FONDO_OG_B[0], t)),
                round(lerp(FONDO_OG_A[1], FONDO_OG_B[1], t)),
                round(lerp(FONDO_OG_A[2], FONDO_OG_B[2], t)),
            )

    for (ncx, ncy, nr, color, alpha) in CIRCULOS_MARCA:
        cx, cy, r = off_x + ncx * lado, ncy * lado, nr * lado
        x0, x1 = max(0, int(cx - r - 2)), min(big_w, int(cx + r + 2))
        y0, y1 = max(0, int(cy - r - 2)), min(big_h, int(cy + r + 2))
        r2 = r * r
        for y in range(y0, y1):
            dy = y + 0.5 - cy
            fila = buf[y]
            for x in range(x0, x1):
                dx = x + 0.5 - cx
                if dx * dx + dy * dy <= r2:
                    fila[x] = mezclar(fila[x], color, alpha)

    def get_pixel(x, y):
        tr = tg = tb = 0
        for j in range(ss):
            fila = buf[y * ss + j]
            for i in range(ss):
                p = fila[x * ss + i]
                tr += p[0]
                tg += p[1]
                tb += p[2]
        n = ss * ss
        return (tr // n, tg // n, tb // n)

    return get_pixel


def build_ico(path, png_paths):
    entradas = []
    datos = []
    offset = 6 + 16 * len(png_paths)
    for p in png_paths:
        with open(p, "rb") as f:
            data = f.read()
        # Tamaño real leído del IHDR (bytes 16-19 = width, 20-23 = height).
        w = struct.unpack(">I", data[16:20])[0]
        h = struct.unpack(">I", data[20:24])[0]
        w_b = 0 if w >= 256 else w
        h_b = 0 if h >= 256 else h
        entradas.append(
            struct.pack("<BBBBHHII", w_b, h_b, 0, 0, 1, 32, len(data), offset)
        )
        datos.append(data)
        offset += len(data)

    with open(path, "wb") as f:
        f.write(struct.pack("<HHH", 0, 1, len(png_paths)))
        for e in entradas:
            f.write(e)
        for d in datos:
            f.write(d)


def main():
    os.makedirs(OUT_DIR, exist_ok=True)

    tamanos = {
        "favicon-16x16.png": 16,
        "favicon-32x32.png": 32,
        "favicon-48x48.png": 48,
        "apple-touch-icon.png": 180,
        "icon-192.png": 192,
        "icon-512.png": 512,
    }

    for nombre, tam in tamanos.items():
        ruta = os.path.join(OUT_DIR, nombre)
        write_png(ruta, tam, tam, render_icono(tam))
        print("OK", ruta)

    ruta_og = os.path.join(OUT_DIR, "og-default.png")
    write_png(ruta_og, 1200, 630, render_og(1200, 630))
    print("OK", ruta_og)

    build_ico(
        os.path.join(OUT_DIR, "favicon.ico"),
        [
            os.path.join(OUT_DIR, "favicon-16x16.png"),
            os.path.join(OUT_DIR, "favicon-32x32.png"),
            os.path.join(OUT_DIR, "favicon-48x48.png"),
        ],
    )
    print("OK", os.path.join(OUT_DIR, "favicon.ico"))


if __name__ == "__main__":
    main()
