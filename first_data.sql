    create database first_data CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

    use first_data;

    CREATE TABLE usuarios (
    CPF VARCHAR(200) VARCHAR PRIMARY KEY,
    usuario_nome VARCHAR(50) NOT NULL,
    email VARCHAR(200) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    isAdmin boolean,
    usuario_funcionario VARCHAR(200) NOT NULL,
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

    insert into pecas(ima_peca, nome_tipo, grupo_peca, id_pecas, quantidade_pecas, quantidade_pecas_no_dia, local_peca)
    values('img1', 'Pistão','Motor e Transmissão','00000001', 0, 0, 'Setor 1'),
    ('img2', 'Pastilhas', 'Freios', '00000002', 0, 0, 'Setor 1'), 
    ('img3', 'Amortecedores', 'Suspensão e Direção', '00000003', 0, 0, 'Setor 2'), 
    ('img4', 'Bateria', 'Elétrica', '00000004', 0, 0, 'Setor 2'), 
    ('img5','Para-choques', 'Carroceria/Acabamento', '00000005', 0, 0, 'Setor 3'),
    ('img6', 'Cinto de Segurança', 'Componentes de Segurança', '00000006', 0, 0, 'Setor 3');

    select * from first_data.pecas;


    
    insert into pecas(ima_peca, nome_tipo, grupo_peca, id_pecas, quantidade_pecas, quantidade_pecas_no_dia, local_peca)
    values('img1', 'Pistão','Motor e Transmissão','00000001', 0, 0, 'Setor 1'),
    ('img2', 'Pastilhas', 'Freios', '00000002', 0, 0, 'Setor 1'), 
    ('img3', 'Amortecedores', 'Suspensão e Direção', '00000003', 0, 0, 'Setor 2'), 
    ('img4', 'Bateria', 'Elétrica', '00000004', 0, 0, 'Setor 2'), 
    ('img5','Para-choques', 'Carroceria/Acabamento', '00000005', 0, 0, 'Setor 3'),
    ('img6', 'Cinto de Segurança', 'Componentes de Segurança', '00000006', 0, 0, 'Setor 3');
    
 
    select * FROM pecas;
    
    create table funcionario (
    nome_fun VARCHAR(200) NOT NULL,
    ID INT AUTO_INCREMENT UNIQUE PRIMARY KEY,
    CPF VARCHAR(200) NOT NULL,
    email VARCHAR(200) NOT NULL,
    setor VARCHAR(200) NOT NULL,
    quantidade_pecas_no_dia INT NOT NULL,
    status_fun VARCHAR(200) NOT NULL
    
    );
 
 
    insert into funcionario(nome_fun, CPF, email, setor, quantidade_pecas_no_dia, status_fun)
    values('João Silva', '111.111.111-11','joao@empresa.com', 'Montagem', '47', 'Ativo'),
    ('Maria Santos', '222.222.222-22','maria@empresa.com', 'Qualidade', '35', 'Ativo'),
    ('Carlos Ramos', '333.333.333-33','carlos@empresa.com', 'Expedição', '52', 'Ativo'),
    ('Ana Gabriela', '444.444.444-44','ana@empresa.com', 'Inspeção', '28', 'Inativo'),
    ('Pedro Pimenteira', '555.555.555-55','pedro@empresa.com', 'Montagem', '61', 'Ativo');