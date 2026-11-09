<?php
// =========================================================
// Tarjeta de proyecto estilo póster (compartida)
// Usada por: pages/index/ProjectsStackCards.php (inicio)
//            pages/Work/WorkGrid.php (Proyectos)
// Datos: data/projects.json
// =========================================================

require_once ROOT_PATH . '/config/helpers.php';

if (!function_exists('ozz_projects')) {
    /** Todos los proyectos (data/projects.json), en orden. */
    function ozz_projects(): array
    {
        return ozz_json('data/projects.json');
    }
}

if (!function_exists('ozz_project_card')) {
    /**
     * @param string $id      clave en data/projects.json
     * @param array  $p       datos del proyecto
     * @param int    $n       posición (1..)
     * @param int    $total   total de tarjetas
     * @param string $sizes   atributo sizes del poster
     */
    function ozz_project_card(string $id, array $p, int $n, int $total, string $sizes): string
    {
        $pad = static fn(int $x) => str_pad((string) $x, 2, '0', STR_PAD_LEFT);
        $studio = ozz_json('lang/ui.json')['common']['studio'] ?? 'Ozz';

        $year = trim((string) ($p['year'] ?? ''));
        $corner = $year !== '' ? $year : $pad($n) . ' / ' . $pad($total);

        // Video => atributo para Scripts/ResponsiveMedia.js
        $video_sources = ozz_sources($p['video']['sources'] ?? []);
        $video_attr = $video_sources
            ? ' data-media-video="' . ozz_e(json_encode(['sources' => $video_sources], JSON_UNESCAPED_SLASHES)) . '"'
            : '';

        // Poster responsive nativo (srcset)
        $poster_alt = $p['poster']['alt'] ?? '';
        $poster = ozz_img($p['poster'] ?? [], ozz_lang(), [
            'class' => 'sc-poster',
            'sizes' => $sizes,
        ]);
        if ($poster !== '') {
            $poster = substr($poster, 0, -1) . ozz_alt($poster_alt, 'alt') . '>';
        }

        ob_start();
        ?>
    <li class="panel sc-panel" id="<?= ozz_e($id) ?>" style="--i: <?= $n - 1 ?>">
      <a class="no-underline sc-card" <?= ozz_href($p['href'] ?? '') ?>>
        <div class="sc-media"<?= $video_attr ?>>
          <?= $poster ?>
        </div>

        <span class="sc-shade" aria-hidden="true"></span>

        <div class="sc-top ozz-label" aria-hidden="true">
          <span<?= ozz_alt($studio) ?>><?= ozz_tx($studio) ?></span>
          <span<?= ozz_alt($p['discipline'] ?? '') ?>><?= ozz_tx($p['discipline'] ?? '') ?></span>
          <span><?= ozz_e($corner) ?></span>
        </div>

        <div class="sc-foot">
          <p class="sc-desc"<?= ozz_alt($p['description'] ?? '') ?>><?= ozz_tx($p['description'] ?? '') ?></p>

          <div class="sc-bottom">
            <h3 class="sc-name"<?= ozz_alt($p['title'] ?? '') ?>><?= ozz_tx($p['title'] ?? '') ?></h3>
            <span class="sc-sector ozz-label"<?= ozz_alt($p['sector'] ?? '') ?>><?= ozz_tx($p['sector'] ?? '') ?></span>
          </div>
        </div>
      </a>
    </li>
        <?php
        return (string) ob_get_clean();
    }
}

if (!function_exists('ozz_projects_ld')) {
    /** JSON-LD (VideoObject) para los proyectos con video. */
    function ozz_projects_ld(array $projects): string
    {
        $graph = [];

        foreach ($projects as $p) {
            $videos = ozz_sources($p['video']['sources'] ?? []);
            if (!$videos) {
                continue;
            }

            $posters = ozz_sources($p['poster']['sources'] ?? []);

            $graph[] = array_filter([
                '@type'        => 'VideoObject',
                'name'         => $p['video']['title'] ?? ozz_t($p['title'] ?? '', 'en'),
                'description'  => $p['video']['description'] ?? ozz_t($p['description'] ?? '', ozz_lang()),
                'thumbnailUrl' => $posters ? end($posters)['src'] : null,
                'contentUrl'   => end($videos)['src'],
                'uploadDate'   => $p['video']['uploadDate'] ?? null,
                'duration'     => $p['video']['duration'] ?? null,
                'keywords'     => !empty($p['video']['keywords']) ? implode(', ', $p['video']['keywords']) : null,
            ]);
        }

        if (!$graph) {
            return '';
        }

        return '<script type="application/ld+json">' . json_encode(
            ['@context' => 'https://schema.org', '@graph' => $graph],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG
        ) . '</script>';
    }
}
