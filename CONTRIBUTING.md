# 🏁 Guia de Contribuição – RSP Diecast / Coleção Racing

Antes de enviar qualquer contribuição, leia este documento com atenção.  
Ele explica como sugerir melhorias, reportar problemas e colaborar com o código do projeto.

---

## 🚀 Objetivo

O **RSP Diecast** é um sistema open source voltado para o **cadastro e análise de carros de competição e miniaturas**, com arquitetura Laravel, design moderno e foco em performance.  
Queremos manter um ambiente colaborativo, técnico e respeitoso.

---

## 📦 Estrutura do Projeto

```
rsp-diecast/
├── app/ # Código backend (Laravel)
├── resources/ # Frontend (Blade + Tailwind)
├── database/ # Migrations e seeders
├── docs/ # Documentação e imagens
├── routes/ # Definição das rotas web/API
├── tests/ # Testes unitários e funcionais
├── README.md # Descrição do projeto
└── RSP-Diecast-Relatorio-Tecnico.md # Guia técnico completo
```
---


---

## 🧩 Como Contribuir

### 1️⃣ Crie um fork do projeto
- Vá até o repositório: [https://github.com/seuusuario/rsp-diecast](https://github.com/seuusuario/rsp-diecast)
- Clique em **Fork** no canto superior direito.

### 2️⃣ Clone seu fork localmente
```bash
git clone https://github.com/seuusuario/rsp-diecast.git
cd rsp-diecast

# 🏁 Guia de Contribuição – RSP Diecast / Coleção Racing

Antes de enviar qualquer contribuição, leia este documento com atenção.  
Ele explica como sugerir melhorias, reportar problemas e colaborar com o código do projeto.

---

## 🚀 Objetivo

O **RSP Diecast** é um sistema open source voltado para o **cadastro e análise de carros de competição e miniaturas**, com arquitetura Laravel, design moderno e foco em performance.  
Queremos manter um ambiente colaborativo, técnico e respeitoso.

---

## 📦 Estrutura do Projeto

rsp-diecast/
├── app/ # Código backend (Laravel)
├── resources/ # Frontend (Blade + Tailwind)
├── database/ # Migrations e seeders
├── docs/ # Documentação e imagens
├── routes/ # Definição das rotas web/API
├── tests/ # Testes unitários e funcionais
├── README.md # Descrição do projeto
└── RSP-Diecast-Relatorio-Tecnico.md # Guia técnico completo

---

## 🧩 Como Contribuir

### 1️⃣ Crie um fork do projeto
- Vá até o repositório: [https://github.com/seuusuario/rsp-diecast](https://github.com/seuusuario/rsp-diecast)
- Clique em **Fork** no canto superior direito.

### 2️⃣ Clone seu fork localmente
```bash
git clone https://github.com/seuusuario/rsp-diecast.git
cd rsp-diecast
3️⃣ Crie uma branch para sua contribuição
bash
Copy code
git checkout -b feature/nome-da-funcionalidade
4️⃣ Faça suas alterações
Mantenha o padrão de código PSR-12 para PHP.

Utilize nomes claros e sem acentos em arquivos e variáveis.

Siga a estrutura e convenções já existentes no projeto.

5️⃣ Teste antes de enviar
Execute:

bash
Copy code
php artisan test
Garanta que tudo esteja funcionando antes de abrir o pull request.

6️⃣ Envie o pull request
bash
Copy code
git push origin feature/nome-da-funcionalidade
Depois, abra um Pull Request no repositório principal.
Descreva claramente o que foi alterado e o motivo.

🧱 Padrão de Commits
Siga este padrão para manter o histórico limpo e legível:

Tipo	Descrição	Exemplo
feat	Nova funcionalidade	feat: adiciona filtro por categoria no dashboard
fix	Correção de bug	fix: corrige upload de imagem duplicada
docs	Alteração na documentação	docs: atualiza README
style	Formatação, espaçamento, etc.	style: ajusta identação do controller
refactor	Refatoração de código	refactor: otimiza consulta SQL no relatório
test	Adição de testes	test: adiciona teste para exportação XLSX

⚙️ Padrões de Código
PHP 8.4 – padrão PSR-12

Tailwind CSS – classes utilitárias

HTML semântico (Blade)

Commits pequenos e descritivos

Código comentado apenas quando necessário

🐛 Reportar Problemas
Se encontrar um bug, crie uma Issue com:

Descrição do problema

Passos para reproduzir

Comportamento esperado

Prints ou logs, se possível

Use o template de issue disponível no repositório.

🤝 Código de Conduta
Respeite todos os colaboradores.
Não serão tolerados comportamentos abusivos, ofensivos ou discriminatórios.
Nosso objetivo é manter uma comunidade inclusiva, técnica e colaborativa.

🧠 Dúvidas e Contato
Para dúvidas sobre o projeto ou sugestões:
📧 contato@rspdiecast.com.br
🌐 https://rspdiecast.com.br

💙 Agradecimento
Obrigado por fazer parte do desenvolvimento do RSP Diecast / Coleção Racing.
Sua contribuição ajuda a tornar este projeto mais completo e acessível a toda a comunidade de colecionadores e entusiastas da velocidade.

“Construindo tecnologia com precisão, paixão e performance.” 🏎️