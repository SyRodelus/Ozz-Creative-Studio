<?php
// INICIO — compartido por index.php (EN) e index-es.php (ES)
$page = 'home';
$with_preloader = true;

include ROOT_PATH . '/templates/layout-start.php';
include ROOT_PATH . '/pages/index/Hero.php';
include ROOT_PATH . '/pages/index/ProjectsStackCards.php';
include ROOT_PATH . '/pages/index/Services.php';
include ROOT_PATH . '/pages/index/Expertise.php';
include ROOT_PATH . '/templates/layout-end.php';
