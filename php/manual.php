<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}

$basePath   = '../';
$activePage = 'manual';
$pageTitle  = 'Manual do Usuário';
?>
<!DOCTYPE html>
<html lang="pt-br">
<?php include 'includes/head.php'; ?>
<style>
.manual-section      { scroll-margin-top: 80px; }
.manual-step         { display:flex; gap:16px; align-items:flex-start; margin-bottom:20px; }
.manual-step-num     { min-width:36px; height:36px; border-radius:50%; background:#1F6FBF;
                        color:#fff; font-weight:700; display:flex; align-items:center;
                        justify-content:center; flex-shrink:0; font-size:.95rem; }
.manual-toc          { position:sticky; top:80px; }
.manual-toc a        { display:block; padding:4px 8px; border-radius:6px; color:#555;
                        text-decoration:none; font-size:.88rem; }
.manual-toc a:hover,
.manual-toc a.active { background:#e8f0fe; color:#1F6FBF; font-weight:600; }
.manual-toc .toc-section { font-weight:700; color:#333; font-size:.9rem;
                             padding:6px 8px 2px; margin-top:8px; }
.tip-box             { background:#e8f8f5; border-left:4px solid #27ae60;
                        padding:10px 14px; border-radius:0 8px 8px 0; font-size:.9rem; }
.warn-box            { background:#fef9e7; border-left:4px solid #f39c12;
                        padding:10px 14px; border-radius:0 8px 8px 0; font-size:.9rem; }
.screen-card         { border:2px solid #dee2e6; border-radius:10px; padding:16px;
                        background:#f8f9fa; }
</style>
<body>
<div class="wrapper">
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-panel">
        <?php include 'includes/navbar.php'; ?>
        <div class="container">
            <div class="page-inner">
                <ul class="breadcrumbs mb-3 pt-2">
                    <li class="nav-home"><a href="../principal.php"><i class="icon-home"></i></a></li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="#">Manual do Usuário</a></li>
                </ul>

                <div class="row">
                    <!-- Sumário lateral -->
                    <div class="col-md-3 d-none d-md-block">
                        <div class="card card-round manual-toc p-3">
                            <div class="fw-bold mb-2" style="font-size:.95rem;">
                                <i class="fas fa-list me-1 text-primary"></i> Sumário
                            </div>
                            <div class="toc-section">🏠 Início</div>
                            <a href="#sec-login">Login</a>
                            <a href="#sec-dashboard">Dashboard</a>
                            <div class="toc-section">📚 Acervo</div>
                            <a href="#sec-livros">Buscar Livros</a>
                            <a href="#sec-cadastrar-livro">Cadastrar Livro</a>
                            <a href="#sec-editar-livro">Editar / Excluir Livro</a>
                            <a href="#sec-import-livros">Importar Livros em Lote</a>
                            <div class="toc-section">🎓 Turmas e Alunos</div>
                            <a href="#sec-turmas">Gerenciar Turmas</a>
                            <a href="#sec-alunos">Gerenciar Alunos</a>
                            <a href="#sec-import-turmas">Importar Turmas em Lote</a>
                            <a href="#sec-import-alunos">Importar Alunos em Lote</a>
                            <div class="toc-section">📖 Empréstimos</div>
                            <a href="#sec-novo-emprestimo">Registrar Empréstimo</a>
                            <a href="#sec-devolucao">Devolver Livro</a>
                            <a href="#sec-atraso">Empréstimos em Atraso</a>
                            <div class="toc-section">🏆 Relatórios</div>
                            <a href="#sec-ranking">Ranking de Leitura</a>
                        </div>
                    </div>

                    <!-- Conteúdo -->
                    <div class="col-md-9">

                        <!-- LOGIN -->
                        <div class="card card-round mb-4 manual-section" id="sec-login">
                            <div class="card-header" style="background:#1F6FBF;">
                                <div class="card-title text-white">
                                    <i class="fas fa-sign-in-alt me-2"></i>1. Login no Sistema
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="manual-step">
                                    <div class="manual-step-num">1</div>
                                    <div>Abra o navegador e acesse o endereço do sistema:
                                        <code>http://localhost/multimeios</code></div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">2</div>
                                    <div>Na tela de login, informe seu <strong>usuário</strong> e <strong>senha</strong> fornecidos pelo administrador.</div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">3</div>
                                    <div>Clique em <strong>Entrar</strong>. Você será redirecionado para o painel principal.</div>
                                </div>
                                <div class="warn-box mt-2">
                                    ⚠️ Se aparecer a mensagem "Usuário ou senha incorretos", verifique se o CAPS LOCK está desativado e tente novamente.
                                </div>
                            </div>
                        </div>

                        <!-- DASHBOARD -->
                        <div class="card card-round mb-4 manual-section" id="sec-dashboard">
                            <div class="card-header" style="background:#1F6FBF;">
                                <div class="card-title text-white">
                                    <i class="fas fa-home me-2"></i>2. Dashboard (Página Inicial)
                                </div>
                            </div>
                            <div class="card-body">
                                <p>A página inicial exibe um resumo completo do sistema:</p>
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <div class="screen-card">
                                            <strong>Cards de resumo</strong>
                                            <ul class="mb-0 mt-1 small">
                                                <li>Total de livros no acervo</li>
                                                <li>Alunos ativos</li>
                                                <li>Livros atualmente emprestados</li>
                                                <li>Devolvidos no mês</li>
                                                <li>Em atraso</li>
                                                <li>Turmas ativas</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="screen-card">
                                            <strong>Gráficos e listas</strong>
                                            <ul class="mb-0 mt-1 small">
                                                <li>Gráfico de empréstimos por mês</li>
                                                <li>Taxa de devolução</li>
                                                <li>Top 5 livros mais emprestados</li>
                                                <li>Top 5 leitores do mês</li>
                                                <li>Empréstimos recentes</li>
                                                <li>Tabela de atrasos (quando houver)</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="tip-box">
                                    ✅ O sistema atualiza automaticamente os empréstimos atrasados ao abrir esta página. Não é necessário fazer nenhuma ação manual.
                                </div>
                            </div>
                        </div>

                        <!-- LIVROS -->
                        <div class="card card-round mb-4 manual-section" id="sec-livros">
                            <div class="card-header" style="background:#1F6FBF;">
                                <div class="card-title text-white">
                                    <i class="fas fa-book me-2"></i>3. Livros
                                </div>
                            </div>
                            <div class="card-body">
                                <h6 id="sec-buscar-livro" class="manual-section fw-bold">3.1 Buscar Livros</h6>
                                <div class="manual-step">
                                    <div class="manual-step-num">1</div>
                                    <div>Clique em <strong>Livros</strong> no menu lateral.</div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">2</div>
                                    <div>Preencha um ou mais campos de pesquisa (título, autor, editora, etc.) e clique em <strong>Buscar</strong>.</div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">3</div>
                                    <div>Os resultados aparecem em lista. Cada livro tem botões de <strong>Editar</strong> e <strong>Excluir</strong>.</div>
                                </div>
                                <div class="tip-box mb-3">
                                    💡 Deixe todos os campos em branco e clique em Buscar para listar <strong>todos os livros</strong> do acervo.
                                </div>

                                <h6 id="sec-cadastrar-livro" class="manual-section fw-bold mt-3">3.2 Cadastrar Livro</h6>
                                <div class="manual-step">
                                    <div class="manual-step-num">1</div>
                                    <div>Na página de Livros, clique em <strong>Adicionar Livro</strong> (botão azul no topo direito).</div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">2</div>
                                    <div>Preencha pelo menos o <strong>Título</strong> e o <strong>Autor</strong> (obrigatórios). Os demais campos são opcionais.</div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">3</div>
                                    <div>Clique em <strong>Cadastrar</strong>. O livro é adicionado ao acervo imediatamente.</div>
                                </div>

                                <h6 id="sec-editar-livro" class="manual-section fw-bold mt-3">3.3 Editar ou Excluir Livro</h6>
                                <div class="manual-step">
                                    <div class="manual-step-num">1</div>
                                    <div>Busque o livro desejado (seção 3.1).</div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">2</div>
                                    <div>Clique em <strong>Editar</strong> para alterar os dados, ou <strong>Excluir</strong> para remover o livro.</div>
                                </div>
                                <div class="warn-box">
                                    ⚠️ A exclusão de um livro é permanente e não pode ser desfeita.
                                </div>

                                <h6 id="sec-import-livros" class="manual-section fw-bold mt-3">3.4 Importar Livros em Lote</h6>
                                <p class="small text-muted">Use quando precisar cadastrar muitos livros de uma vez (ex: novo acervo).</p>
                                <div class="manual-step">
                                    <div class="manual-step-num">1</div>
                                    <div>Clique em <strong>Importar em Lote</strong> (botão verde) na página de Livros.</div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">2</div>
                                    <div>Clique em <strong>Baixar Modelo Excel (.xlsx)</strong> e abra o arquivo no Excel.</div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">3</div>
                                    <div>Preencha os dados dos livros nas colunas. Os campos <strong>título</strong> e <strong>autor</strong> são obrigatórios.</div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">4</div>
                                    <div>
                                        Acesse <a href="https://convertio.co/pt/xls-csv/" target="_blank">convertio.co/pt/xls-csv/</a>,
                                        envie seu arquivo .xlsx e baixe o .csv convertido.
                                    </div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">5</div>
                                    <div>De volta ao sistema, faça o upload do arquivo .csv e clique em <strong>Importar</strong>.</div>
                                </div>
                            </div>
                        </div>

                        <!-- TURMAS -->
                        <div class="card card-round mb-4 manual-section" id="sec-turmas">
                            <div class="card-header" style="background:#1F6FBF;">
                                <div class="card-title text-white">
                                    <i class="fas fa-chalkboard-teacher me-2"></i>4. Turmas e Alunos
                                </div>
                            </div>
                            <div class="card-body">
                                <h6 class="fw-bold">4.1 Criar Nova Turma</h6>
                                <div class="manual-step">
                                    <div class="manual-step-num">1</div>
                                    <div>Clique em <strong>Turmas</strong> no menu lateral.</div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">2</div>
                                    <div>Clique em <strong>Nova Turma</strong>, preencha o nome e a descrição, e clique em <strong>Cadastrar</strong>.</div>
                                </div>

                                <h6 id="sec-import-turmas" class="manual-section fw-bold mt-3">4.2 Importar Turmas em Lote</h6>
                                <div class="manual-step">
                                    <div class="manual-step-num">1</div>
                                    <div>Clique em <strong>Importar em Lote</strong> na página de Turmas.</div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">2</div>
                                    <div>Baixe o modelo .xlsx, preencha (colunas: <code>nome</code>, <code>descricao</code>) e converta em
                                        <a href="https://convertio.co/pt/xls-csv/" target="_blank">convertio.co</a>.
                                    </div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">3</div>
                                    <div>Faça o upload do .csv no sistema e clique em <strong>Importar</strong>.</div>
                                </div>

                                <h6 id="sec-alunos" class="manual-section fw-bold mt-3">4.3 Gerenciar Alunos de uma Turma</h6>
                                <div class="manual-step">
                                    <div class="manual-step-num">1</div>
                                    <div>Na lista de turmas, clique em <strong>Alunos</strong> na turma desejada.</div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">2</div>
                                    <div>Use <strong>Adicionar Aluno</strong> para cadastrar individualmente (nome, matrícula, senha opcional).</div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">3</div>
                                    <div>Use os botões <strong>Editar</strong> ou <strong>Excluir</strong> em cada linha para gerenciar alunos já cadastrados.</div>
                                </div>

                                <h6 id="sec-import-alunos" class="manual-section fw-bold mt-3">4.4 Importar Alunos em Lote</h6>
                                <div class="manual-step">
                                    <div class="manual-step-num">1</div>
                                    <div>Dentro da turma, clique em <strong>Importar Planilha</strong>.</div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">2</div>
                                    <div>Baixe o modelo .xlsx, preencha (col. A: matrícula, col. B: nome) e converta em
                                        <a href="https://convertio.co/pt/xls-csv/" target="_blank">convertio.co</a>.
                                    </div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">3</div>
                                    <div>Envie o .csv no modal e clique em <strong>Importar</strong>.</div>
                                </div>
                                <div class="tip-box">
                                    💡 Todos os alunos importados ficam vinculados à turma que você estava gerenciando.
                                </div>

                                <h6 class="fw-bold mt-3">4.5 Arquivar e Reativar Turma</h6>
                                <p class="small">
                                    <strong>Arquivar:</strong> desativa a turma e todos os seus alunos. Alunos inativos não aparecem na seleção de empréstimos.<br>
                                    <strong>Reativar:</strong> restaura a turma e os alunos para o status Ativo.
                                </p>
                            </div>
                        </div>

                        <!-- EMPRÉSTIMOS -->
                        <div class="card card-round mb-4 manual-section" id="sec-novo-emprestimo">
                            <div class="card-header" style="background:#1F6FBF;">
                                <div class="card-title text-white">
                                    <i class="fas fa-th-list me-2"></i>5. Empréstimos
                                </div>
                            </div>
                            <div class="card-body">
                                <h6 class="fw-bold">5.1 Registrar Novo Empréstimo</h6>
                                <div class="manual-step">
                                    <div class="manual-step-num">1</div>
                                    <div>Clique em <strong>Empréstimos</strong> no menu lateral e depois em <strong>Novo Empréstimo</strong>.</div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">2</div>
                                    <div><strong>Selecionar Aluno:</strong> pesquise por nome ou turma e clique no aluno desejado.</div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">3</div>
                                    <div><strong>Selecionar Livro:</strong> pesquise por título e clique no livro desejado.</div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">4</div>
                                    <div><strong>Definir Data:</strong> escolha a data de devolução (mínimo D+1) e clique em <strong>Salvar Empréstimo</strong>.</div>
                                </div>
                                <div class="tip-box mb-3">
                                    💡 O empréstimo é registrado com status <strong>Ativo</strong>. O sistema monitora automaticamente o prazo.
                                </div>

                                <h6 id="sec-devolucao" class="manual-section fw-bold">5.2 Registrar Devolução</h6>
                                <div class="manual-step">
                                    <div class="manual-step-num">1</div>
                                    <div>Na lista de empréstimos, localize o registro desejado.</div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">2</div>
                                    <div>Clique em <strong>Receber</strong> e confirme no modal que aparece.</div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">3</div>
                                    <div>O empréstimo é marcado como <strong>Devolvido</strong> com a data atual registrada.</div>
                                </div>

                                <h6 id="sec-atraso" class="manual-section fw-bold mt-3">5.3 Empréstimos em Atraso</h6>
                                <p class="small">
                                    O sistema detecta atrasos automaticamente ao abrir a página inicial. Empréstimos com prazo vencido
                                    aparecem com badge <span class="badge bg-danger">Atrasado</span> na lista e na tabela do Dashboard.
                                    Para regularizar, clique em <strong>Receber</strong> normalmente.
                                </p>
                            </div>
                        </div>

                        <!-- RANKING -->
                        <div class="card card-round mb-4 manual-section" id="sec-ranking">
                            <div class="card-header" style="background:#1F6FBF;">
                                <div class="card-title text-white">
                                    <i class="fas fa-trophy me-2"></i>6. Ranking de Leitura
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="manual-step">
                                    <div class="manual-step-num">1</div>
                                    <div>Clique em <strong>Ranking</strong> no menu lateral. O mês atual é carregado automaticamente.</div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">2</div>
                                    <div>Use os filtros para escolher <strong>mês</strong>, <strong>ano</strong>, <strong>turma</strong> e <strong>quantidade</strong> de alunos exibidos.</div>
                                </div>
                                <div class="manual-step">
                                    <div class="manual-step-num">3</div>
                                    <div>Clique em <strong>Buscar</strong> para atualizar os resultados.</div>
                                </div>
                                <p class="small mt-2">
                                    O ranking exibe um <strong>pódio visual</strong> com os 5 primeiros colocados, um
                                    <strong>gráfico horizontal</strong> e uma <strong>tabela completa</strong> com barra de progresso.
                                </p>
                                <div class="tip-box">
                                    💡 O ranking conta pela data de início do empréstimo, não pela data de devolução.
                                </div>
                            </div>
                        </div>

                    </div><!-- /col-md-9 -->
                </div><!-- /row -->

            </div>
        </div>
        <?php include 'includes/footer.php'; ?>

        <script>
        // Destaca link ativo no sumário ao rolar
        (function() {
            var links = document.querySelectorAll('.manual-toc a');
            var sections = [];
            links.forEach(function(a) {
                var id = a.getAttribute('href').replace('#','');
                var el = document.getElementById(id);
                if (el) sections.push({ el: el, a: a });
            });
            window.addEventListener('scroll', function() {
                var scrollY = window.scrollY + 100;
                var current = null;
                sections.forEach(function(s) {
                    if (s.el.offsetTop <= scrollY) current = s;
                });
                links.forEach(function(a) { a.classList.remove('active'); });
                if (current) current.a.classList.add('active');
            }, { passive: true });
        })();
        </script>
</body>
</html>
