<?php
// =======================================================
// HELPERS DE PLANTILLA (idioma, JSON, URLs, imágenes)
// -------------------------------------------------------
// Requiere config/bootstrap.php cargado antes (ROOT_PATH,
// BASE_URL). Todas las funciones están protegidas con
// function_exists para poder incluir este archivo varias
// veces sin errores.
// =======================================================

if (!function_exists('ozz_lang')) {
    /**
     * Idioma actual con el mismo criterio que el menú:
     * archivos *-es.php => español, el resto => inglés.
     */
    function ozz_lang(): string
    {
        $file = basename($_SERVER['SCRIPT_NAME'] ?? ($_SERVER['PHP_SELF'] ?? ''));
        return str_contains($file, '-es.php') ? 'es' : 'en';
    }
}

if (!function_exists('ozz_other_lang')) {
    /** El idioma contrario al actual. */
    function ozz_other_lang(): string
    {
        return ozz_lang() === 'es' ? 'en' : 'es';
    }
}

if (!function_exists('ozz_page_url')) {
    /**
     * URL de una página del sitio en un idioma: ozz_page_url('Work', 'es')
     * => BASE_URL/Work-es.php. Admite subcarpetas: 'Work/Mango'.
     */
    function ozz_page_url(string $slug, ?string $lang = null): string
    {
        $lang = $lang ?? ozz_lang();
        $slug = preg_replace('/(-es)?\.php$/', '', trim($slug, '/'));

        return rtrim(BASE_URL, '/') . '/' . $slug . ($lang === 'es' ? '-es' : '') . '.php';
    }
}

if (!function_exists('ozz_current_page_url')) {
    /** URL de la página actual en el idioma indicado (conserva la query). */
    function ozz_current_page_url(string $lang): string
    {
        $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        $base_path = (string) parse_url(BASE_URL, PHP_URL_PATH);
        $rel = ltrim(substr($script, strlen($base_path)), '/');
        $query = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';

        return ozz_page_url($rel ?: 'index.php', $lang) . $query;
    }
}

if (!function_exists('ozz_e')) {
    /** Escapa texto para HTML/atributos. */
    function ozz_e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('ozz_json')) {
    /**
     * Lee un JSON relativo a ROOT_PATH (con caché por petición).
     * Devuelve [] si no existe o es inválido, para que la página
     * nunca se rompa por un error de contenido.
     */
    function ozz_json(string $relativePath): array
    {
        static $cache = [];

        if (isset($cache[$relativePath])) {
            return $cache[$relativePath];
        }

        $file = rtrim(ROOT_PATH, '/') . '/' . ltrim($relativePath, '/');
        $data = [];

        if (is_file($file)) {
            $decoded = json_decode((string) file_get_contents($file), true);

            if (is_array($decoded)) {
                $data = $decoded;
            } else {
                error_log('[ozz_json] JSON inválido: ' . $file . ' — ' . json_last_error_msg());
            }
        } else {
            error_log('[ozz_json] No existe: ' . $file);
        }

        return $cache[$relativePath] = $data;
    }
}

if (!function_exists('ozz_t')) {
    /**
     * Traduce un valor del JSON:
     *  - {"es": "...", "en": "..."} => el idioma pedido (fallback en)
     *  - "texto"                    => tal cual
     */
    function ozz_t($value, string $lang): string
    {
        $text = is_array($value)
            ? (string) ($value[$lang] ?? $value['en'] ?? reset($value) ?? '')
            : (string) ($value ?? '');

        // {BASE_URL} dentro de los textos/HTML del JSON => URL del sitio
        return str_contains($text, '{BASE_URL}')
            ? str_replace('{BASE_URL}', rtrim(BASE_URL, '/'), $text)
            : $text;
    }
}

// -------------------------------------------------------
// CAMBIO DE IDIOMA SIN RECARGA
// -------------------------------------------------------
// El servidor imprime el texto del idioma actual y guarda
// la traducción en atributos data-alt / data-alt-{atributo}.
// Scripts/LangSwitch.js los intercambia al pulsar ES/EN.
//
// Uso en plantillas:
//   ozz_tx($valor)          -> texto escapado del idioma actual
//   ozz_alt($valor)         -> atributo data-alt con la traducción
//   ozz_alt($valor, 'alt')  -> atributo data-alt-alt (para atributos)
//   ozz_href($href)         -> href + data-alt-href
// -------------------------------------------------------

if (!function_exists('ozz_tx')) {
    /** Texto traducido y escapado en el idioma actual. */
    function ozz_tx($value): string
    {
        return ozz_e(ozz_t($value, ozz_lang()));
    }
}

if (!function_exists('ozz_alt')) {
    /**
     * Atributo con la traducción alternativa. Si ambos idiomas son
     * iguales (p. ej. "Branding") no imprime nada.
     */
    function ozz_alt($value, string $attr = ''): string
    {
        if (!is_array($value)) {
            return '';
        }

        $current = ozz_t($value, ozz_lang());
        $other = ozz_t($value, ozz_other_lang());

        if ($other === $current) {
            return '';
        }

        $name = $attr === '' ? 'data-alt' : 'data-alt-' . $attr;

        return ' ' . $name . '="' . ozz_e($other) . '"';
    }
}

