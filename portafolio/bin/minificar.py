#!/usr/bin/env python3
"""
Minificado manual de CSS/JS del tema, sin build tools (no hay Node en este
entorno). Genera *.min.css / *.min.js a partir de los fuentes; los fuentes
quedan intactos para seguir editando ahí.

CSS: minificado estándar (agresivo) — quita comentarios, colapsa espacios y
saltos de línea, protegiendo antes las cadenas literales (content: "...").

JS: minificado conservador — solo quita comentarios (de bloque y de línea,
verificado a mano que ningún string/regex del código contiene "//") y
espacios en blanco sobrantes (indentación, líneas vacías), pero SIN fusionar
líneas de código entre sí: evita cualquier riesgo de ASI (inserción
automática de punto y coma) o de romper un literal de expresión regular por
una heurística equivocada de dónde empieza/termina.

Uso: python3 bin/minificar.py (desde la raíz del tema), cada vez que se
edite style.css o algún js/*.js. Los *.min.* generados quedan versionados
en el repo (functions.php encola esos, no los fuentes) — si cambia el
minificador, hay que volver a correrlo y commitear la salida nueva.
"""
import re
import os

RAIZ = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))


def minificar_css(src):
    # 1) Comentarios /* ... */ fuera de cadenas: el CSS de este tema no
    #    tiene cadenas con "/*" dentro, así que un strip directo es seguro.
    sin_comentarios = re.sub(r"/\*.*?\*/", "", src, flags=re.S)

    # 2) Proteger cadenas literales (content: "..." o '...') antes de tocar
    #    espacios, para no colapsar un espacio que sea parte del contenido.
    cadenas = []

    def _proteger(m):
        cadenas.append(m.group(0))
        return f"\0{len(cadenas) - 1}\0"

    protegido = re.sub(r'"[^"]*"|\'[^\']*\'', _proteger, sin_comentarios)

    # 3) Colapsar espacios en blanco (incluye saltos de línea) a uno solo.
    compacto = re.sub(r"\s+", " ", protegido).strip()

    # 4) Quitar espacios alrededor de los delimitadores donde no hacen falta.
    compacto = re.sub(r"\s*([{}:;,>])\s*", r"\1", compacto)
    # Excepción: un espacio SÍ es significativo en selectores descendientes
    # ("a b") y en valores con varios términos (p. ej. "1px solid red") — el
    # regex anterior no toca esos espacios porque no están pegados a los
    # delimitadores {, }, :, ;, ,, >.

    # 5) Punto y coma sobrante justo antes de "}".
    compacto = re.sub(r";}", "}", compacto)

    # 6) Restaurar las cadenas protegidas.
    def _restaurar(m):
        return cadenas[int(m.group(1))]

    compacto = re.sub(r"\0(\d+)\0", _restaurar, compacto)

    return compacto.strip() + "\n"


def minificar_js(src):
    lineas_salida = []
    en_comentario_bloque = False

    for linea in src.split("\n"):
        if en_comentario_bloque:
            fin = linea.find("*/")
            if fin == -1:
                continue  # sigue dentro del comentario de bloque
            linea = linea[fin + 2:]
            en_comentario_bloque = False

        # Puede haber un comentario de bloque de una sola línea (/* ... */)
        # o el inicio de uno multilínea; se procesa en un bucle por si hay
        # más de uno en la misma línea.
        while True:
            inicio = linea.find("/*")
            if inicio == -1:
                break
            fin = linea.find("*/", inicio + 2)
            if fin == -1:
                linea = linea[:inicio]
                en_comentario_bloque = True
                break
            linea = linea[:inicio] + linea[fin + 2:]

        # Comentario de línea completa o al final de una línea de código.
        # Verificado a mano (ver comentario del módulo): ningún string ni
        # regex literal de este código contiene "//".
        idx = linea.find("//")
        if idx != -1:
            linea = linea[:idx]

        linea = linea.rstrip()
        if linea.strip() == "":
            continue

        lineas_salida.append(linea.strip())

    return "\n".join(lineas_salida) + "\n"


def escribir_si_cambia(ruta, contenido):
    if os.path.exists(ruta) and open(ruta, encoding="utf-8").read() == contenido:
        return False
    with open(ruta, "w", encoding="utf-8") as f:
        f.write(contenido)
    return True


def main():
    origen_css = os.path.join(RAIZ, "style.css")
    destino_css = os.path.join(RAIZ, "style.min.css")
    src = open(origen_css, encoding="utf-8").read()
    min_css = minificar_css(src)
    escribir_si_cambia(destino_css, min_css)
    print(f"{origen_css}: {len(src)} B -> {destino_css}: {len(min_css)} B "
          f"({100 - len(min_css) * 100 // len(src)}% menos)")

    for nombre in sorted(os.listdir(os.path.join(RAIZ, "js"))):
        if not nombre.endswith(".js") or nombre.endswith(".min.js"):
            continue
        origen = os.path.join(RAIZ, "js", nombre)
        destino = os.path.join(RAIZ, "js", nombre.replace(".js", ".min.js"))
        src = open(origen, encoding="utf-8").read()
        min_js = minificar_js(src)
        escribir_si_cambia(destino, min_js)
        print(f"{origen}: {len(src)} B -> {destino}: {len(min_js)} B "
              f"({100 - len(min_js) * 100 // len(src)}% menos)")


if __name__ == "__main__":
    main()
