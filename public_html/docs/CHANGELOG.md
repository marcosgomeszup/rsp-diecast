🏁 CHANGELOG – RSP Diecast / Coleção Racing  
Todas as mudanças relevantes neste projeto serão documentadas neste arquivo.  
O formato segue o padrão **Keep a Changelog**  
e **Semantic Versioning**.

---

## [v1.3.0] – 2025-11-07
🚀 **Versão estável — Estrutura Web Completa e Painel Administrativo**

### 🆕 Adicionado
- Sistema web completo (PHP + MySQL) implantado em ambiente **cPanel / public_html**.
- Criação do **painel administrativo** com menu lateral fixo e layout unificado:
  - `index.php` com listagem das **10 últimas miniaturas** e contador total.
  - `dashboard.php` com gráficos interativos e estatísticas gerais.
  - `cadastro.php` com formulário dividido entre “Dados do Carro” e “Dados da Miniatura”.
  - `listar_carros.php` (em estrutura base para a próxima versão).
- Implementação de **upload de imagens** (até 3 fotos por miniatura) com salvamento padronizado na pasta `/uploads`.
- Criação da **tabela `escalas`** e vinculação direta ao cadastro.
- Adição de campo **“comentário” (VARCHAR 1024)** para observações livres.
- Inclusão de **controle de sessão e login obrigatório** com `$_SESSION`.
- Implementação da **função `foto_principal()`** para exibição da primeira imagem com fallback.
- Criação do **botão de Logout** e página `logout.php` com destruição segura da sessão.
- Padronização de filtros de integridade nos selects:
  ```sql
  WHERE nome IS NOT NULL AND TRIM(nome) <> ''
Implementação do histórico técnico oficial em formato Markdown:

HISTORICO_V1.md (documentação de versão 1.0)

Descrição detalhada de arquitetura, banco de dados e interface.

🧩 Alterado
Estrutura de layout reformulada: menu lateral substitui o menu superior em todas as páginas.

Paleta e tipografia mantidas conforme diretriz RSP Diecast / Williams Theme:

Azul escuro #00205B

Azul claro #00AEEF

Branco #FFFFFF

Campos <select> reestruturados para evitar duplicação e valores vazios.

Padronização do charset para utf8mb4 e aplicação global de htmlspecialchars() e trim().

Otimização das consultas SQL com fetch_all(MYSQLI_ASSOC) para melhor performance e compatibilidade.

🐞 Corrigido
Problema de opções em branco nos selects (corrigido via tratamento de encoding e reconstrução dos resultsets).

Erro 404 na navegação (listar_carros.php vs listagem.php).

Caminhos incorretos em fotos JSON (../uploads/, public_html/) corrigidos com SQL de normalização.

Exibição incorreta das imagens (fallback implementado).

Problemas de charset e duplicação em pilotos, categorias e equipes resolvidos com TRIM() e DELETE seletivo.

🧱 Infraestrutura
Banco de dados revisado e normalizado.

Configuração de permissões ajustada (uploads/ com 755).

Compatibilidade total com MySQL 8.x e PHP 8.3+.

Scripts SQL de manutenção criados:

Geração de anos (1940–2025).

Inserção de escalas em ordem de grandeza (1:10, 1:18, 1:24, 1:32, 1:43, 1:64).

🧾 Documentação
Criação do documento técnico “Histórico Técnico — RSP Diecast v1.0” detalhando arquitetura, módulos e correções.

Definição de plano de evolução para v1.1, v1.2 e v1.3.

Adaptação de estilo Markdown para integração com o GitHub Wiki e README.

🔜 Próximas Versões Planejadas
[v1.4.0] – 2025-12-20 (prevista)
Planejado

Conclusão do módulo listar_carros.php com filtros dinâmicos.

Inclusão de estatísticas avançadas no dashboard (por marca, escala e fabricante).

API REST inicial para integração com PWA.

Mecanismo de mensagens de sucesso/erro no cadastro.

Otimização de performance no carregamento de imagens e consultas.

🧾 Histórico de Versões
Versão	Data	Tipo	Descrição
v1.3.0	07/11/2025	Minor	Estrutura Web Completa e Painel Administrativo
v1.2.0	17/10/2025	Major	Base completa e documentação
v1.1.0	20/09/2025	Minor	Estrutura inicial
v1.0.0	15/08/2025	Base	Início do projeto

📜 Licença
Este projeto está licenciado sob os termos da MIT License.
Consulte o arquivo LICENSE para mais detalhes.

“Construindo tecnologia com precisão, paixão e performance.”
— Equipe RSP Diecast © 2025

yaml
Copy code

---

📦 **Sugestão de commit:**
```bash
git add CHANGELOG.md
git commit -m "🧱 v1.3.0 – Estrutura web completa e painel administrativo"
git tag -a v1.3.0 -m "Versão 1.3.0 – RSP Diecast / Coleção Racing"
git push origin main --tags
