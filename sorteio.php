<?php
/**
 * Esse arquivo é que faz o sorteio dos participantes.
 * É possível configurar para que um participante não tire uma ou mais pessoas
 * Ou obrigar uma pessoa a tirar alguém de uma lista.
*/ 
$participantes = require 'participantes.php';
$config = require 'config.php';

// ==== CONFIGURAÇÕES DE RESTRIÇÕES ====

// 1️⃣ Pessoas que NÃO podem tirar certas outras
// Exemplo: 'Pedro' não pode tirar 'Ana' nem 'João'
$restricoes = [
    'Pedro' => ['Ana','João'],
    'Maria' => ['João', 'José'],
];

// 2️⃣ Pessoas que DEVEM tirar alguém específico
// Exemplo: 'Ana' deve tirar 'João'
$forcados = [
    // 'Bóris' => 'Bidu' // João obrigatoriamente tira Maria
];

// 3️⃣ Quem DEVE SER SORTEADO por (reverso do forçamento)
// Exemplo: Bóris deve ser sorteado por Maria OU João OU Pedro
$restricoes_recebedor = [
    'Pedro' => ['Ana','João'],
    'Maria' => ['João', 'José'],
];

$nomes = array_column($participantes, 'nome');
$total = count($nomes);


$sorteioValido = false;
$tentativas = 0;

// ==== FUNÇÃO PARA VALIDAR RESULTADO ====
function valido($nomes, $sorteio, $restricoes, $forcados, $restricoes_recebedor)
{
    for ($i = 0; $i < count($nomes); $i++) {
        $doador = $nomes[$i];
        $recebedor = $sorteio[$i];

        // Não pode tirar a si mesmo
        if ($doador === $recebedor) return false;

        // 1️⃣ Restrição direta
        if (isset($restricoes[$doador]) && in_array($recebedor, $restricoes[$doador])) {
            return false;
        }

        // 2️⃣ Forçamento direto
        if (isset($forcados[$doador]) && $forcados[$doador] !== $recebedor) {
            return false;
        }

        // 3️⃣ Restrição reversa (quem pode ser sorteado por quem)
        if (isset($restricoes_recebedor[$recebedor])) {
            $permitidos = $restricoes_recebedor[$recebedor];
            if (!in_array($doador, $permitidos)) {
                return false;
            }
        }
    }

    // Evita duplicações
    if (count($sorteio) !== count(array_unique($sorteio))) return false;

    return true;
}

// ==== LOOP DE SORTEIO ====
do {
    $tentativas++;
    $sorteio = $nomes;
    shuffle($sorteio);
    $sorteioValido = valido($nomes, $sorteio, $restricoes, $forcados, $restricoes_recebedor);
} while (!$sorteioValido && $tentativas < 5000);

if (!$sorteioValido) {
    die("❌ Não foi possível gerar um sorteio válido após 5000 tentativas. Verifique as restrições e forçamentos.\n");
}

$resultados = [];
foreach ($participantes as $i => $pessoa) {
    // hash única
    $hash = hash('sha256', $pessoa['telefone'] . $pessoa['nome'] . uniqid('', true));
    $amigo = $sorteio[$i];

    $resultados[] = [
        'nome' => $pessoa['nome'],
        'telefone' => $pessoa['telefone'],
        'amigo' => $amigo,
        'hash' => $hash,
        // controle de acessos agora permite múltiplas visualizações
        'view_count' => 0,
        'last_viewed' => null
    ];

    echo "{$pessoa['nome']} -> {$amigo}\n";
    echo "Link: ".$config['url_acesso']."reveal.php?chave={$hash}\n\n";
}

// salva os resultados
file_put_contents('resultados.json', json_encode($resultados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Sorteio concluído e links gerados!\n";

echo "\nIniciando envio das mensagens via WhatsApp em 90 segundos...\n";


// sleep(90);

// ==== ENVIO VIA ULTRAMSG ====
foreach ($resultados as $i => $pessoa) {
    $link = $config["url_acesso"]."reveal.php?chave=" . $pessoa['hash'];
    $mensagem = "🎁 Olá, {$pessoa['nome']}! Seu amigo secreto foi sorteado!\n\n"
              . "Clique para descobrir: {$link}\n\n"
              . "🤫 Guarde esse segredo até o dia da revelação!";

    echo ($i + 1) . "/{$total} Enviando para {$pessoa['nome']} ({$pessoa['telefone']})...\n";
    enviarMensagemWhatsApp($pessoa['telefone'], $mensagem, $config);

    if ($i < $total - 1) {
        $espera = rand(20, 300);
        echo "Aguardando {$espera}s para o próximo envio...\n";
        sleep($espera);
    }
}

echo "\n✅ Mensagens enviadas para todos os participantes.\n";

// ==== FUNÇÃO DE ENVIO ====
function enviarMensagemWhatsApp($telefone, $mensagem, $config)
{
    $data = [
        'token' => $config['token'],
        'to' => $telefone,
        'body' => $mensagem
    ];

	/*
	 * A mensagem é enviada usando o Ultramsg (https://ultramsg.com/pt/)
	 * Fique a vontade para implementar sua API favorita;
	 */ 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $config['api_url']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        echo "Erro ao enviar para {$telefone}: $err\n";
    } else {
        echo "Mensagem enviada para {$telefone}: $response\n";
    }
}