if (!function_exists('ozz_th')) {
    /**
     * HTML del idioma actual SIN escapar. Solo para contenido propio de
     * los JSON del sitio (p. ej. "<em>texto</em>", "<strong>", "<br>").
     */
    function ozz_th($value): string
    {
        return ozz_t($value, ozz_lang());
    }
}

if (!function_exists('ozz_alt_html')) {
    /** data-alt-html con el HTML del otro idioma (LangSwitch.js usa innerHTML). */
    function ozz_alt_html($value): string
    {
        if (!is_array($value)) {
            return '';
        }

        $current = ozz_t($value, ozz_lang());
        $other = ozz_t($value, ozz_other_lang());

        return $other === $current ? '' : ' data-alt-html="' . ozz_e($other) . '"';
    }
}

if (!function_exists('ozz_flame_svg')) {
    /** Ícono de la llama de Ozz (mismo trazo que el hero y el footer). */
    function ozz_flame_svg(string $class = ''): string
    {
        $d = 'M 366.419 6.863 C 363.714 7.782, 357.450 11.170, 352.500 14.391 C 308.220 43.207, 278.912 91.742, 256.421 173.500 C 254 182.300, 251.276 191.177, 250.368 193.228 C 245.865 203.393, 238.549 196.129, 234.024 177 C 231.089 164.596, 229.278 159, 226.739 154.500 C 224.142 149.897, 218.842 146.864, 214.806 147.672 C 205.669 149.502, 195.692 167.215, 191.575 188.916 C 190.779 193.116, 189.166 205.766, 187.991 217.026 C 183.257 262.418, 175.259 298.429, 169.414 300.672 C 166.593 301.754, 164.714 299.714, 159.500 289.906 C 152.888 277.467, 151.430 275.429, 147.336 272.899 C 144.572 271.190, 142.784 270.853, 138.408 271.216 C 133.859 271.593, 132.341 272.246, 129.409 275.085 C 124.592 279.750, 120.404 288.833, 118.407 298.950 C 116.408 309.068, 116.924 336.164, 119.379 350 C 132.655 424.850, 144.086 490.125, 145.004 496.338 C 146.558 506.844, 145.573 513.580, 141.956 517.198 C 139.357 519.797, 138.578 520, 131.212 520 C 121.788 520, 118.676 521.775, 116.966 528.125 C 114.352 537.835, 123.363 573.825, 134.964 600 C 151.352 636.977, 181.884 677.864, 216.050 708.586 C 274.372 761.029, 315.805 777.140, 398.778 779.635 C 479.766 782.071, 546.508 763.495, 590.341 726.317 C 618.802 702.177, 639.188 665.988, 647.415 625 C 651.485 604.721, 652.393 594.782, 652.307 571.500 C 652.172 535.484, 648.866 515.955, 632.500 454.500 C 618.729 402.794, 615.341 381.942, 616.288 354.738 C 616.679 343.490, 617.391 337.405, 619.005 331.500 C 621.739 321.497, 621.392 314.110, 618.020 310.521 C 609.955 301.937, 595.415 307.065, 582.929 322.898 C 571.965 336.802, 570.896 338, 569.467 338 C 566.067 338, 563.820 330.808, 556.992 298.068 C 552.491 276.491, 545.947 237.456, 542.980 214.500 C 540.668 196.606, 539.843 193.282, 536.937 190.163 C 533.523 186.499, 529.608 187.311, 523.690 192.912 C 517.442 198.825, 511.948 207.772, 504.516 224.142 C 496.941 240.828, 492.458 248.596, 487.100 254.324 C 479.388 262.569, 474.483 263.768, 471.563 258.122 C 467.444 250.157, 463.030 183.679, 463.010 129.315 C 463 100.208, 462.672 97.427, 458.759 93.250 C 456.075 90.385, 450.177 90.283, 444.194 92.998 C 438.463 95.599, 429.621 104.662, 423.215 114.500 C 415.680 126.074, 400.252 157.808, 392.588 177.500 C 383.916 199.782, 382.070 203.419, 379.226 203.823 C 374.328 204.518, 371.621 192.540, 369.999 163 C 367.662 120.429, 369.660 77.915, 376.054 34.195 C 378.544 17.170, 378.599 9.824, 376.250 7.807 C 373.322 5.292, 371.547 5.122, 366.419 6.863';

        return '<svg' . ($class !== '' ? ' class="' . ozz_e($class) . '"' : '')
            . ' viewBox="0 0 787 787" aria-hidden="true" focusable="false"><path d="' . $d . '"/></svg>';
    }
}

