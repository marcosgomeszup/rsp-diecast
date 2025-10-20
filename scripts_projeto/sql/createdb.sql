-- --------------------------------------------------
-- Banco de Dados: RSP Diecast / Coleção Racing
-- Autor: Equipe RSP Diecast
-- Versão: 1.3
-- Data: 2025-10-17
-- --------------------------------------------------

-- Cria o banco de dados
CREATE DATABASE IF NOT EXISTS rspdiecast CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rspdiecast;

-- --------------------------------------------------
-- Tabela: users
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  senha VARCHAR(255) NULL,
  tipo_login ENUM('local', 'google') DEFAULT 'local',
  foto_perfil VARCHAR(255) NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Seed de usuários iniciais
INSERT INTO users (nome, email, senha, tipo_login)
VALUES
('Administrador Local', 'admin@colecaoracing.com', SHA2('Admin@123', 256), 'local'),
('Rodrigo', 'rodrigo@gmail.com', NULL, 'google'),
('Marcos', 'marcos@gmail.com', NULL, 'google');

-- --------------------------------------------------
-- Tabela: cars (carros e miniaturas)
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS cars (
  id INT AUTO_INCREMENT PRIMARY KEY,
  categoria VARCHAR(50) NOT NULL,
  modelo VARCHAR(100) NOT NULL,
  ano INT NOT NULL,
  equipe VARCHAR(100) NOT NULL,
  piloto VARCHAR(100) NOT NULL,
  marca_miniatura VARCHAR(100) NULL,
  fabricante VARCHAR(100) NULL,
  codigo_miniatura VARCHAR(100) NULL,
  criado_por INT NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (criado_por) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- --------------------------------------------------
-- Tabela: car_images (galeria de imagens)
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS car_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  car_id INT NOT NULL,
  imagem_path VARCHAR(255) NOT NULL,
  legenda VARCHAR(255) NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- --------------------------------------------------
-- Tabela: logs (registro de operações do sistema)
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  acao VARCHAR(255) NOT NULL,
  tabela_afetada VARCHAR(100) NULL,
  data_evento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- --------------------------------------------------
-- VIEW: vw_cars_detalhes (visualização completa de carros)
-- --------------------------------------------------
CREATE OR REPLACE VIEW vw_cars_detalhes AS
SELECT
  c.id,
  c.modelo,
  c.categoria,
  c.ano,
  c.equipe,
  c.piloto,
  c.marca_miniatura,
  c.fabricante,
  c.codigo_miniatura,
  u.nome AS criado_por,
  COUNT(ci.id) AS total_imagens,
  MAX(ci.imagem_path) AS ultima_imagem,
  c.criado_em
FROM cars c
LEFT JOIN users u ON c.criado_por = u.id
LEFT JOIN car_images ci ON ci.car_id = c.id
GROUP BY c.id;

-- --------------------------------------------------
-- Tabela: relatorios_export (registro de exportações)
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS relatorios_export (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT,
  tipo_arquivo ENUM('csv', 'xlsx') NOT NULL,
  data_export TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- --------------------------------------------------
-- Finalização
-- --------------------------------------------------
SHOW TABLES;
