<?php
/**
 * Esse arquivo é responsável por exibir o nome do amigo sorteado
 * para cada participante.
 * Quando o sorteio ocorre, o participante recebe um link
 * para conferir o nome do seu amigo.
 * O link possui "uma criptografia" simples, para não
 * ficar explicito na url ;-)
 */ 
 
$config = require 'config.php';
 
if (!isset($_GET['chave'])) {
    die('Link inválido.');
}

$hash = $_GET['chave'];
$arquivo = 'resultados.json';

if (!file_exists($arquivo)) {
    die('Nenhum sorteio encontrado.');
}

$resultados = json_decode(file_get_contents($arquivo), true);
$encontrado = null;
$indexEncontrado = null;

foreach ($resultados as $idx => $item) {
    if ($item['hash'] === $hash) {
        $encontrado = $item;
        $indexEncontrado = $idx;
        break;
    }
}

if ($encontrado === null) {
    die('Participante não encontrado.');
}

// registra a visualização (permite múltiplas)
$resultados[$indexEncontrado]['view_count'] = intval($resultados[$indexEncontrado]['view_count']) + 1;
$resultados[$indexEncontrado]['last_viewed'] = date('Y-m-d H:i:s');

// atualiza o arquivo JSON (persistir o histórico)
file_put_contents($arquivo, json_encode($resultados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Amigo Secreto da familia 🎁</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta charset="UTF-8">
    <meta name="description" content="Revele quem é o seu amigo secreto!">
    <meta name="keywords" content="Amigo Secreto, Natal, Presente, Sorteio">
    <meta name="author" content="Denis Brandl">

    <meta property="og:title" content="Amigo Secreto da familia 🎁">
    <meta property="og:description" content="Revele quem é o seu amigo secreto!">
    <meta property="og:image" content="<?= $config['url_acesso']; ?>amigo_secreto.jpg">
    <meta property="og:url" content="<?= $config['url_acesso']; ?>reveal.php">

    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding-top: 60px; background:#f9f9f9; }
        .card { display: inline-block; background: white; padding: 40px 60px; border-radius: 12px; box-shadow:0 6px 22px rgba(0,0,0,0.08); }
        h1 { color:#d63384; margin:0 0 10px }
        h2 { color:#0d6efd; margin: 8px 0 18px }
        .meta { color:#666; font-size:14px; margin-top:10px }
    </style>
</head>
<body>
    <div class="card">
        <h1>🎁 Olá, <?= htmlspecialchars($encontrado['nome']) ?>!</h1>
        <p>Seu amigo secreto é:</p>
        <h2><?= htmlspecialchars($encontrado['amigo']) ?></h2>
        <p>🤫 Guarde esse segredo até o dia da revelação!</p>

        <p>
            Não esqueça de enviar sua sugestão de presente no grupo do WhatsApp! 🎄
        </p>

        <p> Lembrando que o valor sugerido é de R$50,00. </p>
    </div>
</body>
</html>
