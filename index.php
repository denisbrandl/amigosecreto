<?php
session_start();

// Senha do painel administrativo
$senha_admin = 'TroqueEstaSenha!2026';

// Logout
if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();

    header('Location: index.php');
    exit;
}

// Se já estiver autenticado, vai direto para o painel
if (!empty($_SESSION['admin_logado'])) {
    header('Location: admin.php');
    exit;
}

// Processa o formulário
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $senha = $_POST['senha'] ?? '';

    if (hash_equals($senha_admin, $senha)) {

        session_regenerate_id(true);

        $_SESSION['admin_logado'] = true;

        header('Location: admin.php');
        exit;

    } else {

        $erro = 'Senha incorreta!';

    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Amigo Secreto - Administração</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login {
            background: #fff;
            width: 100%;
            max-width: 380px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 18px rgba(0,0,0,.10);
        }

        h1 {
            text-align: center;
            font-size: 22px;
            margin: 0 0 24px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            border: 0;
            border-radius: 6px;
            color: #fff;
            background: #0d6efd;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            filter: brightness(.95);
        }

        .erro {
            background: #f8d7da;
            color: #842029;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="login">

        <h1>🎁 Amigo Secreto</h1>

        <?php if ($erro): ?>
            <div class="erro">
                <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="post">

            <label for="senha">Senha de acesso</label>

            <input
                type="password"
                id="senha"
                name="senha"
                required
                autofocus
            >

            <button type="submit">
                Entrar
            </button>

        </form>

    </div>

</body>
</html>
