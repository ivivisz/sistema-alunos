-- Banco de dados do sistema EduGestão
CREATE DATABASE IF NOT EXISTS sistema_alunos
CHARACTER SET utf8mb4
COLLATE utf8mb4_0900_ai_ci;

USE sistema_alunos;

-- Tabela de usuários do sistema
CREATE TABLE usuarios (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    codigo_mfa VARCHAR(6) DEFAULT NULL,
    expira_mfa DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_usuarios_email (email)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;

-- Tabela de alunos vinculada aos usuários
CREATE TABLE alunos (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telefone VARCHAR(30) DEFAULT NULL,
    data_nascimento DATE DEFAULT NULL,
    turma VARCHAR(40) NOT NULL,
    usuario_id INT NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_usuario_email (usuario_id, email),
    CONSTRAINT fk_alunos_usuarios
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios (id)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;
