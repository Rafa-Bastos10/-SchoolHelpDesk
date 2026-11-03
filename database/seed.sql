-- =====================================================
-- SEED - SchoolHelpDesk
-- Dados ficticios para testes
-- Execute depois de criar as tabelas e a VIEW.
-- =====================================================

USE SchoolHelpDesk;

-- 1. USUARIOS (10 registros)
INSERT INTO usuario (nome, email, senha, nivel) VALUES
('Ana Souza', 'ana.souza@schoolhelpdesk.test', 'senha123', 'administrador'),
('Carlos Lima', 'carlos.lima@schoolhelpdesk.test', 'senha123', 'tecnico'),
('Mariana Costa', 'mariana.costa@schoolhelpdesk.test', 'senha123', 'tecnico'),
('Pedro Alves', 'pedro.alves@schoolhelpdesk.test', 'senha123', 'tecnico'),
('Juliana Rocha', 'juliana.rocha@schoolhelpdesk.test', 'senha123', 'tecnico'),
('Rafael Mendes', 'rafael.mendes@schoolhelpdesk.test', 'senha123', 'tecnico'),
('Beatriz Oliveira', 'beatriz.oliveira@schoolhelpdesk.test', 'senha123', 'administrador'),
('Lucas Ferreira', 'lucas.ferreira@schoolhelpdesk.test', 'senha123', 'tecnico'),
('Camila Ribeiro', 'camila.ribeiro@schoolhelpdesk.test', 'senha123', 'tecnico'),
('Bruno Martins', 'bruno.martins@schoolhelpdesk.test', 'senha123', 'tecnico');

-- 2. CLIENTES (10 registros)
INSERT INTO cliente (nome, email, senha) VALUES
('Joao Pedro', 'joao.pedro@aluno.schoolhelpdesk.test', 'cliente123'),
('Maria Eduarda', 'maria.eduarda@aluno.schoolhelpdesk.test', 'cliente123'),
('Gabriel Santos', 'gabriel.santos@aluno.schoolhelpdesk.test', 'cliente123'),
('Laura Martins', 'laura.martins@aluno.schoolhelpdesk.test', 'cliente123'),
('Matheus Oliveira', 'matheus.oliveira@aluno.schoolhelpdesk.test', 'cliente123'),
('Isabela Costa', 'isabela.costa@aluno.schoolhelpdesk.test', 'cliente123'),
('Enzo Almeida', 'enzo.almeida@aluno.schoolhelpdesk.test', 'cliente123'),
('Sofia Rodrigues', 'sofia.rodrigues@aluno.schoolhelpdesk.test', 'cliente123'),
('Thiago Pereira', 'thiago.pereira@aluno.schoolhelpdesk.test', 'cliente123'),
('Valentina Gomes', 'valentina.gomes@aluno.schoolhelpdesk.test', 'cliente123');

-- 3. AMBIENTES (10 registros)
INSERT INTO ambiente (nome, codigo) VALUES
('Laboratorio de Informatica 1', 'LAB01'),
('Laboratorio de Informatica 2', 'LAB02'),
('Sala de Aula 1', 'SALA01'),
('Sala de Aula 2', 'SALA02'),
('Biblioteca', 'BIB01'),
('Secretaria', 'SEC01'),
('Sala dos Professores', 'PROF01'),
('Auditorio', 'AUD01'),
('Laboratorio de Redes', 'RED01'),
('Sala de Reunioes', 'REU01');

-- 4. CHAMADOS (10 registros)
-- Os IDs abaixo correspondem aos registros inseridos acima,
-- considerando tabelas inicialmente vazias e AUTO_INCREMENT iniciando em 1.
INSERT INTO chamado
(titulo, descricao, data_chamado, prioridade, status_chamado,
 id_usuario, id_cliente, id_ambiente)
VALUES
('Computador nao liga', 'O computador da bancada nao liga ao pressionar o botao.', '2026-10-20', 'alta', 'aguardando', 2, 1, 1),
('Problema com internet', 'A conexao com a internet esta instavel durante as aulas.', '2026-10-21', 'media', 'em andamento', 3, 2, 2),
('Projetor sem imagem', 'O projetor liga, mas nao exibe a imagem do computador.', '2026-10-22', 'media', 'concluido', 4, 3, 3),
('Teclado com defeito', 'Algumas teclas do teclado nao estao funcionando.', '2026-10-23', 'baixa', 'aguardando', 5, 4, 1),
('Acesso ao sistema', 'O usuario nao consegue acessar o sistema academico.', '2026-10-24', 'alta', 'em andamento', 6, 5, 6),
('Impressora nao imprime', 'A impressora apresenta erro ao enviar documentos.', '2026-10-25', 'media', 'concluido', 8, 6, 6),
('Falha na rede local', 'Os computadores do laboratorio nao conseguem se comunicar.', '2026-10-26', 'urgente', 'em andamento', 9, 7, 9),
('Instalacao de software', 'Solicitacao de instalacao de um programa para atividade.', '2026-10-27', 'baixa', 'aguardando', 10, 8, 2),
('Monitor piscando', 'O monitor apresenta oscilacao na imagem durante o uso.', '2026-10-28', 'media', 'cancelado', 2, 9, 4),
('Configuracao de audio', 'O audio do computador do auditorio nao esta funcionando.', '2026-10-29', 'alta', 'aguardando', 3, 10, 8);

-- Conferencia: cada tabela deve possuir pelo menos 10 registros de seed.
SELECT 'usuario' AS tabela, COUNT(*) AS quantidade FROM usuario
UNION ALL
SELECT 'cliente', COUNT(*) FROM cliente
UNION ALL
SELECT 'ambiente', COUNT(*) FROM ambiente
UNION ALL
SELECT 'chamado', COUNT(*) FROM chamado;

-- Teste da VIEW criada anteriormente:
SELECT * FROM vw_chamados;
