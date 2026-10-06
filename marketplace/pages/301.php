<?php
// ================================
//  Página de Redirecionamento 301
// ================================

// Defina aqui para onde deve redirecionar
$newUrl = INCLUDE_PATH_LOJA . "c/" . $link;

// Evita looping caso a URL já seja a nova
if ($_SERVER['REQUEST_URI'] === parse_url($newUrl, PHP_URL_PATH)) {
    exit(__('page_moved_title'));
}

// Define cabeçalho 301 (Moved Permanently)
header("HTTP/1.1 301 Moved Permanently");
header("Location: $newUrl");
?>

<h1><?= __('page_moved_title') ?></h1>
<p><?= __('page_moved_text') ?>:</p>
<p><a href="<?= $newUrl; ?>" class="btn btn-dark"><?= __('page_moved_button') ?></a></p>

<script>
    $(document).ready(function () {
        // Redirecionamento via JS
        window.location.replace("<?= $newUrl; ?>");
    });
</script>