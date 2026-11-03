-- schema

create database SchoolHelpDesk;

use SchoolHelpDesk;

create table if not exists usuario (
id int auto_increment primary key,
nome varchar(100),
email varchar(100),
senha varchar(100),
nivel ENUM('tecnico','administrador') default 'tecnico'
);

create table if not exists cliente (
id int auto_increment primary key,
nome varchar(100),
email varchar(100),
senha varchar(100)
);

create table if not exists ambiente (
id int auto_increment primary key,
nome varchar(100),
codigo varchar(10)
);

create table if not exists chamado (
id int auto_increment primary key,
titulo varchar(100),
descricao varchar (200),
data_chamado DATE,
prioridade ENUM('baixa', 'media','alta', 'urgente') default 'media',
status_chamado ENUM('em andamento', 'aguardando', 'concluido', 'cancelado') default 'aguardando', 
id_usuario int, 
id_cliente int,
id_ambiente int,
FOREIGN KEY (id_usuario) REFERENCES usuario(id),
FOREIGN KEY (id_cliente) REFERENCES cliente(id),
FOREIGN KEY (id_ambiente) REFERENCES ambiente(id)
);
