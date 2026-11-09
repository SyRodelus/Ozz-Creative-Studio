<?php
// =========================================================
// i18n-merge.php — une una pareja de fragmentos HTML
// (versión EN + versión ES) en UNA plantilla PHP bilingüe
// + un JSON con los textos {es, en}.
// ---------------------------------------------------------
// Uso (solo por consola):
//   php tools/i18n-merge.php <es.html> <en.html> <salida.php> <salida.json> <prefijo> [--force]
//
// Cómo funciona: recorre los dos árboles HTML en paralelo.
//  - Elementos con solo texto distinto  → data-alt (texto)
//  - Texto con formato (<strong>, <br>…) → data-alt-html
//  - Atributos traducibles distintos     → data-alt-{atributo}
// La plantilla generada imprime el idioma actual y deja la
// traducción en esos atributos para Scripts/LangSwitch.js.
// =========================================================

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$args = array_values(array_filter($argv, fn($a) => $a !== '--force'));
$force = in_array('--force', $argv, true);

if (count($args) < 6) {
    fwrite(STDERR, "Uso: php tools/i18n-merge.php <es.html> <en.html> <salida.php> <salida.json> <prefijo> [--force]\n");
    exit(1);
}

[, $esFile, $enFile, $outTpl, $outJson, $prefix] = $args;

foreach ([$esFile, $enFile] as $f) {
    if (!is_file($f)) {
        fwrite(STDERR, "No existe: $f\n");
        exit(1);
    }
}

if (!$force && (file_exists($outTpl) || file_exists($outJson))) {
    fwrite(STDERR, "La salida ya existe (usa --force para sobrescribir): $outTpl / $outJson\n");
    exit(1);
}

const TRANSLATABLE = [
    'alt', 'title', 'aria-label', 'placeholder', 'content', 'value', 'label',
    'href', 'data-text', 'data-tooltip', 'data-tooltip-title', 'data-tooltip-meta',
    'data-title', 'data-description',
];

$keys = [];      // key => ['es' => ..., 'en' => ...]
$report = [];    // avisos
$counter = 0;

// ---------------------------------------------------------
// Bloques PHP: se protegen con un token por contenido
// ---------------------------------------------------------
$php = [];
$protect = function (string $html) use (&$php): string {
    return preg_replace_callback('/<\?(?:php|=).*?\?>/s', function ($m) use (&$php) {
        $token = '__PHP' . substr(md5($m[0]), 0, 10) . '__';
        $php[$token] = $m[0];
        return $token;
    }, $html);
};

