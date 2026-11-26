<?php
/**
 * Esse arquivo permite fazer um novo sorteio,
 * visualizar a lista de participantes,
 * e acompanhar quem acessou o seu amigo secreto.
 * Na prática, o administrador consegue visualizar quem sorteou quem,
 * mas o segredo é o que garante a diversão :-)
*/ 

session_start();
// Verifica se o administrador está autenticado
if (empty($_SESSION['admin_logado'])) {
    header('Location: index.php');
    exit;
}


$arquivo = 'resultados.json';
$sorteioScript = 'sorteio.php';
$participantes = require 'participantes.php';
$config = require 'config.php';

array_multisort(array_column($participantes, 'nome'), SORT_ASC, $participantes);

// se o botão "Gerar novo sorteio" foi clicado
if (isset($_GET['reset']) && $_GET['reset'] === '1') {
    // executa o script sorteio.php novamente
    exec("php " . escapeshellarg($sorteioScript));
    die('Fim');
    header("Location: ".$config['url_acesso']."admin.php?gerado=1");
    exit;
}

if (!file_exists($arquivo)) {
    die('Nenhum sorteio encontrado. Execute sorteio.php primeiro.');
}

$resultados = json_decode(file_get_contents($arquivo), true) ?? [];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo - Amigo Secreto 🎅</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f8; margin:0; padding:0; }
        h1 { text-align:center; background:#0d6efd; color:#fff; padding:18px 0; margin:0; }
        h2 { text-align:center; background:#BEC3C9; color:#fff; padding:18px 0; margin:0; }
        .toolbar { text-align:center; margin:14px 0; }
        .btn { display:inline-block; padding:10px 18px; border-radius:6px; color:#fff; background:#0d6efd; text-decoration:none; margin:0 6px; font-size:14px;}
        .btn-danger { background:#dc3545; }
        .btn:hover { filter:brightness(.95); }
        table { width:95%; margin:16px auto 40px; border-collapse: collapse; background:#fff; box-shadow:0 4px 12px rgba(0,0,0,.06); }
        th, td { padding:12px 14px; border-bottom:1px solid #eee; text-align:left; font-size:14px; }
        th { background:#f8f9fa; }
        tr:hover { background:#fafafa; }
        .copy-btn { background:#198754; border:none; color:white; padding:6px 10px; border-radius:5px; cursor:pointer; font-size:13px; }
        .footer { text-align:center; padding:14px; color:#666; }
		h5 { text-align:center; padding:14px; color:#666; background-color: red; color: white; }
    </style>
    <script>
        function copiarLink(link) {
            navigator.clipboard.writeText(link).then(() => {
                alert('Link copiado: ' + link);
            });
        }
    </script>
</head>
<body>
    <h1>🎁 Painel Administrativo - Amigo Secreto</h1>

    <?php if (isset($_GET['gerado'])): ?>
        <div style="text-align:center; background:#d1e7dd; color:#0f5132; padding:10px;">✅ Novo sorteio gerado com sucesso!</div>
    <?php endif; ?>

    <div class="toolbar">
        <a href="<?= $config['url_acesso']; ?>admin.php?reset=1" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja gerar um novo sorteio? Isso apagará o atual.')">🔁 Gerar Novo Sorteio</a>
		<a href="index.php?logout=1" class="btn">
			🚪 Sair
		</a>        
    </div>
    
    <h2> Participantes </h2>
    <table>
		<tr>
			<th>Nome</th>
			<th>Telefone</th>
		</tr>
		<?php foreach ($participantes as $participante) { ?>
			<tr>
				<th><?= $participante['nome'];?></th>
				<th><?= str_replace("55", "", $participante['telefone']);?></th>
			</tr>
		<?php } ?>
	</table>

	<?php if (count($resultados) > 0) { ?>
		<table>
			<tr>
				<th>#</th>
				<th>Nome</th>
				<th>Telefone</th>
				<th>Visualizações</th>
				<th>Última Visualização</th>
				<th>Link</th>
				<th>Ações</th>
			</tr>
			<?php foreach ($resultados as $i => $r): 
				$link = $config['url_acesso'] . "reveal.php?chave=" . urlencode($r['hash']);
			?>
				<tr>
					<td><?= $i + 1 ?></td>
					<td><?= htmlspecialchars($r['nome']) ?></td>
					<td><?= htmlspecialchars($r['telefone']) ?></td>
					<td><?= intval($r['view_count']) ?></td>
					<td><?= $r['last_viewed'] ? date('d/m/Y H:i', strtotime($r['last_viewed'])) : '-' ?></td>
					<td><a href="<?= $link ?>" target="_blank">Abrir</a></td>
					<td><button class="copy-btn" onclick="copiarLink('<?= $link ?>')">📋 Copiar</button></td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php }  else { ?>
		<div class="container">
			<h5> Nenhum sorteio ativo. Clique em Gerar Novo Sorteio para fazer o sorteio. </h5>
		</div>
	<?php } ?>

    <div class="footer">
        <small>Atualizado em <?= date('d/m/Y H:i') ?></small>
    </div>
</body>
</html>
