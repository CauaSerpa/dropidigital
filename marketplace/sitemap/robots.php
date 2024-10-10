<?php
    // Define o tipo de conteúdo como texto
    header("Content-Type: text/plain");

    // Define os caminhos de URL para o sitemap
    $sitemapUrl = $urlCompleta . "sitemap.xml";

    // Gera o conteúdo do arquivo robots.txt
    echo "User-agent: *\n";
    echo "Disallow: /private/\n";
    echo "Disallow: /tmp/\n";
    echo "Disallow: /admin/\n";
    echo "Disallow: /login/\n";
    echo "Allow: /\n";
    echo "Sitemap: $sitemapUrl\n";