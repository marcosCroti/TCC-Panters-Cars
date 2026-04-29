    create database first_data CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

    use first_data;

    CREATE TABLE usuarios (
    CPF INT PRIMARY KEY,
    usuario_nome VARCHAR(50) NOT NULL,
    email VARCHAR(200) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    isAdmin boolean,
    telefone INT NOT NULL
    );

    CREATE TABLE pecas (
    ima_peca BLOB NOT NULL,
    nome_tipo VARCHAR(200) NOT NULL,
    grupo_peca VARCHAR(200) NOT NULL,
    id_pecas INT AUTO_INCREMENT UNIQUE PRIMARY KEY,
    quantidade_pecas INT NOT NULL,
    quantidade_pecas_no_dia INT NOT NULL,
    local_peca VARCHAR (200) NOT NULL,
    entrada_peca TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE tipo_peca (
    nome_tipo VARCHAR(200) NOT NULL PRIMARY KEY,
    id_pecas INT REFERENCES pecas(id_pecas),
    ima_peca BLOB NOT NULL
    );

    select * from first_data.pecas;

    insert into pecas(ima_peca, nome_tipo, grupo_peca, id_pecas, quantidade_pecas, quantidade_pecas_no_dia, local_peca)
    values('img1', 'Pistão','Motor e Transmissão','00000001', 0, 0, 'Setor 1'),
    ('img2', 'Pastilhas', 'Freios', '00000002', 0, 0, 'Setor 1'), 
    ('img3', 'Amortecedores', 'Suspensão e Direção', '00000003', 0, 0, 'Setor 2'), 
    ('img4', 'Bateria', 'Elétrica', '00000004', 0, 0, 'Setor 2'), 
    ('img5','Para-choques', 'Carroceria/Acabamento', '00000005', 0, 0, 'Setor 3'),
    ('img6', 'Cinto de Segurança', 'Componentes de Segurança', '00000006', 0, 0, 'Setor 3');
    
    select * FROM pecas;