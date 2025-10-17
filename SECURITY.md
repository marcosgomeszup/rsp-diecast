# 🔐 Política de Segurança – RSP Diecast / Coleção Racing

---

## 🧭 Visão Geral

A equipe do **RSP Diecast / Coleção Racing** leva a **segurança da aplicação e dos dados dos usuários** muito a sério.  
Nosso compromisso é manter o sistema livre de vulnerabilidades conhecidas, aplicando correções rápidas e comunicando de forma transparente qualquer incidente relevante.

---

## 🛡️ Relato de Vulnerabilidades

Se você identificar uma falha de segurança, **não abra uma issue pública**.  
Em vez disso, envie um e-mail diretamente para:

📧 **security@rspdiecast.com.br**

Por favor, inclua:
- Uma descrição detalhada da vulnerabilidade;  
- Passos para reproduzi-la;  
- Qualquer código de prova de conceito, se possível;  
- Informações sobre o ambiente (versão, navegador, SO, etc.);  
- Seu nome e contato (opcional).

Você receberá uma confirmação em até **48 horas úteis**.

---

## ⚙️ Processo de Correção

1. O time técnico avaliará a vulnerabilidade.  
2. Será definida a gravidade (baixa, média, alta ou crítica).  
3. A correção será implementada e testada internamente.  
4. Caso a falha afete versões públicas, uma atualização será liberada e documentada.  
5. O colaborador que reportou o problema poderá ser **creditado** na nota de versão, se desejar.

---

## 🧩 Boas Práticas Recomendadas

Para contribuir de forma segura, recomendamos:

- **Não incluir chaves ou tokens** em commits (use variáveis de ambiente no `.env`);  
- **Evitar dependências desatualizadas** (mantenha `composer.json` e `package.json` atualizados);  
- **Verificar permissões de diretórios** no ambiente de produção (especialmente `/storage` e `/public`);  
- **Proteger endpoints sensíveis** com autenticação e CSRF tokens;  
- **Usar HTTPS** em todos os acessos à aplicação.

---

## 🔄 Atualizações de Segurança

Boletins de segurança e correções críticas serão publicados nas **releases oficiais** do GitHub:  
📦 [https://github.com/seuusuario/rsp-diecast/releases](https://github.com/seuusuario/rsp-diecast/releases)

---

## 📅 Ciclo de Revisão

A política de segurança é revisada **a cada 6 meses** ou sempre que houver atualizações relevantes no ecossistema Laravel / PHP / MySQL.

---

## 🧠 Referências

- [Laravel Security Best Practices](https://laravel.com/docs/master/security)  
- [OWASP Top 10 Web Application Risks](https://owasp.org/www-project-top-ten/)  
- [GitHub Security Advisories](https://docs.github.com/en/code-security)

---

## 🏁 Contato Direto

**Equipe de Segurança RSP Diecast**  
📧 security@rspdiecast.com.br  
🌐 [https://rspdiecast.com.br](https://rspdiecast.com.br)

---

> “Segurança é performance invisível — quando tudo funciona, é porque ela está lá.”  
> — Equipe RSP Diecast © 2025
