    create database first_data CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

    use first_data;

    CREATE TABLE usuarios (
    ID INT AUTO_INCREMENT UNIQUE PRIMARY KEY,
    CPF VARCHAR(200) NOT NULL,
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
    quantidade_pecas_setor INT NOT NULL,
    quantidade_pecas_no_dia INT NOT NULL,
    setor VARCHAR (200) NOT NULL,
    entrada_peca TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    item_inspe TEXT
    );

    CREATE TABLE tipo_peca (
    nome_tipo VARCHAR(200) NOT NULL PRIMARY KEY,
    id_pecas INT REFERENCES pecas(id_pecas),
    ima_peca BLOB NOT NULL
    );
 
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
    values('João Silva', '111.111.111-11','joao@empresa.com', 'Motor e Transmissão', '47', 'Ativo'),
    ('Maria Santos', '222.222.222-22','maria@empresa.com', 'Freios', '35', 'Ativo'),
    ('Carlos Ramos', '333.333.333-33','carlos@empresa.com', 'Suspensão  e Direção', '52', 'Ativo'),
    ('Ana Gabriela', '444.444.444-44','ana@empresa.com', 'Elétrica', '28', 'Inativo'),
    ('Pedro Pimenteira', '555.555.555-55','pedro@empresa.com', 'Componentes de Segurança', '61', 'Ativo');

    create table pecas_por_funcionario(
    nome_fun VARCHAR(200) NOT NULL,
    setor VARCHAR(200) NOT NUll,
    quantidade_pecas_no_dia INT NOT NULL
    );
    
    create table examinar_pecas(
    pecas_aprovadas INT NOT NULL,
    pecas_rejeitadas INT NOT NULL,
    pecas_examinadas INT NOT NULL,
    quantidade_pecas INT NOT NULL,
    quantidade_pecas_setor INT NOT NULL,
    quantidade_pecas_no_dia INT NOT NULL
    );


create table instrucao(
	id integer auto_increment unique primary key,
    instrucao varchar(200) not null,
    id_peca varchar(200) not null
);

insert into instrucao(instrucao, id_peca)
values('Inspecionar canaletas dos anéis de segmento.', '1'),
('Checar alinhamento do pino de pistão.', '1'),
('Verificar ausência de riscos na saia do pistão.', '1'),
('Verificar espessura do material de atrito.', '2'),
('Inspecionar trincas ou trincamentos na superfície.', '2'),
('Conferir assentamento das placas antirruído.', '2'),
('Verificar desgaste uniforme das sapatas.', '2'),
("Verificar curso e velocidade de retorno da haste.",'3'),
("Checar vazamento de fluido hidráulico.", "3"),
("Conferir estado e desgaste das buchas de fixação.", "3"),
("Inspecionar alinhamento da coifa e do batente.", "3"),
("Verificar alinhamento e fixação das presilhas.", "4"),
("Checar pintura, tonalidade e acabamento externo.", "4"),
("Conferir absorvedor de impacto interno.", "4"),
("Verificar encaixe das grades e faróis de milha.", "4");

insert into instrucao(instrucao, id_peca)
values("Verificar carga e tensão da bateria.", "5"),
("Conferir nível do eletrólito nas células.", "5"),
("Checar vedação e ausência de corrosão nos bornes.", "5"),
("Testar corrente de partida a frio (CCA).", "5");
