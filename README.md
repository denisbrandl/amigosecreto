# 🎁 Amigo Secreto

Sistema simples para organizar sorteios de amigo secreto, desenvolvido em PHP, com acesso administrativo, sorteio automático, regras personalizadas e revelação individual através de links privados.

O projeto foi desenvolvido para uso familiar, permitindo organizar o sorteio sem depender de aplicativos específicos, cadastros externos ou banco de dados.

Cada participante recebe um link exclusivo para descobrir quem tirou, mantendo o segredo entre os participantes.

---

## ✨ Funcionalidades

- 🎲 Sorteio automático de amigo secreto.
- 🚫 Impede que uma pessoa tire a si mesma.
- 🔀 Evita que um participante seja sorteado mais de uma vez.
- ⚙️ Configuração de restrições entre participantes.
- 🎯 Possibilidade de definir sorteios obrigatórios.
- 🔒 Painel administrativo protegido por senha.
- 🔗 Geração de links individuais para cada participante.
- 🎁 Revelação do amigo secreto por link.
- 👀 Controle de quantidade de visualizações de cada link.
- 🕒 Registro da data e hora do último acesso.
- 📋 Botão para copiar os links individuais.
- 🔄 Possibilidade de realizar um novo sorteio.
- 📱 Interface adaptada para dispositivos móveis.

---

## 🎯 Propósito

O objetivo do projeto é simplificar a organização de um amigo secreto entre familiares e amigos.

O organizador cadastra os participantes, configura eventuais restrições e realiza o sorteio.

Após o sorteio, cada participante possui um link individual que permite visualizar o nome de seu amigo secreto.

O administrador consegue acompanhar os acessos e verificar se os participantes já descobriram seus respectivos sorteados.

---

## 🛠️ Tecnologias utilizadas

- **PHP** — lógica da aplicação, sorteio, autenticação e processamento dos dados.
- **HTML5** — estrutura das páginas.
- **CSS3** — estilos e interface.
- **JavaScript** — funcionalidades de interação, como copiar links.
- **JSON** — armazenamento dos resultados do sorteio e controle de acessos.
- **Sessões PHP** — autenticação do painel administrativo.
- **Apache / .htaccess** — hospedagem e proteção de arquivos.
- **cURL** — utilizado na integração prevista para envio de mensagens via API.

Não utiliza banco de dados, framework ou dependências externas para seu funcionamento básico.

---

## 📁 Estrutura do projeto

```text
amigosecreto/
│
├── index.php
├── admin.php
├── sorteio.php
├── reveal.php
├── participantes.php
├── config.php
├── resultados.json
├── .htaccess
├── amigo_secreto.jpg
└── README.md
```

### `index.php`

Página inicial e autenticação do administrador.

Responsabilidades:

- Exibir o formulário de login.
- Validar a senha de acesso.
- Criar a sessão administrativa.
- Redirecionar o administrador para o painel.
- Encerrar a sessão através da opção de logout.

### `admin.php`

Painel administrativo do sistema.

Responsabilidades:

- Exibir os participantes cadastrados.
- Mostrar os resultados do sorteio.
- Exibir os links individuais.
- Permitir copiar os links.
- Informar a quantidade de visualizações.
- Mostrar a data e hora do último acesso.
- Permitir gerar um novo sorteio.

O acesso ao painel exige uma sessão autenticada.

### `participantes.php`

Arquivo responsável pelo cadastro dos participantes.

Os dados são armazenados em um array PHP, contendo:

- Nome do participante.
- Número de telefone.

Exemplo:

```php
return [
    [
        'nome' => 'Participante 1',
        'telefone' => '5547999999999'
    ],
    [
        'nome' => 'Participante 2',
        'telefone' => '5547888888888'
    ],
];
```

Os telefones devem ser informados com código do país, utilizando o padrão internacional.

Para números brasileiros, normalmente é utilizado o prefixo `55`.

### `config.php`

Arquivo de configuração geral da aplicação.