$load = function (string $html): DOMDocument {
    $doc = new DOMDocument('1.0', 'UTF-8');
    libxml_use_internal_errors(true);
    $doc->loadHTML(
        '<?xml encoding="UTF-8"?><html><head><meta charset="utf-8"></head><body><div id="__root__">' . $html . '</div></body></html>',
        LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();
    $doc->encoding = 'UTF-8';
    return $doc;
};

$esDoc = $load($protect(file_get_contents($esFile)));
$enDoc = $load($protect(file_get_contents($enFile)));

$esRoot = $esDoc->getElementById('__root__');
$enRoot = $enDoc->getElementById('__root__');

if (!$esRoot || !$enRoot) {
    fwrite(STDERR, "No se pudo analizar el HTML.\n");
    exit(1);
}

// ---------------------------------------------------------
// Utilidades
// ---------------------------------------------------------
$norm = fn(string $s): string => trim(preg_replace('/\s+/u', ' ', $s));

$inner = function (DOMNode $el) use ($norm): string {
    $html = '';
    foreach ($el->childNodes as $child) {
        $html .= $el->ownerDocument->saveHTML($child);
    }
    return $norm($html);
};

$sigChildren = function (DOMNode $el): array {
    $out = [];
    foreach ($el->childNodes as $c) {
        if ($c instanceof DOMElement) {
            $out[] = $c;
        } elseif ($c instanceof DOMText && trim($c->textContent) !== '') {
            $out[] = $c;
        }
    }
    return $out;
};

$path = function (DOMNode $el): string {
    $parts = [];
    while ($el && $el instanceof DOMElement && $el->getAttribute('id') !== '__root__') {
        $cls = trim((string) $el->getAttribute('class'));
        $parts[] = $el->nodeName . ($cls !== '' ? '.' . explode(' ', $cls)[0] : '');
        $el = $el->parentNode;
    }
    return implode(' > ', array_reverse($parts));
};

$newKey = function (DOMElement $el, string $suffix = '') use (&$counter, $prefix): string {
    $counter++;
    $hint = trim((string) $el->getAttribute('class'));
    $hint = $hint !== '' ? explode(' ', $hint)[0] : $el->nodeName;
    $hint = preg_replace('/[^a-z0-9_-]+/i', '', $hint);
    return $prefix . '.' . str_pad((string) $counter, 2, '0', STR_PAD_LEFT) . '_' . $hint . ($suffix ? '_' . $suffix : '');
};

// Texto del JSON: los tokens PHP de la URL base se guardan como {BASE_URL}
$jsonText = function (string $s) use (&$php, &$report): string {
    return preg_replace_callback('/__PHP[0-9a-f]{10}__/', function ($m) use (&$php, &$report) {
        $code = $php[$m[0]] ?? '';
        if (preg_match('/\$base_url|BASE_URL/', $code)) {
            return '{BASE_URL}';
        }
        $report[] = "Bloque PHP dentro de un texto traducible: $code";
        return '';
    }, $s);
};

$isMixedOrInline = function (array $kids): bool {
    foreach ($kids as $k) {
        if ($k instanceof DOMText) {
            return true; // hay texto suelto junto a elementos
        }
    }
    return false;
};

// ---------------------------------------------------------
// Recorrido en paralelo
// ---------------------------------------------------------
$walk = function (DOMElement $es, DOMElement $en) use (&$walk, &$keys, &$report, $norm, $inner, $sigChildren, $path, $newKey, $jsonText, $isMixedOrInline) {
    if ($es->nodeName !== $en->nodeName) {
        throw new RuntimeException("Estructura distinta en {$path($es)}: <{$es->nodeName}> vs <{$en->nodeName}>");
    }

    // 1) Atributos
    $names = [];
    foreach ($es->attributes as $a) $names[$a->name] = true;
    foreach ($en->attributes as $a) $names[$a->name] = true;

    foreach (array_keys($names) as $name) {
        $vEs = $es->getAttribute($name);
        $vEn = $en->getAttribute($name);
        if ($norm($vEs) === $norm($vEn)) {
            continue;
        }

        $key = $newKey($es, $name);
        $keys[$key] = ['es' => $jsonText($norm($vEs)), 'en' => $jsonText($norm($vEn))];

        if (in_array($name, TRANSLATABLE, true)) {
            $es->setAttribute($name, "@@AX:$key@@");
            $es->setAttribute("data-ozzalt-$name", $key);
        } else {
            // No traducible en vivo (p. ej. src de un script): se resuelve en el servidor
            $es->setAttribute($name, "@@AC:$key@@");
            $report[] = "Atributo '$name' distinto en {$path($es)} (se elige en el servidor)";
        }
    }

    // 2) Contenido idéntico: nada más que hacer
    if ($inner($es) === $inner($en)) {
        return;
    }

    if (in_array($es->nodeName, ['script', 'style'], true)) {
        $report[] = "Contenido de <{$es->nodeName}> distinto en {$path($es)} (se conserva la versión ES)";
        return;
    }

    $kEs = $sigChildren($es);
    $kEn = $sigChildren($en);
    $hasElements = (bool) array_filter($kEs, fn($k) => $k instanceof DOMElement);

    // 3) Solo texto
    if (!$hasElements && !array_filter($kEn, fn($k) => $k instanceof DOMElement)) {
        $key = $newKey($es);
        $keys[$key] = ['es' => $jsonText($norm($es->textContent)), 'en' => $jsonText($norm($en->textContent))];
        while ($es->firstChild) $es->removeChild($es->firstChild);
        $es->appendChild($es->ownerDocument->createTextNode("@@TX:$key@@"));
        $es->setAttribute('data-ozzalt', $key);
        return;
    }

    // 4) Mismos hijos (forma) sin texto suelto: recorrer cada hijo
    $sameShape = count($kEs) === count($kEn);
    if ($sameShape) {
        foreach ($kEs as $i => $c) {
            if (get_class($c) !== get_class($kEn[$i]) || ($c instanceof DOMElement && $c->nodeName !== $kEn[$i]->nodeName)) {
                $sameShape = false;
                break;
            }
        }
    }

    if ($sameShape && !$isMixedOrInline($kEs)) {
        foreach ($kEs as $i => $c) {
            $walk($c, $kEn[$i]);
        }
        return;
    }

    // 5) Texto con formato o estructura distinta: se intercambia el HTML interno
    if (!$sameShape) {
        $report[] = "Estructura interna distinta en {$path($es)} (se usa data-alt-html)";
    }

    $key = $newKey($es, 'html');
    $keys[$key] = ['es' => $jsonText($inner($es)), 'en' => $jsonText($inner($en))];
    while ($es->firstChild) $es->removeChild($es->firstChild);
    $es->appendChild($es->ownerDocument->createTextNode("@@HX:$key@@"));
    $es->setAttribute('data-ozzalthtml', $key);
};

try {
    $kEs = $sigChildren($esRoot);
    $kEn = $sigChildren($enRoot);
    if (count($kEs) !== count($kEn)) {
        throw new RuntimeException('Distinto número de bloques raíz: ' . count($kEs) . ' vs ' . count($kEn));
    }
    foreach ($kEs as $i => $c) {
        if ($c instanceof DOMElement && $kEn[$i] instanceof DOMElement) {
            $walk($c, $kEn[$i]);
        }
    }
} catch (RuntimeException $e) {
    fwrite(STDERR, "ERROR: " . $e->getMessage() . "\n");
    exit(2);
}

// ---------------------------------------------------------
// Serializar la plantilla
// ---------------------------------------------------------
$html = '';
foreach ($esRoot->childNodes as $child) {
    $html .= $esDoc->saveHTML($child);
}

$q = fn(string $k) => "\$T['" . $k . "']";

$html = preg_replace_callback('/ data-ozzalt="([^"]+)"/', fn($m) => '<?= ozz_alt(' . $q($m[1]) . ') ?>', $html);
$html = preg_replace_callback('/ data-ozzalthtml="([^"]+)"/', fn($m) => '<?= ozz_alt_html(' . $q($m[1]) . ') ?>', $html);
$html = preg_replace_callback('/ data-ozzalt-([a-z0-9-]+)="([^"]+)"/', fn($m) => '<?= ozz_alt(' . $q($m[2]) . ", '" . $m[1] . "') ?>", $html);
$html = preg_replace_callback('/@@TX:([^@]+)@@/', fn($m) => '<?= ozz_tx(' . $q($m[1]) . ') ?>', $html);
$html = preg_replace_callback('/@@HX:([^@]+)@@/', fn($m) => '<?= ozz_th(' . $q($m[1]) . ') ?>', $html);
$html = preg_replace_callback('/@@A[XC]:([^@]+)@@/', fn($m) => '<?= ozz_tx(' . $q($m[1]) . ') ?>', $html);
$html = strtr($html, $php);

// Guardas: nunca escribir algo vacío o con marcadores sin resolver
if (trim($html) === '' || str_contains($html, '@@') || preg_match('/__PHP[0-9a-f]{10}__/', $html)) {
    fwrite(STDERR, "ERROR: la plantilla resultante no es válida; no se escribió nada.\n");
    exit(3);
}

$relJson = str_replace('\\', '/', $outJson);
$relJson = preg_replace('#^.*?/Ozz1/#', '', $relJson);

$header = "<?php\n"
    . "// Plantilla bilingüe generada con tools/i18n-merge.php a partir de\n"
    . "//   " . basename($esFile) . " + " . basename($enFile) . "\n"
    . "// Textos: $relJson ({\"es\": …, \"en\": …}). Edite los textos allí.\n"
    . "require_once ROOT_PATH . '/config/helpers.php';\n"
    . "\$T = ozz_json('" . $relJson . "');\n"
    . "?>\n";

$json = json_encode($keys, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

@mkdir(dirname($outTpl), 0777, true);
@mkdir(dirname($outJson), 0777, true);
file_put_contents($outTpl, $header . $html);
file_put_contents($outJson, $json . "\n");

echo "OK  " . count($keys) . " textos → $outTpl\n";
foreach (array_unique($report) as $r) {
    echo "  · $r\n";
}
