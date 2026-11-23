<section class="titulo">
    <div>
        <h1 class="saudacao">Funcionários</h1>
        <p class="subtitulo"><?= $ehRoot ? 'Cadastre técnicos e administradores' : 'Cadastre técnicos' ?></p>
    </div>
</section>

<section class="duas-colunas">
    <div class="tabela-box">
        <h2 class="tabela-titulo">Novo funcionário</h2>
        <form method="POST" action="admin.php" class="formulario">
            <input type="hidden" name="acao" value="cadastrar_funcionario">

            <label for="nome">Nome completo</label>
            <input type="text" id="nome" name="nome" required>

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required>

            <label for="cargo">Cargo</label>
            <select id="cargo" name="cargo" required>
                <option value="">Selecione</option>
                <?= opcoes($cargosPermitidos) ?>
            </select>

            <label for="senha">Senha (mínimo 6 caracteres)</label>
            <input type="password" id="senha" name="senha" minlength="6" required>

            <button type="submit" class="botao">Cadastrar</button>
        </form>
    </div>

    <div class="tabela-box">
        <h2 class="tabela-titulo">Cadastrados</h2>
        <table>
            <thead><tr class="cabecalho"><th>Nome</th><th>E-mail</th><th>Cargo</th></tr></thead>
            <tbody>
                <?php foreach ($funcionarios as $f): ?>
                    <tr>
                        <td><?= h($f['nome']) ?></td>
                        <td><?= h($f['email']) ?></td>
                        <td><span class="status <?= $f['nivel'] === 'administrador' ? 'aberto' : 'concluido' ?>"><?= h(CARGOS[$f['nivel']] ?? $f['nivel']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
