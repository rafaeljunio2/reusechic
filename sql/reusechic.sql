-- ReuseChic - Banco de Dados MySQL
-- Importe este arquivo no phpMyAdmin ou via: mysql -u root -p < reusechic.sql

CREATE DATABASE IF NOT EXISTS reusechic CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE reusechic;

-- Usuários (clientes)
CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  contato VARCHAR(30),
  senha VARCHAR(255) NOT NULL,
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Administradores
CREATE TABLE IF NOT EXISTS administradores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  contato VARCHAR(30),
  senha VARCHAR(255) NOT NULL,
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Categorias
CREATE TABLE IF NOT EXISTS categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL UNIQUE,
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Produtos
CREATE TABLE IF NOT EXISTS produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(200) NOT NULL,
  descricao TEXT,
  preco DECIMAL(10,2) NOT NULL,
  tamanho VARCHAR(20),
  cor VARCHAR(50),
  estado VARCHAR(50) DEFAULT 'Ótima',
  estoque INT DEFAULT 1,
  status ENUM('disponivel','vendido','indisponivel') DEFAULT 'disponivel',
  categoria_id INT,
  imagem_principal VARCHAR(255),
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
);

-- Imagens dos produtos (galeria)
CREATE TABLE IF NOT EXISTS imagens_produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  produto_id INT NOT NULL,
  caminho VARCHAR(255) NOT NULL,
  FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
);

-- Carrinho
CREATE TABLE IF NOT EXISTS carrinho (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT,
  produto_id INT NOT NULL,
  quantidade INT DEFAULT 1,
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
);

-- Pedidos (registro das finalizações via WhatsApp)
CREATE TABLE IF NOT EXISTS pedidos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT,
  total DECIMAL(10,2) NOT NULL,
  itens TEXT NOT NULL,
  status VARCHAR(30) DEFAULT 'pendente',
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- Configurações do site (personalização dinâmica)
CREATE TABLE IF NOT EXISTS configuracoes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  chave VARCHAR(50) NOT NULL UNIQUE,
  valor TEXT
);

-- Banners / Carrossel
CREATE TABLE IF NOT EXISTS banners (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(150),
  subtitulo VARCHAR(200),
  imagem VARCHAR(255) NOT NULL,
  link VARCHAR(255),
  ativo TINYINT(1) DEFAULT 1,
  ordem INT DEFAULT 0
);

-- Dados iniciais
INSERT INTO configuracoes (chave, valor) VALUES
  ('nome_site', 'Reuse Chic'),
  ('subtitulo', 'Brechó Sustentável'),
  ('logo', 'assets/logo.png'),
  ('cor_primaria', '#c98b7a'),
  ('cor_secundaria', '#f6dcd5'),
  ('whatsapp', '5561999999999'),
  ('email_contato', 'reusechic@email.com'),
  ('endereco', 'Área Especial para Indústria Lote 02, Setor Leste, Gama, Brasília-DF'),
  ('titulo_home', '50% off'),
  ('subtitulo_home', 'Exclusivo: 09 a 24 de Abril')
ON DUPLICATE KEY UPDATE valor = VALUES(valor);

INSERT INTO categorias (nome) VALUES ('Blusas'), ('Vestidos'), ('Calças e Bermudas'), ('Acessórios')
ON DUPLICATE KEY UPDATE nome = VALUES(nome);

-- Admin padrão (senha: admin123)
INSERT INTO administradores (nome, email, contato, senha) VALUES
  ('Administrador', 'admin@reusechic.com', '5561999999999',
   '$2y$10$e0NRZ6QbZ6qkXkJYHvL0QexJ8YGz5yGZJxV7XKqDqL5Y3qX8YgK5e')
ON DUPLICATE KEY UPDATE email = VALUES(email);
-- Gere uma nova senha em PHP: password_hash('admin123', PASSWORD_DEFAULT)
