# EduGestão — Sistema de Gerenciamento de Alunos

## Sobre o projeto

O EduGestão é um sistema web desenvolvido para facilitar o gerenciamento de alunos. A aplicação permite cadastrar, consultar, editar e excluir registros, com acesso individual para cada usuário.

O projeto foi desenvolvido como atividade acadêmica, utilizando tecnologias de desenvolvimento web e banco de dados.

## Tecnologias utilizadas

- PHP
- HTML5
- CSS3
- MySQL
- Visual Studio Code

## Funcionalidades

- Cadastro de usuários.
- Login com verificação em duas etapas (MFA).
- Painel inicial com indicadores de alunos.
- Cadastro de alunos.
- Listagem de alunos.
- Edição de cadastros.
- Exclusão de alunos.
- Separação dos registros conforme o usuário autenticado.
- Encerramento de sessão.

## Banco de dados

O sistema utiliza o MySQL e possui duas tabelas principais:

- `usuarios`: armazena os dados de acesso dos usuários.
- `alunos`: armazena os dados dos alunos e identifica o usuário responsável por cada cadastro.

## Como executar o projeto

### 1. Preparar o ambiente

Instale e configure:

- MySQL Server.
- PHP com as extensões `mysqli` e `mysqlnd`.
- Um editor de código, como o Visual Studio Code.

### 2. Configurar o banco de dados

Crie o banco de dados `sistema_alunos` no MySQL e importe a estrutura das tabelas necessárias.

### 3. Configurar a conexão

Copie o arquivo `conexao.example.php`, renomeie a cópia para `conexao.php` e informe as credenciais locais do MySQL.

### 4. Iniciar o servidor PHP

Abra o terminal na pasta do projeto e execute:

```bash
php -S localhost:8000
```

### 5. Acessar o sistema

Abra o navegador e acesse:

http://localhost:8000/login.php

## Observações

A verificação em duas etapas (MFA) é implementada por meio de um código temporário armazenado no banco de dados. Nesta versão acadêmica, o código não é enviado por e-mail ou SMS.

As credenciais locais do banco de dados não devem ser compartilhadas ou publicadas no repositório.

## Projeto acadêmico

Desenvolvido como projeto acadêmico do curso de Sistemas de Informação.