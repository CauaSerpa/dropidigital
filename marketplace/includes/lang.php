<?php

function loadLanguage($lang) {
    static $translations = [];

    if (isset($translations[$lang])) {
        return $translations[$lang];
    }

    $file = __DIR__ . "/../lang/{$lang}.php";

    if (!file_exists($file)) {
        $file = __DIR__ . "/../lang/pt.php"; // fallback global
    }

    $translations[$lang] = require $file;
    return $translations[$lang];
}

function __($key, $default = null) {
    global $currentLang;

    $translations = loadLanguage($currentLang);

    if (isset($translations[$key])) {
        return $translations[$key];
    }

    // fallback para pt se a chave não existir
    $fallback = loadLanguage('pt');

    if (isset($fallback[$key])) {
        return $fallback[$key];
    }

    // último fallback
    return $default ?? $key;
}