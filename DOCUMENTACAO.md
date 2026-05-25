# 📚 Multimeios — Documentação do Sistema

> Sistema de Gerenciamento de Biblioteca Escolar  
> Desenvolvido pelo **Curso Técnico em Informática — EEEP Multimeios**

---

## Índice

1. [Visão Geral](#1-visão-geral)
2. [Requisitos Técnicos](#2-requisitos-técnicos)
3. [Instalação e Configuração](#3-instalação-e-configuração)
4. [Banco de Dados](#4-banco-de-dados)
5. [Estrutura de Arquivos](#5-estrutura-de-arquivos)
6. [Módulos do Sistema](#6-módulos-do-sistema)
7. [Fluxo de Empréstimo](#7-fluxo-de-empréstimo)
8. [Importação em Lote via CSV](#8-importação-em-lote-via-csv)
9. [Segurança](#9-segurança)
10. [Solução de Problemas](#10-solução-de-problemas)

---

## 1. Visão Geral

O **Multimeios** é um sistema web para controle de biblioteca escolar. Permite gerenciar o acervo de livros, cadastro de turmas e alunos, registro de empréstimos e devoluções, e geração de relatórios de leitura.

**Funcionalidades principais:**
- Cadastro e busca de livros
- Gerenciamento de turmas e alunos
- Controle de empréstimos e devoluções
- Alertas automáticos de atraso
- Ranking de leitura por mês e turma
- Dashboard com estatísticas em tempo real
- Importação em lote via planilha CSV

**Modo de operação:** 100% offline — sem dependências de CDN ou serviços externos.

---

## 2. Requisitos Técnicos

| Componente | Versão mínima |
|---|---|
| PHP | 7.4+ (recomendado 8.0+) |
| MySQL / MariaDB | 5.7+ / 10.3+ |
| Servidor web | Apache (XAMPP) |
| Navegador | Chrome, Firefox, Edge (modernos) |

**Stack utilizada:**
- PHP com PDO (prepared statements)
- MySQL — banco `bibliotecaep`
- Bootstrap 5 (local)
- Kaiadmin Admin Template (local)
- jQuery 3.7.1 (local)
- Chart.js (local)
- DataTables (local)
- Font Awesome 5 (local)

---

## 3. Instalação e Configuração

### 3.1 Instalação

1. Instale o [XAMPP](https://www.apachefriends.org/) com PHP 7.4+ e MySQL
2. Copie a pasta `multimeios` para `C:\xampp\htdocs\`
3. Importe o banco de dados (veja seção 3.2)
4. Configure a conexão (veja seção 3.3)
5. Acesse: `http://localhost/multimeios`

### 3.2 Banco de Dados

1. Importe o arquivo `bibliotecaep.sql` no phpMyAdmin
2. Após importar, o usuário padrão já estará criado:

| Campo | Valor |
|---|---|
| Usuário | `multimeios` |
| Senha | `senha123` |

Para criar o usuário manualmente via SQL:

```sql
INSERT INTO usuarios (nome, senha, foto)
VALUES ('multimeios', '$2y$10$YPWeCGOPmDDLYXCK47SCROykn.aJtDg9YPhe4qBB7paneSLDPeNrG', NULL);
```

> ⚠️ A senha `senha123` está armazenada como hash bcrypt. Nunca armazene senha em texto puro.

## 4. Banco de Dados

### 4.1 Diagrama de Tabelas

```
turmas                alunos                  livros
──────────────        ──────────────────       ──────────────────
id (PK)         ←─── turma (FK)               idLivro (PK)
nome                  idAluno (PK)             titulo
descricao             matricula                autor
status                nome                     editora
                      senha                    edicao
                      status                   dataPublicacao
                                               lugar
                                               origem
Emprestimo                                     categoria
──────────────────                             ISBN
idEmprestimo (PK)
idA (FK) ────────────────────────────────────► alunos.idAluno
idL (FK) ────────────────────────────────────► livros.idLivro
dataInicial
dataFinal
dataRecebimento
status
```

### 4.2 Valores de Status

**`Emprestimo.status`**
| Valor | Significado |
|---|---|
| `0` | Ativo (livro emprestado, dentro do prazo) |
| `1` | Devolvido |
| `-1` | Atrasado (prazo vencido, não devolvido) |

> O sistema atualiza automaticamente status `0 → -1` toda vez que a página inicial é acessada, para empréstimos com `dataFinal < hoje`.

**`turmas.status` / `alunos.status`**
| Valor | Significado |
|---|---|
| `1` | Ativo/Ativa |
| `0` | Inativo/Arquivada |

---

## 5. Estrutura de Arquivos

```
multimeios/
│
├── index.php                  ← Tela de login
├── principal.php              ← Dashboard (página inicial)
├── livros.php                 ← Busca de livros
├── bd.php                     ← Conexão com banco de dados
│
├── php/
│   ├── includes/              ← Partials reutilizáveis
│   │   ├── head.php           ← <head> HTML (CSS, meta tags)
│   │   ├── sidebar.php        ← Menu lateral
│   │   ├── navbar.php         ← Barra superior
│   │   └── footer.php         ← Rodapé + scripts JS
│   │
│   ├── — Páginas de visualização —
│   ├── emprestimo.php         ← Lista de empréstimos
│   ├── turmas.php             ← Lista de turmas
│   ├── alterar_turma.php      ← Gerenciar alunos de uma turma
│   ├── ranking.php            ← Ranking de leitura
│   ├── cadastrarLivros.php    ← Formulário cadastro de livro
│   ├── editarLivros.php       ← Formulário edição de livro
│   ├── buscarLivros.php       ← Resultados de busca de livros
│   ├── selecionarAluno.php    ← Passo 1 do empréstimo
│   ├── selecionarLivro.php    ← Passo 2 do empréstimo
│   ├── selecionarData.php     ← Passo 3 do empréstimo
│   ├── importar_livros.php    ← Importação CSV de livros
│   ├── importar_turmas.php    ← Importação CSV de turmas
│   ├── sobre.php              ← Página sobre o sistema
│   │
│   ├── — Ações (sem HTML, só processam e redirecionam) —
│   ├── autenticacao.php       ← Processa login
│   ├── sair.php               ← Logout
│   ├── salvarEmprestimo.php   ← Grava empréstimo no banco
│   ├── receberLivro.php       ← Registra devolução
│   ├── atualizarLivros.php    ← Salva edição de livro
│   ├── excluirLivros.php      ← Exclui livro
│   ├── excluirEmprestimo.php  ← Exclui empréstimo
│   ├── cadastrar_aluno.php    ← Cadastra aluno (AJAX)
│   ├── atualizar_aluno.php    ← Edita aluno
│   ├── excluir_aluno.php      ← Exclui aluno
│   ├── inserir_turma.php      ← Cria turma
│   ├── editar_turma.php       ← Edita turma
│   ├── excluir_turma.php      ← Exclui turma
│   ├── arquivar_turma.php     ← Arquiva turma + inativa alunos
│   └── desarquivar_turma.php  ← Reativa turma + alunos
│
└── assets/
    ├── css/                   ← Bootstrap, Kaiadmin, fontes
    ├── js/                    ← jQuery, Chart.js, DataTables, etc.
    └── fonts/                 ← FontAwesome, Simple Line Icons
```

---

## 6. Módulos do Sistema

### 6.1 Login (`index.php`)

Tela de autenticação. Credenciais verificadas contra tabela `usuarios` com `password_verify()` (hash bcrypt).

Após login bem-sucedido, a sessão recebe:
- `$_SESSION['autenticado'] = '1'`
- `$_SESSION['nome']` — nome do usuário
- `$_SESSION['foto']` — caminho da foto de perfil

**Todas as páginas verificam** `$_SESSION['autenticado']` e redirecionam para `index.php` se ausente.

---

### 6.2 Dashboard (`principal.php`)

Exibido após o login. Atualiza automaticamente empréstimos vencidos para status `-1`.

**Cards de resumo:**
- Total de livros no acervo
- Alunos ativos
- Livros atualmente emprestados
- Devolvidos no mês corrente
- Em atraso
- Turmas ativas

**Gráfico:** Empréstimos por mês nos últimos 6 meses (Chart.js)

**Taxa de devolução:** Porcentagem de empréstimos já devolvidos sobre o total

**Top 5 livros mais emprestados** (todos os tempos)

**Top 5 leitores do mês** (mês atual)

**Empréstimos recentes** — últimos 5 ativos, com alerta visual se devolução ≤ 2 dias

**Tabela de atrasos** — aparece apenas se houver atrasos; botão "Receber" por linha

---

### 6.3 Livros (`livros.php` / `php/cadastrarLivros.php`)

**Buscar:** Formulário de pesquisa por título, autor, editora, edição, data, lugar, origem, categoria. Resultados em `buscarLivros.php` com botões Editar e Excluir por livro.

**Cadastrar (individual):** Formulário com campos:

| Campo | Obrigatório |
|---|---|
| Título | ✅ |
| Autor | ✅ |
| Editora | ❌ |
| Edição | ❌ |
| Data de Publicação | ❌ |
| Lugar | ❌ |
| Origem | ❌ |
| Categoria | ❌ |
| ISBN | ❌ |

**Importar em lote:** ver seção 8.

---

### 6.4 Turmas (`php/turmas.php`)

Lista todas as turmas com status (Ativa / Arquivada).

**Ações por turma:**
- **Alunos** — abre `alterar_turma.php` para gerenciar alunos da turma
- **Editar** — modal inline para alterar nome e descrição
- **Arquivar** — muda turma para `status=0` e todos os alunos para `status=0`
- **Reativar** — muda turma e alunos de volta para `status=1`
- **Excluir** — exclui permanentemente a turma

**Importar em lote:** ver seção 8.

---

### 6.5 Alunos (`php/alterar_turma.php`)

Acessado via **Turmas → Alunos**. Exibe alunos da turma selecionada.

**Adicionar aluno (individual):** Modal AJAX (sem recarregar página). Campos: nome (obrigatório), matrícula, senha.

**Importar alunos via CSV:** Modal com upload de arquivo `.csv` no formato `matricula,nome`.

**Editar aluno:** Modal com nome e matrícula.

**Excluir aluno:** Modal de confirmação antes de deletar.

---

### 6.6 Empréstimos (`php/emprestimo.php`)

Lista todos os empréstimos com filtros de busca. Coluna de status com badge colorido:

| Badge | Significado |
|---|---|
| 🟢 Ativo | Emprestado, dentro do prazo |
| 🔴 Atrasado | Prazo vencido, não devolvido |
| ⚪ Devolvido | Livro já retornado |

**Ações:** Receber (devolver), Excluir.

---

### 6.7 Registrar Empréstimo (3 passos)

```
selecionarAluno.php → selecionarLivro.php → selecionarData.php → salvarEmprestimo.php
```

**Passo 1 — Selecionar Aluno:** Busca por nome e/ou turma. Apenas alunos com `status=1` aparecem.

**Passo 2 — Selecionar Livro:** Busca por título. Exibe todos os livros cadastrados.

**Passo 3 — Confirmar e definir data:** Exibe resumo (aluno + livro). Campo de data de devolução com mínimo de D+1. Ao salvar, redireciona para `emprestimo.php`.

---

### 6.8 Ranking (`php/ranking.php`)

Filtra por **mês**, **ano** e **turma** (ou todas). Carrega automaticamente o mês atual.

**Pódio visual (top 5):** Cards estilizados com alturas escalonadas:
- 🥇 1º — ouro, mais alto
- 🥈 2º — prata
- 🥉 3º — bronze
- 4º — azul
- 5º — lilás

**Gráfico horizontal:** Barras por aluno (Chart.js), com tooltip exibindo turma.

**Tabela completa:** Posição com badge colorido, barra de progresso proporcional ao 1º lugar.

---

## 7. Fluxo de Empréstimo

```
┌─────────────────────────────────────────────────────────────────┐
│                     REGISTRAR EMPRÉSTIMO                        │
│                                                                 │
│  [Empréstimos] → [Novo Empréstimo]                              │
│       ↓                                                         │
│  Buscar aluno (nome/turma) → Selecionar aluno                   │
│       ↓                                                         │
│  Buscar livro (título) → Selecionar livro                       │
│       ↓                                                         │
│  Definir data de devolução → Confirmar                          │
│       ↓                                                         │
│  Empréstimo salvo (status = 0 "Ativo")                          │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                     DEVOLUÇÃO                                   │
│                                                                 │
│  [Empréstimos] → Localizar linha → [Receber]                    │
│       ↓                                                         │
│  Modal de confirmação                                           │
│       ↓                                                         │
│  status = 1 "Devolvido", dataRecebimento = hoje                 │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                 DETECÇÃO DE ATRASO (automática)                 │
│                                                                 │
│  Ao acessar principal.php:                                      │
│  UPDATE Emprestimo SET status = -1                              │
│  WHERE dataFinal < CURDATE() AND status = 0                     │
└─────────────────────────────────────────────────────────────────┘
```

---

## 8. Importação em Lote via CSV

### 8.1 Livros (`php/importar_livros.php`)

**Acessar:** Livros → "Importar em Lote"

**Baixar modelo:** Botão "Baixar Modelo CSV" gera arquivo de exemplo pronto para editar no Excel.

**Formato do arquivo:**

```csv
titulo,autor,editora,edicao,dataPublicacao,lugar,origem,categoria,ISBN
Dom Casmurro,Machado de Assis,Garnier,1ª,1899,Rio de Janeiro,Doação,Romance,978-85-359-0277-5
O Cortiço,Aluísio Azevedo,Ática,2ª,1890,São Paulo,Compra,Naturalismo,
```

| Coluna | Obrigatório | Observação |
|---|---|---|
| titulo | ✅ | Linha ignorada se vazio |
| autor | ✅ | Linha ignorada se vazio |
| editora | ❌ | Deixar vazio = NULL |
| edicao | ❌ | |
| dataPublicacao | ❌ | Formato livre (ex: 1899, 2023) |
| lugar | ❌ | |
| origem | ❌ | Ex: Doação, Compra |
| categoria | ❌ | |
| ISBN | ❌ | |

**Regras:**
- Primeira linha deve ser o cabeçalho (ignorada)
- Separador: vírgula `,`
- Encoding: UTF-8 (recomendado salvar como "CSV UTF-8" no Excel)
- Após importação, exibe contagem de importados e lista de linhas ignoradas com motivo

---

### 8.2 Turmas (`php/importar_turmas.php`)

**Acessar:** Turmas → "Importar em Lote"

**Formato do arquivo:**

```csv
nome,descricao
1º Ano A,Turma do primeiro ano A
2º Ano B,Turma do segundo ano B
3º Ano C,
```

| Coluna | Obrigatório | Observação |
|---|---|---|
| nome | ✅ | Linha ignorada se vazio |
| descricao | ❌ | Pode ser vazio |

**Regras:** Todas as turmas importadas recebem `status = 1` (Ativa).

---

### 8.3 Alunos por turma (`php/alterar_turma.php`)

**Acessar:** Turmas → (turma desejada) → "Importar Planilha"

**Formato do arquivo:**

```csv
matricula,nome
20240001,Ana Silva
20240002,Bruno Costa
,Carlos Oliveira
```

| Coluna | Observação |
|---|---|
| matricula | Pode ser vazio |
| nome | Obrigatório |

> Alunos importados ficam vinculados automaticamente à turma em que o arquivo foi importado.

---

## 9. Segurança

### Autenticação
- Senhas armazenadas com `password_hash()` (bcrypt, PHP padrão)
- Verificação via `password_verify()`
- Sessão PHP (`$_SESSION['autenticado']`) exigida em **todos** os arquivos
- `header('Location: ...')` + `exit` imediato após falha de auth — sem renderização parcial

### SQL Injection
- Todos os queries usam **PDO com prepared statements** e `bindParam`/`execute([...])` — nenhuma concatenação de variáveis em SQL

### XSS
- Toda saída de dados do banco usa `htmlspecialchars()` antes de exibir no HTML

### CSRF
- Ações destrutivas (excluir, arquivar) exigem confirmação via modal ou `confirm()` JavaScript
- *(Recomendação futura: tokens CSRF nos formulários POST)*

### Operações destrutivas
- `excluirLivros.php` e `excluirEmprestimo.php` habilitam e desabilitam `FOREIGN_KEY_CHECKS` corretamente para evitar deixar o banco em estado inconsistente

---

## 10. Solução de Problemas

### "Falha na conexão" ao abrir o sistema
- Verifique se o MySQL está rodando no XAMPP
- Confirme nome do banco em `bd.php` (`bibliotecaep`)
- Confirme usuário/senha em `bd.php`

### Ícones não aparecem (quadrados no lugar)
- Verifique se os arquivos em `assets/fonts/fontawesome/` e `assets/fonts/simple-line-icons/` estão presentes
- Confirme que `assets/css/fonts.min.css` existe

### Gráficos não aparecem
- Verifique se `assets/js/plugin/chart.js/chart.min.js` existe
- Verifique erros no console do navegador (F12)

### Importação CSV com caracteres errados (acentos quebrados)
- Salve o arquivo como **CSV UTF-8** no Excel (Arquivo → Salvar Como → CSV UTF-8 com BOM)
- O sistema detecta e remove o BOM automaticamente

### Empréstimos não aparecem como atrasados
- Acesse a página inicial (`principal.php`) — o sistema atualiza os status de atraso ao carregar essa página
- Confirme que a data do servidor está correta

### Login não funciona com usuário novo
- A senha deve ser armazenada como hash bcrypt via `password_hash($senha, PASSWORD_DEFAULT)`
- Não armazene senha em texto puro na tabela `usuarios`

---

## Histórico de Versões

| Versão | Data | Descrição |
|---|---|---|
| 1.0 | 2024 | Versão inicial do sistema |
| 2.0 | 2025 | Refactoring completo: includes reutilizáveis, bugs críticos corrigidos, modo 100% offline, dashboard com estatísticas, ranking melhorado, importação em lote CSV |

---

*Documentação gerada em 2025 — EEEP Multimeios*