Centraliza informações como:

- URL de acesso ao sistema.
- URL da API de mensagens.
- Token de autenticação da API.

As configurações devem ser ajustadas de acordo com o ambiente de instalação.

### `sorteio.php`

Responsável pela lógica de sorteio.

O sistema utiliza embaralhamento aleatório dos participantes e valida o resultado conforme as regras configuradas.

As regras permitem:

- Impedir que um participante tire a si mesmo.
- Impedir determinados sorteios entre pessoas.
- Definir participantes que obrigatoriamente devem tirar uma pessoa específica.
- Definir restrições de quem pode ser sorteado por determinados participantes.
- Evitar participantes duplicados como destinatários.

O sistema tenta gerar um sorteio válido até um limite de tentativas configurado no código.

Após a validação, os resultados são gravados no arquivo `resultados.json`.

### `reveal.php`

Página responsável pela revelação individual do amigo secreto.

Cada participante recebe um link contendo uma chave única.

Exemplo:

```text
https://seusite.com/reveal.php?chave=TOKEN_INDIVIDUAL
```

Ao acessar o link:

1. A chave é validada em relação aos resultados armazenados.
2. O participante visualiza o nome de seu amigo secreto.
3. A quantidade de visualizações é incrementada.
4. A data e hora do último acesso são atualizadas.

O acesso ao link pode ser realizado mais de uma vez.

A chave funciona como um identificador privado de acesso. Não representa criptografia dos dados exibidos.

### `resultados.json`

Arquivo utilizado para armazenar os resultados do sorteio.

Cada registro contém informações como:

- Nome do participante.
- Telefone.
- Nome do amigo sorteado.
- Chave individual de acesso.
- Quantidade de visualizações.
- Data e hora do último acesso.

Exemplo de estrutura:

```json
[
    {
        "nome": "Participante 1",
        "telefone": "5547999999999",
        "amigo": "Participante 2",
        "hash": "token_individual",
        "view_count": 0,
        "last_viewed": null
    }
]
```

Esse arquivo é atualizado durante o sorteio e também quando um participante acessa seu link.

### `.htaccess`

Arquivo de configuração do Apache utilizado para impedir o acesso direto ao arquivo `resultados.json`.

Essa proteção evita que os resultados do sorteio sejam acessados diretamente pelo navegador através do arquivo JSON.

### `amigo_secreto.jpg`

Imagem utilizada na apresentação e no compartilhamento da página de revelação, incluindo metadados Open Graph.

---

## 🚀 Instalação

### Requisitos

- Servidor web Apache.
- PHP.
- Extensão PHP para manipulação de arquivos e JSON.
- Permissão de escrita no diretório do projeto para criação e atualização do arquivo `resultados.json`.

Para a integração com WhatsApp, também é necessário suporte ao cURL e uma API de mensagens devidamente configurada.

### 1. Clonar o repositório

```bash
git clone https://github.com/SEU-USUARIO/amigosecreto.git
```

Ou baixar o projeto e extrair os arquivos no diretório do servidor web.

### 2. Configurar os participantes

Edite o arquivo:

```text
participantes.php
```

Inclua todos os participantes do sorteio, informando nome e telefone (com 55 na frente, referente ao código DDI do Brasil)

### 3. Configurar o sistema

Edite o arquivo:

```text
config.php
```

Ajuste a URL de acesso para o endereço onde o sistema será hospedado.

Exemplo:

```php
'url_acesso' => 'https://seusite.com/amigosecreto/'
```

Caso utilize a integração de mensagens, configure também os parâmetros da API.

### 4. Configurar a senha administrativa

Edite o arquivo:

```text
index.php
```

Altere a variável de senha administrativa para uma senha de sua preferência.

```php
$senha_admin = 'SUA_SENHA_SEGURA';
```

Não utilize a senha padrão em ambiente de produção.

### 5. Permissões de arquivos

O servidor PHP precisa ter permissão para criar e atualizar o arquivo:

