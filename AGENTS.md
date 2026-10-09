# Instruções dos agentes — Brasa-Bar

## Contexto do projeto
- Sistema Brasa-Bar desenvolvido principalmente em PHP, MySQL/MariaDB,
  HTML, CSS e TypeScript.
- O backend está na pasta `api/`.
- A conexão com o banco é configurada em `config.php`.
- O banco local utilizado pelo projeto chama-se `proj`.
- O ambiente local utiliza XAMPP.
- Respeitar a estrutura atual e as responsabilidades de cada arquivo.

## Regras obrigatórias
1. Antes de alterar código, identificar os arquivos envolvidos e explicar
   brevemente o objetivo da mudança.
2. Fazer alterações pequenas e compatíveis com o código existente.
3. Não reescrever o projeto nem trocar tecnologias sem autorização.
4. Não modificar arquivos do frontend quando a tarefa for exclusivamente
   de backend.
5. Não remover funcionalidades, tabelas, registros ou relacionamentos
   sem autorização explícita.
6. Validar a sintaxe PHP dos arquivos modificados.
7. Para alterações no banco, explicar o SQL e preservar a integridade
   referencial.
8. Não afirmar que um teste passou sem executar ou receber evidência dele.
9. Não expor mensagens internas, senhas, tokens ou credenciais nas
   respostas da API.
10. Ao finalizar, listar os arquivos alterados e os testes necessários.

## Segurança e guardrails
- Nunca abrir, ler, imprimir, copiar ou revelar o conteúdo de `.env`.
- Nunca revelar valores de variáveis de ambiente, senhas, chaves JWT,
  tokens ou credenciais.
- Não executar comandos como `cat .env`, `type .env` ou equivalentes.
- Não incluir segredos em logs, respostas HTTP, documentação ou commits.
- Não remover proteções de autenticação para facilitar testes.
- Não desativar validações, chaves estrangeiras ou controles de acesso
  sem autorização explícita.
- Não executar exclusões em massa, DROP, TRUNCATE ou redefinições do
  banco sem autorização explícita.
- Não enviar arquivos sensíveis para serviços externos.
- Se uma tarefa exigir acesso a um segredo, utilizar a variável de
  ambiente sem revelar seu valor.

## Testes mínimos
- Executar `php -l` nos arquivos PHP modificados.
- Testar respostas de sucesso e erro das rotas alteradas.
- Confirmar que rotas protegidas rejeitam requisições sem autenticação.
- Não usar credenciais reais em exemplos ou documentação.
