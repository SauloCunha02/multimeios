# 📚 Multimeios — Sistema de Biblioteca Escolar

Sistema de gerenciamento de biblioteca escolar desenvolvido pelos alunos do **Curso Técnico em Informática** da **EEEP Deputado José Maria Melo** (Guaraciaba do Norte - CE).

---

## ✨ Funcionalidades

- **Dashboard** — estatísticas em tempo real (acervo, empréstimos, atrasos, ranking do mês)
- **Livros** — cadastro, edição, exclusão e importação em lote via planilha
- **Empréstimos** — controle de retiradas e devoluções com alertas de atraso automáticos
- **Turmas e Alunos** — gerenciamento completo, importação em lote via planilha
- **Ranking de Leitura** — pódio top 5 por mês/ano com gráfico e tabela completa
- **Manual do Usuário** — guia passo a passo embutido no sistema
- **100% Offline** — nenhuma dependência de CDN externo

---

## ⚙️ Requisitos

| Requisito | Versão mínima |
|-----------|--------------|
| PHP       | 7.4+         |
| MySQL     | 5.7+ / MariaDB 10.3+ |
| Servidor  | Apache (XAMPP recomendado) |
| Navegador | Chrome, Firefox, Edge (moderno) |

---

## 🚀 Instalação

### 1. Clone o repositório

```bash
git clone https://github.com/SauloCunha02/multimeios.git
```

Coloque a pasta dentro do diretório do servidor web:

- **XAMPP (Windows):** `C:\xampp\htdocs\multimeios`
- **Linux/Mac:** `/var/www/html/multimeios`

### 2. Crie o banco de dados

Acesse o **phpMyAdmin** (ou qualquer client MySQL) e execute:

```sql
CREATE DATABASE bibliotecaep CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

Em seguida, importe o arquivo de estrutura:

```
bibliotecaep.sql
```

No phpMyAdmin: selecione o banco `bibliotecaep` → aba **Importar** → selecione `bibliotecaep.sql` → Execute.

### 3. Configure a conexão

Edite o arquivo `bd.php` na raiz do projeto:

```php
$host = 'localhost';   // endereço do MySQL
$db   = 'bibliotecaep'; // nome do banco
$user = 'root';        // usuário MySQL
$pass = '';            // senha MySQL
```

> ⚠️ Em produção, use um usuário MySQL dedicado com senha forte. Não use `root`.

### 4. Acesse o sistema

Abra o navegador e acesse:

```
http://localhost/multimeios
```

---

## 🔐 Acesso padrão

| Campo | Valor |
|-------|-------|
| Usuário | `multimeios` |
| Senha   | `senha123`   |

> ⚠️ Troque a senha após o primeiro acesso editando diretamente no banco de dados (tabela `usuarios`, campo `senha` com hash bcrypt).

---

## 📁 Estrutura do Projeto

```
multimeios/
├── index.php              # Tela de login
├── principal.php          # Dashboard
├── livros.php             # Lista de livros
├── bd.php                 # Configuração do banco de dados
├── bibliotecaep.sql       # Estrutura do banco
├── assets/                # CSS, JS, fontes e imagens (100% local)
└── php/
    ├── includes/          # Componentes reutilizáveis (head, navbar, sidebar, footer)
    ├── exemplos/          # Modelos .xlsx para importação em lote
    ├── uploads/           # Capas de livros enviadas
    ├── emprestimo.php     # Tela de empréstimos
    ├── turmas.php         # Gerenciamento de turmas
    ├── ranking.php        # Ranking de leitura
    ├── importar_livros.php   # Importação em lote de livros
    ├── importar_turmas.php   # Importação em lote de turmas
    ├── manual.php         # Manual do usuário
    └── sobre.php          # Sobre o sistema
```

---

## 📊 Importação em Lote (Livros / Turmas / Alunos)

O sistema aceita planilhas no fluxo:

1. **Baixe** o modelo `.xlsx` dentro do sistema (botão "Baixar Modelo Excel")
2. **Preencha** no Excel ou LibreOffice
3. **Converta** para `.csv` em [convertio.co/pt/xls-csv/](https://convertio.co/pt/xls-csv/)
4. **Importe** o arquivo `.csv` no sistema

### Colunas por módulo

| Módulo | Colunas obrigatórias | Colunas opcionais |
|--------|---------------------|-------------------|
| Livros | `titulo`, `autor` | `editora`, `edicao`, `dataPublicacao`, `lugar`, `origem`, `categoria`, `ISBN` |
| Turmas | `nome` | `descricao` |
| Alunos | `nome` | `matricula` |

> Livros duplicados (mesmo ISBN ou mesmo título+autor) são ignorados automaticamente.

---

## 🗄️ Reset do Banco de Dados

Para apagar todos os dados e recomeçar do zero (mantém a estrutura):

```sql
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE Emprestimo;
TRUNCATE TABLE alunos;
TRUNCATE TABLE turmas;
TRUNCATE TABLE livros;
SET FOREIGN_KEY_CHECKS = 1;
```

Para apagar inclusive o usuário de acesso (reiniciar tudo):

```sql
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE Emprestimo;
TRUNCATE TABLE alunos;
TRUNCATE TABLE turmas;
TRUNCATE TABLE livros;
TRUNCATE TABLE usuarios;
SET FOREIGN_KEY_CHECKS = 1;
```

---

## 🛠️ Tecnologias Utilizadas

| Tecnologia | Uso |
|-----------|-----|
| PHP 8+ | Backend e lógica de negócio |
| MySQL / MariaDB | Banco de dados |
| Bootstrap 5 | Interface responsiva |
| Chart.js | Gráficos do dashboard e ranking |
| DataTables | Tabelas com busca e paginação |
| jQuery | Interações JS |
| Kaiadmin Lite | Template base do painel admin |

---

## 👥 Desenvolvido por

**EEEP Deputado José Maria Melo** — Guaraciaba do Norte, Ceará  
Curso Técnico em Informática — Turma 2024

- 📸 Instagram da escola: [@eeepdepjosemariamelo](https://www.instagram.com/eeepdepjosemariamelo/)
- 📸 Instagram do curso: [@infor_epdjmm](https://www.instagram.com/infor_epdjmm/)

---

## 📄 Licença

Este projeto é disponibilizado para uso educacional e comunitário.  
Consulte o arquivo [LICENSE](LICENSE) para mais detalhes.
