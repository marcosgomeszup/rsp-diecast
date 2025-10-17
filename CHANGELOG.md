# 🏁 CHANGELOG – RSP Diecast / Coleção Racing

> Todas as mudanças relevantes neste projeto serão documentadas neste arquivo.  
> O formato segue o padrão [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/)  
> e [Semantic Versioning](https://semver.org/lang/pt-BR/).

---

## [v1.2.0] – 2025-10-17  
### 🚀 Versão atual – Base completa e documentação

**Adicionado**
- Arquitetura completa do sistema (Laravel + MySQL + Tailwind).  
- Estrutura de dados e modelagem das tabelas (`users`, `cars`, `car_images`).  
- Implementação de login híbrido (Google e local).  
- Upload de imagens (câmera, desktop e web).  
- Exportação de relatórios (CSV e XLSX) via Laravel Excel.  
- Dashboard interativo com Chart.js.  
- PWA com cache dinâmico e modo offline.  
- Interface escura inspirada na **Williams F1**.  
- Documentação técnica completa em `RSP-Diecast-Relatorio-Tecnico.md`.  
- Arquivos de governança: `README.md`, `LICENSE`, `CONTRIBUTING.md`, `CODE_OF_CONDUCT.md`, `SECURITY.md`.

**Alterado**
- Paleta de cores e fontes ajustadas para o tema Williams (Azul escuro #00205B, Azul claro #00AEEF, Branco #FFFFFF).  
- Organização do repositório para incluir diretórios `/docs/imagens` e `/docs/scripts`.

**Removido**
- Campo `posicao_campeonato` da tabela de carros, por não agregar valor estatístico imediato.

---

## [v1.1.0] – 2025-09-20  
### 🔧 Estrutura inicial

**Adicionado**
- Protótipo de autenticação local (login e senha).  
- Configuração inicial de banco MySQL via phpMyAdmin (versão 8.0.43).  
- Criação das entidades principais: carros e miniaturas.  
- Definição da arquitetura base (cPanel + Laravel).

---

## [v1.0.0] – 2025-08-15  
### 🧱 Início do projeto

**Adicionado**
- Conceito e escopo do sistema RSP Diecast / Coleção Racing.  
- Definição de requisitos funcionais e não funcionais.  
- Escolha da stack tecnológica: PHP 8.4 + Laravel 11 + MySQL 8.  
- Planejamento do PWA e estrutura modular.  

---

## 🔜 Próximas Versões Planejadas

### [v1.3.0] – 2025-12-01 *(prevista)*
**Planejado**
- API pública (REST + JWT).  
- Dashboard aprimorado com estatísticas por fabricante.  
- Sistema de notificações e alertas.  
- Sincronização PWA com cadastros offline.  

### [v1.4.0] – 2026-02-15 *(prevista)*
**Planejado**
- Módulo de estoque e gerenciamento de coleções privadas.  
- Integração com redes sociais e compartilhamento de miniaturas.  
- Modo noturno dinâmico com preferências do usuário.

---

## 🧾 Histórico de Versões

| Versão | Data | Tipo | Descrição |
|---------|------|------|------------|
| **v1.2.0** | 17/10/2025 | Major | Base completa e documentação |
| **v1.1.0** | 20/09/2025 | Minor | Estrutura inicial |
| **v1.0.0** | 15/08/2025 | Base | Início do projeto |

---

## 📜 Licença
Este projeto está licenciado sob os termos da **MIT License**.  
Consulte o arquivo [LICENSE](./LICENSE) para mais detalhes.

---

> “Construindo tecnologia com precisão, paixão e performance.”  
> — Equipe RSP Diecast © 2025