```text
resultados.json
```

Verifique as permissões do diretório de instalação conforme o usuário utilizado pelo Apache/PHP.

### 6. Acessar o sistema

Abra o endereço configurado no navegador.

Exemplo:

```text
https://seusite.com/amigosecreto/
```

Informe a senha administrativa para acessar o painel.

---

## 🎲 Como utilizar

### 1. Cadastro

Inclua os participantes no arquivo `participantes.php`.

### 2. Configuração de restrições

Caso existam regras específicas, configure-as diretamente no arquivo `sorteio.php`.

### 3. Realizar o sorteio

Acesse o painel administrativo e utilize a opção de gerar novo sorteio.

O sistema gera as combinações e armazena os resultados.

### 4. Distribuir os links

O painel apresenta um link individual para cada participante.

Utilize a opção de copiar link para compartilhar individualmente.

### 5. Revelação

Cada participante acessa seu link para descobrir quem tirou.

O acesso fica registrado no painel administrativo.

---

## 🔐 Privacidade e segurança

O sistema utiliza links individuais com tokens para permitir a revelação do resultado.

A segurança desse acesso depende da confidencialidade do link: qualquer pessoa que possua o link poderá visualizar o resultado correspondente.

Recomendações:

- Não compartilhe os links em grupos.
- Envie cada link individualmente.
- Utilize uma senha administrativa forte.
- Não publique os telefones reais dos participantes no repositório.
- Não publique tokens ou resultados reais.
- Mantenha o arquivo `resultados.json` protegido contra acesso externo.
- Utilize HTTPS na hospedagem.
- Não publique tokens de APIs ou credenciais no GitHub.

---

## 📲 Integração com WhatsApp

O projeto possui código relacionado ao envio de mensagens via API, utilizando cURL.

A principio foi implementado utilizando o serviço da UltraMsg (https://ultramsg.com/pt/)

A proposta é permitir o envio automático de uma mensagem individual contendo o link do sorteio.

A integração depende de:

- Serviço de API de WhatsApp.
- Token de autenticação.
- Endpoint configurado.
- Número de telefone válido.
- Configuração e validação da conta do serviço.

A integração deve ser revisada e configurada antes de ser utilizada em produção.

---

## 💾 Armazenamento

O sistema utiliza arquivos locais em vez de banco de dados.

| Arquivo | Finalidade |
|---|---|
| `participantes.php` | Cadastro dos participantes |
| `config.php` | Configurações da aplicação |
| `resultados.json` | Resultados e histórico de acessos |

Essa abordagem mantém o projeto simples e facilita sua instalação em hospedagens PHP tradicionais.

---

## 📌 Limitações

- Não utiliza banco de dados.
- Cadastro de participantes é realizado manualmente no arquivo PHP.
- Não possui gerenciamento de usuários administrativos.
- A autenticação utiliza uma senha definida no código.
- Os links individuais não possuem autenticação adicional.
- O controle de visualizações depende do arquivo JSON.
- O sistema depende das permissões de escrita do servidor.
- A integração com WhatsApp depende de serviço externo.

---

## 🗺️ Possíveis melhorias futuras

- Cadastro e edição de participantes pelo painel.
- Configuração de restrições pelo painel administrativo.
- Melhor tratamento de sorteios impossíveis.
- Proteção contra requisições CSRF.
- Armazenamento da senha utilizando hash.
- Envio de links por WhatsApp com integração configurável.
- Histórico de sorteios.
- Possibilidade de desfazer ou cancelar um sorteio.
- Melhorias na interface e experiência mobile.
- Migração para banco de dados, caso o projeto evolua.

---

## 📄 Licença

Projeto desenvolvido inicialmente para uso pessoal e familiar.

A utilização, modificação e distribuição do código podem ser definidas conforme a licença escolhida pelo proprietário do repositório.

---

**Desenvolvido para tornar o amigo secreto mais simples, divertido e organizado. 🎅🎁**
