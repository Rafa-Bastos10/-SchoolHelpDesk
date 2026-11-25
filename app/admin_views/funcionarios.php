<section class="titulo">
    <div>
        <h1 class="saudacao">Funcionários</h1>
        <p class="subtitulo">Cadastre novos técnicos ou administradores e gerencie os acessos</p>
    </div>
</section>

<section class="cartao-borda">
    <div class="cabecalho-bloco">
        <h2>Cadastrar funcionário</h2>
        <p>Preencha os dados para criar um novo acesso ao sistema.</p>
    </div>

    <form method="POST" action="admin.php?pagina=funcionarios">
        <input type="hidden" name="acao" value="cadastrar_funcionario">

        <div class="grade-form">
            <div class="campo">
                <label for="nome">Nome completo</label>
                <input type="text" id="nome" name="nome" placeholder="Digite o nome" required>
            </div>

            <div class="campo">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="Digite o e-mail" required>
            </div>

            <div class="campo">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="Mínimo 6 caracteres" minlength="6" required>
            </div>

            <div class="campo">
                <label for="nivel">Nível de acesso</label>
                <select id="nivel" name="nivel">
                    <option value="tecnico">Técnico</option>
                    <option value="administrador">Administrador</option>
                </select>
            </div>
        </div>

        <div class="rodape-form">
            <button type="submit" class="botao">+ Cadastrar funcionário</button>
        </div>
    </form>
</section>

<section class="cartao-tabela">
    <div class="cabecalho-cartao">
        <h2>Funcionários cadastrados (<?= count($funcionarios) ?>)</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>NOME</th>
                <th>E-MAIL</th>
                <th>CARGO</th>
                <th>AÇÃO</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($funcionarios) > 0): ?>
                <?php foreach ($funcionarios as $func): ?>
                    <?php $isAdmin = ($func['nivel'] === 'administrador'); ?>
                    <tr>
                        <td class="codigo">#<?= str_pad((string) $func['id'], 4, '0', STR_PAD_LEFT) ?></td>
                        <td class="col-titulo"><?= htmlspecialchars($func['nome']) ?></td>
                        <td><?= htmlspecialchars($func['email']) ?></td>
                        <td>
                            <span class="status <?= $isAdmin ? 'aberto' : 'andamento' ?>">
                                <?= $isAdmin ? 'Administrador' : 'Técnico' ?>
                            </span>
                        </td>
                        <td>
                            <?php if ((int) $func['id'] !== (int) $_SESSION['id_usuario']): ?>
                                <form method="POST" action="admin.php?pagina=funcionarios" onsubmit="return confirm('Deseja excluir este cadastro?')">
                                    <input type="hidden" name="acao" value="excluir_funcionario">
                                    <input type="hidden" name="id_funcionario" value="<?= $func['id'] ?>">
                                    <button type="submit" class="botao-excluir">Excluir</button>
                                </form>
                            <?php else: ?>
                                <span class="tag-voce">Conta atual</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="vazio">Nenhum funcionário cadastrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>