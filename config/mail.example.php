<?php
// Plantilla de configuración SMTP. Copie este archivo como config/mail.php
// y complete los datos reales (config/mail.php NO se sube al repositorio).
// También puede usar variables de entorno: OZZ_SMTP_HOST, OZZ_SMTP_USER,
// OZZ_SMTP_PASS y OZZ_SMTP_PORT.
return [
    'host'     => getenv('OZZ_SMTP_HOST') ?: 'smtp.example.com',
    'username' => getenv('OZZ_SMTP_USER') ?: 'usuario@example.com',
    'password' => getenv('OZZ_SMTP_PASS') ?: '',
    'port'     => (int) (getenv('OZZ_SMTP_PORT') ?: 465),
    'from'     => 'team@ozzcreativestudio.com',
    'fromName' => 'Ozz Creative Studio',
    'to'       => ['team@ozzcreativestudio.com'],
];
