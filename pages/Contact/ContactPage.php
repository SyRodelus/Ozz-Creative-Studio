<?php
// =========================================================
// CONTACTO ("Talk to Us") — ES / EN
// ---------------------------------------------------------
// Textos: lang/ui.json → "contact"
// Servicios del formulario: pages/index/texts/services.json
// Envío: pages/Contact/ContactForm.js → php/sendemail.php
// =========================================================

require_once ROOT_PATH . '/config/helpers.php';

if (session_status() === PHP_SESSION_ACTIVE && empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$ct = ozz_json('lang/ui.json')['contact'] ?? [];
$ct_services = ozz_json('pages/index/texts/services.json')['items'] ?? [];
$ct_budget = ozz_list_pairs($ct['budgetOptions'] ?? []);
$ct_h1 = $ct['title'] ?? '';

$ct_email = 'team@ozzcreativestudio.com';
$ct_phone = '+57 315 536 3130';
$ct_whatsapp = 'https://wa.me/573155363130';

$ct_social = [
    ['label' => 'Instagram', 'href' => 'https://www.instagram.com/ozzcreativestudio?igsh=ZjlvMHVjbDM4ODRs'],
    ['label' => 'Behance',   'href' => 'https://www.behance.net/mateogonzalez5'],
];

$ct_arrow = '<svg viewBox="0 0 640 640" aria-hidden="true" focusable="false"><path d="M566.6 342.6C579.1 330.1 579.1 309.8 566.6 297.3L406.6 137.3C394.1 124.8 373.8 124.8 361.3 137.3C348.8 149.8 348.8 170.1 361.3 182.6L466.7 288L96 288C78.3 288 64 302.3 64 320C64 337.7 78.3 352 96 352L466.7 352L361.3 457.4C348.8 469.9 348.8 490.2 361.3 502.7C373.8 515.2 394.1 515.2 406.6 502.7L566.6 342.7z"/></svg>';

// Campo de texto con etiqueta flotante
$ct_field = static function (string $name, $label, string $type = 'text', array $extra = []) {
    $attrs = '';
    foreach ($extra as $k => $v) {
        $attrs .= $v === true ? ' ' . $k : ' ' . $k . '="' . ozz_e($v) . '"';
    }
    $required = !empty($extra['required']);

    return '<div class="ct-field">'
        . '<input class="ct-input" type="' . ozz_e($type) . '" id="ct-' . ozz_e($name) . '" name="' . ozz_e($name) . '" placeholder=" "' . $attrs . '>'
        . '<label class="ct-label" for="ct-' . ozz_e($name) . '"><span' . ozz_alt($label) . '>' . ozz_tx($label) . '</span>' . ($required ? ' *' : '') . '</label>'
        . '</div>';
};
?>
<link rel="stylesheet" href="<?= ozz_e(ozz_asset('pages/Contact/ContactPage.css')) ?>" />

<section class="ct" id="contact" aria-labelledby="contact-title">
  <header class="ozz-head ct-head">
    <div class="ozz-head__bar ozz-label">
      <span<?= ozz_alt($ct['number'] ?? '') ?>><?= ozz_tx($ct['number'] ?? '') ?></span>
      <span<?= ozz_alt($ct['kicker'] ?? '') ?>><?= ozz_tx($ct['kicker'] ?? '') ?></span>
      <span<?= ozz_alt($ct['reply'] ?? '') ?>><?= ozz_tx($ct['reply'] ?? '') ?></span>
    </div>

    <h1 class="ozz-head__title reveal-lines" id="contact-title" style="--chars: <?= max(1, mb_strlen(ozz_t($ct_h1, ozz_lang()))) ?>"<?= ozz_alt($ct_h1) ?>><?= ozz_tx($ct_h1) ?></h1>

    <div class="ozz-head__aside">
      <p class="ozz-head__intro"<?= ozz_alt($ct['intro'] ?? '') ?>><?= ozz_tx($ct['intro'] ?? '') ?></p>
    </div>
  </header>

  <div class="ct-body">
    <!-- Canales -->
    <aside class="ct-channels" aria-labelledby="ct-channels-title">
      <h2 class="ozz-label ct-subtitle" id="ct-channels-title"<?= ozz_alt($ct['channelsTitle'] ?? '') ?>><?= ozz_tx($ct['channelsTitle'] ?? '') ?></h2>

      <a class="ct-channel" href="mailto:<?= ozz_e($ct_email) ?>">
        <span class="ozz-label ct-channel__label"<?= ozz_alt($ct['emailLabel'] ?? '') ?>><?= ozz_tx($ct['emailLabel'] ?? '') ?></span>
        <span class="ct-channel__value ct-channel__value--long"><?= ozz_e($ct_email) ?></span>
        <span class="ct-channel__arrow"><?= $ct_arrow ?></span>
      </a>

      <a class="ct-channel" href="<?= ozz_e($ct_whatsapp) ?>" target="_blank" rel="noopener">
        <span class="ozz-label ct-channel__label"<?= ozz_alt($ct['whatsappLabel'] ?? '') ?>><?= ozz_tx($ct['whatsappLabel'] ?? '') ?></span>
        <span class="ct-channel__value"><?= ozz_e($ct_phone) ?></span>
        <span class="ct-channel__arrow"><?= $ct_arrow ?></span>
      </a>

      <div class="ct-social">
        <span class="ozz-label ct-channel__label"<?= ozz_alt($ct['socialTitle'] ?? '') ?>><?= ozz_tx($ct['socialTitle'] ?? '') ?></span>
        <ul>
          <?php foreach ($ct_social as $ct_s): ?>
          <li><a href="<?= ozz_e($ct_s['href']) ?>" target="_blank" rel="noopener"><?= ozz_e($ct_s['label']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </aside>

    <!-- Formulario -->
    <form class="ct-form" id="ctForm" novalidate aria-labelledby="ct-form-title"
          action="<?= ozz_e(BASE_URL) ?>/php/sendemail.php" method="post">
      <h2 class="ozz-label ct-subtitle" id="ct-form-title"<?= ozz_alt($ct['formTitle'] ?? '') ?>><?= ozz_tx($ct['formTitle'] ?? '') ?></h2>

      <input type="hidden" name="csrf_token" value="<?= ozz_e($_SESSION['csrf_token'] ?? '') ?>">
      <input type="hidden" name="lang" value="<?= ozz_e(ozz_lang()) ?>" data-alt-value="<?= ozz_e(ozz_other_lang()) ?>">

      <!-- Honeypot anti-spam (oculto para personas) -->
      <div class="ct-hp" aria-hidden="true">
        <label for="ct-website">Website</label>
        <input type="text" id="ct-website" name="website" tabindex="-1" autocomplete="off">
      </div>

      <div class="ct-row">
        <?= $ct_field('firstName', $ct['firstName'] ?? '', 'text', ['required' => true, 'maxlength' => '25', 'autocomplete' => 'given-name']) ?>
        <?= $ct_field('lastName', $ct['lastName'] ?? '', 'text', ['required' => true, 'maxlength' => '25', 'autocomplete' => 'family-name']) ?>
      </div>

      <div class="ct-row">
        <?= $ct_field('email', $ct['email'] ?? '', 'email', ['required' => true, 'maxlength' => '80', 'autocomplete' => 'email']) ?>
        <?= $ct_field('company', $ct['company'] ?? '', 'text', ['maxlength' => '80', 'autocomplete' => 'organization']) ?>
      </div>

      <fieldset class="ct-fieldset">
        <legend class="ozz-label"<?= ozz_alt($ct['serviceLegend'] ?? '') ?>><?= ozz_tx($ct['serviceLegend'] ?? '') ?></legend>
        <div class="ct-chips">
          <?php foreach ($ct_services as $ct_s): ?>
          <label class="ct-chip">
            <input type="checkbox" name="services[]" value="<?= ozz_e($ct_s['slug'] ?? '') ?>">
            <span<?= ozz_alt($ct_s['title'] ?? '') ?>><?= ozz_tx($ct_s['title'] ?? '') ?></span>
          </label>
          <?php endforeach; ?>
          <label class="ct-chip">
            <input type="checkbox" name="services[]" value="Other">
            <span<?= ozz_alt($ct['serviceOther'] ?? '') ?>><?= ozz_tx($ct['serviceOther'] ?? '') ?></span>
          </label>
        </div>
      </fieldset>

      <div class="ct-field ct-field--select">
        <select class="ct-input" id="ct-budget" name="budget">
          <?php foreach ($ct_budget as $ct_i => $ct_b): ?>
          <option value="<?= $ct_i ?>"<?= ozz_alt($ct_b) ?>><?= ozz_tx($ct_b) ?></option>
          <?php endforeach; ?>
        </select>
        <label class="ct-label ct-label--fixed" for="ct-budget"><span<?= ozz_alt($ct['budget'] ?? '') ?>><?= ozz_tx($ct['budget'] ?? '') ?></span></label>
      </div>

      <div class="ct-field ct-field--area">
        <textarea class="ct-input" id="ct-message" name="message" rows="5" maxlength="1500" placeholder=" " required></textarea>
        <label class="ct-label" for="ct-message"><span<?= ozz_alt($ct['message'] ?? '') ?>><?= ozz_tx($ct['message'] ?? '') ?></span> *</label>
        <span class="ct-hint"<?= ozz_alt($ct['messagePlaceholder'] ?? '') ?>><?= ozz_tx($ct['messagePlaceholder'] ?? '') ?></span>
      </div>

      <label class="ct-consent">
        <input type="checkbox" name="accept" value="1" required>
        <span>
          <span<?= ozz_alt($ct['consentA'] ?? '') ?>><?= ozz_tx($ct['consentA'] ?? '') ?></span>
          <a <?= ozz_href('Privacy_Policy') ?> target="_blank"<?= ozz_alt($ct['consentLink'] ?? '') ?>><?= ozz_tx($ct['consentLink'] ?? '') ?></a>.
        </span>
      </label>

      <div class="ct-actions">
        <button class="ct-submit" type="submit">
          <span class="ct-submit__text"<?= ozz_alt($ct['submit'] ?? '') ?>><?= ozz_tx($ct['submit'] ?? '') ?></span>
          <?= $ct_arrow ?>
        </button>
        <p class="ct-required ozz-label">* <span<?= ozz_alt($ct['required'] ?? '') ?>><?= ozz_tx($ct['required'] ?? '') ?></span></p>
      </div>

      <div class="ct-status" role="status" aria-live="polite"></div>
    </form>
  </div>
</section>

<?php include ROOT_PATH . '/partials/Reveal/reveal.html'; ?>

<script defer src="<?= ozz_e(ozz_asset('pages/Contact/ContactForm.js')) ?>"></script>
