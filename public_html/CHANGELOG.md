# Changelog – RSP Diecast

Todas as mudanças relevantes deste projeto serão documentadas neste arquivo.

O formato segue, de forma livre, as boas práticas do [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)
e versionamento semântico (SemVer), começando em versão `0.x` enquanto o sistema ainda está em evolução.

## [Unreleased]

- Ajustes futuros de layout e UX no dashboard.
- Melhorias de performance nas consultas do banco.
- Possível migração/integração com versão em Laravel descrita na documentação técnica.

---

## [0.3.1] – 2025-12-10

### Corrigido
- Correção na visualização de fotos nas telas de miniaturas/detalhes, garantindo que as imagens cadastradas sejam exibidas corretamente.
- Ajuste no fluxo de login para evitar erros ao acessar páginas internas após autenticação.

### Alterado
- Refinos de layout nas páginas principais e no dashboard, padronizando a interface com a identidade visual atual do sistema.

_Commits relacionados: `ajuste de layout`, `correção de visualização de fotos e login`._

---

## [0.3.0] – 2025-11-26

### Alterado
- Reestruturação do banco de dados para suportar melhor o crescimento da coleção, com ajustes em tabelas e relacionamentos.

_Commits relacionados: `estrutura de banco`._

---

## [0.2.0] – 2025-11-06 / 2025-11-07

### Adicionado
- Nova `index.php` como página principal autenticada, conectada ao banco de dados.
- Botão de **logout** para permitir o encerramento seguro da sessão do usuário.
- Script `limpar_duplicados` para auxiliar na manutenção da base de dados, removendo registros duplicados.

### Alterado
- Diversos ajustes no formulário de cadastro (`cadastro.php`) e no fluxo de salvamento (`salvar_carro.php`), melhorando a experiência de cadastro de miniaturas e carros.
- Melhorias na listagem de carros (consultas, filtros e exibição), deixando a navegação da coleção mais fluida.
- Ajustes no dashboard para exibir corretamente os dados vindos do banco (contagens, listagens e painéis).
- Inclusão de verificação de ambiente para diferenciar comportamentos entre desenvolvimento e produção.

_Commits relacionados (exemplos): `update cadastro.php`, `atualizado cadastro`, `ajustes no cadastro`,  
`salvar_carro`, `cadastro.php e salvar_carro.php`, `update listar carros`,  
`update com listar carros`, `ajuste index`, `ajuste dashboard`,  
`adicionado botao de logout`, `adicionado limpar_duplicados`,  
`adicionado uma verificação de ambiente`._

---

## [0.1.1] – 2025-10-29 / 2025-10-30

### Adicionado
- Inclusão de campo/controle de **ano** nas estruturas de cadastro e listagem, permitindo organizar as miniaturas por ano.

### Alterado
- Pequenos ajustes gerais no código e organização de arquivos, preparando o terreno para os aprimoramentos seguintes.

_Commits relacionados: `inserção de anos`, `.`._

---

## [0.1.0] – 2025-10-17 / 2025-10-27

### Adicionado
- Criação inicial do repositório RSP Diecast, incluindo:
  - Licença MIT (`LICENSE`).
  - Código de Conduta (`CODE_OF_CONDUCT.md`).
  - Guia de Contribuição (`CONTRIBUTING.md`).
  - Política de Segurança (`SECURITY.md`).
  - Relatório técnico detalhado do sistema (`RSP-Diecast-Relatorio-Tecnico.md`).
- Configuração inicial de banco de dados e infraestrutura do backend (`db e lavarel` / conexão ao DB).
- Primeiras versões das telas:
  - `index` inicial do sistema.
  - Tela de cadastro (`cadastro.php`).
  - Endpoint de salvamento (`salvar_carro.php`).
  - Primeira `index` já integrada ao banco de dados.

### Documentação
- Atualizações incrementais no relatório técnico, no guia de contribuição e no changelog anterior.

_Commits relacionados (exemplos): `license`, `security`, `Update CODE_OF_CONDUCT.md`,  
`Update CONTRIBUTING.md`, `Update RSP-Diecast-Relatorio-Tecnico.md`,  
`Update CHANGELOG.md`, `db e lavarel`, `nova index`, `nova index ja com banco`,  
`conexao ao db`, `cadastro.php`, `cadastro.php e salvar_carro.php`._
