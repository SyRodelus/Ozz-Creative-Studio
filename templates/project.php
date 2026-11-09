<?php
// =========================================================
// PÁGINA DE PROYECTO — compartida por Work/<Proyecto>.php (EN)
// y Work/<Proyecto>-es.php (ES).
// Espera $project_id (clave en data/projects.json).
// - Metadatos: data/projects.json → <id>.meta
// - Contenido: <pageDir>/Project.php + <pageDir>/texts.json
// =========================================================

require_once ROOT_PATH . '/config/helpers.php';

$project = ozz_json('data/projects.json')[$project_id ?? ''] ?? null;

if (!$project || empty($project['pageDir'])) {
    http_response_code(404);
    exit('Proyecto no encontrado');
}

$page = 'project';
$page_meta = $project['meta'] ?? [];

include ROOT_PATH . '/templates/layout-start.php';
include ROOT_PATH . '/' . $project['pageDir'] . '/Project.php';
include ROOT_PATH . '/templates/layout-end.php';