if (!function_exists('ozz_href')) {
    /**
     * href + data-alt-href. Acepta:
     *  - {"es": "Work-es.php", "en": "Work.php"} (rutas del proyecto)
     *  - "Work" (slug: se resuelve a Work.php / Work-es.php)
     */
    function ozz_href($value): string
    {
        if (is_array($value)) {
            $current = ozz_url(ozz_t($value, ozz_lang()));
            $other = ozz_url(ozz_t($value, ozz_other_lang()));
        } else {
            $current = ozz_page_url((string) $value, ozz_lang());
            $other = ozz_page_url((string) $value, ozz_other_lang());
        }

        $html = 'href="' . ozz_e($current) . '"';

        if ($other !== $current) {
            $html .= ' data-alt-href="' . ozz_e($other) . '"';
        }

        return $html;
    }
}

if (!function_exists('ozz_list')) {
    /** Lista traducida: {"es": [...], "en": [...]} => array del idioma pedido. */
    function ozz_list($value, ?string $lang = null): array
    {
        $lang = $lang ?? ozz_lang();
        if (!is_array($value)) {
            return [];
        }

        $list = $value[$lang] ?? $value['en'] ?? [];

        return is_array($list) ? array_values($list) : [];
    }
}

if (!function_exists('ozz_list_pairs')) {
    /** Combina una lista {es:[], en:[]} en pares [{es, en}, ...] para usar con ozz_alt(). */
    function ozz_list_pairs($value): array
    {
        $es = ozz_list($value, 'es');
        $en = ozz_list($value, 'en');
        $out = [];

        foreach ($es as $i => $text) {
            $out[] = ['es' => $text, 'en' => $en[$i] ?? $text];
        }

        return $out;
    }
}

if (!function_exists('ozz_url')) {
    /**
     * Convierte una ruta del proyecto en URL absoluta y codifica
     * cada segmento (espacios => %20) para que funcione igual en
     * Windows (XAMPP) y en el servidor Linux de producción.
     */
    function ozz_url(string $path): string
    {
        if ($path === '' || preg_match('#^(https?:)?//|^(data|blob|mailto|tel):#i', $path)) {
            return $path;
        }

        // Separa query/hash para no codificarlos
        $suffix = '';
        if (preg_match('/^([^?#]*)([?#].*)$/', $path, $m)) {
            $path = $m[1];
            $suffix = $m[2];
        }

        $segments = array_map(
            static fn($s) => rawurlencode(rawurldecode($s)),
            explode('/', ltrim($path, '/'))
        );

        return rtrim(BASE_URL, '/') . '/' . implode('/', $segments) . $suffix;
    }
}

if (!function_exists('ozz_asset')) {
    /**
     * URL de un CSS/JS con versión automática (?v=fecha de modificación):
     * al editar el archivo cambia la URL y el navegador no usa la copia
     * vieja en caché.
     */
    function ozz_asset(string $path): string
    {
        $file = rtrim(ROOT_PATH, '/') . '/' . ltrim($path, '/');
        $version = is_file($file) ? (string) filemtime($file) : '';

        return ozz_url($path) . ($version !== '' ? '?v=' . $version : '');
    }
}

if (!function_exists('ozz_sources')) {
    /**
     * Normaliza una lista de fuentes [{src, w, h}] ordenada de menor a mayor ancho.
     */
    function ozz_sources(array $sources): array
    {
        $out = [];

        foreach ($sources as $s) {
            if (empty($s['src'])) {
                continue;
            }

            $w = (int) ($s['w'] ?? 0);
            $h = (int) ($s['h'] ?? 0);

            $out[] = [
                'src' => ozz_url($s['src']),
                'w'   => $w ?: 1920,
                'h'   => $h ?: (int) round(($w ?: 1920) * 9 / 16),
            ];
        }

        usort($out, static fn($a, $b) => $a['w'] <=> $b['w']);

        return $out;
    }
}

if (!function_exists('ozz_img')) {
    /**
     * Genera un <img> responsive nativo (srcset + sizes): el navegador
     * elige la imagen según el ancho de pantalla y la densidad de píxeles
     * SIN JavaScript, y solo descarga una.
     *
     * $image = ["sources" => [{src, w, h}, ...], "alt" => {es, en}]
     */
    function ozz_img(array $image, string $lang, array $attrs = []): string
    {
        $sources = ozz_sources($image['sources'] ?? []);

        if (!$sources) {
            return '';
        }

        $smallest = $sources[0];
        $srcset = implode(', ', array_map(
            static fn($s) => $s['src'] . ' ' . $s['w'] . 'w',
            $sources
        ));

        $attrs = array_merge([
            'src'      => $smallest['src'],
            'srcset'   => $srcset,
            'sizes'    => '100vw',
            'width'    => $smallest['w'],
            'height'   => $smallest['h'],
            'alt'      => ozz_t($image['alt'] ?? '', $lang),
            'loading'  => 'lazy',
            'decoding' => 'async',
        ], $attrs);

        $html = '<img';
        foreach ($attrs as $name => $value) {
            if ($value === null || $value === false) {
                continue;
            }
            $html .= ' ' . $name . '="' . ozz_e($value) . '"';
        }

        return $html . '>';
    }
}
