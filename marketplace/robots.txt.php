<?php
header('Content-Type: text/plain; charset=utf-8');

echo "# ==========================================\n";
echo "# ROBOTS.TXT - SITE PHP COM URL AMIGAVEL\n";
echo "# ==========================================\n\n";

echo "User-agent: *\n\n";

echo "# 🚫 Áreas privadas\n";
echo "Disallow: /newsletter/\n";
echo "Disallow: /busca\n\n";

echo "# 🚫 Parametros (evita conteudo duplicado)\n";
echo "Disallow: /*?utm_\n";
echo "Disallow: /*?fbclid=\n";
echo "Disallow: /*?gclid=\n";
echo "Disallow: /*?ref=\n";
echo "Disallow: /*?session=\n";

echo "# ✅ Recursos essenciais\n";
echo "Allow: *.css\n";
echo "Allow: *.js\n";
echo "Allow: *.png\n";
echo "Allow: *.jpg\n";
echo "Allow: *.jpeg\n";
echo "Allow: *.gif\n";
echo "Allow: *.svg\n";
echo "Allow: *.webp\n";
echo "Allow: *.pdf\n\n";

echo "Allow: /\n\n";

echo "# ==========================================\n";
echo "# 🤖 BOTS DE IA (PERMITIDOS)\n";
echo "# ==========================================\n\n";

echo "User-agent: GPTBot\n";
echo "Allow: /\n\n";

echo "User-agent: OAI-SearchBot\n";
echo "Allow: /\n\n";

echo "User-agent: SearchGPT\n";
echo "Allow: /\n\n";

echo "User-agent: ClaudeBot\n";
echo "Allow: /\n\n";

echo "User-agent: Google-Extended\n";
echo "Allow: /\n\n";

echo "User-agent: PerplexityBot\n";
echo "Allow: /\n\n";

echo "User-agent: CCBot\n";
echo "Allow: /\n\n";

echo "# ==========================================\n";
echo "# 🗺️ SITEMAP\n";
echo "# ==========================================\n\n";

echo "Sitemap: " . INCLUDE_PATH_LOJA . "sitemap_index.xml\n";

exit;