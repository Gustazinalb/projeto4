# Skill: MySQL Database

## Objetivo
Orientar alterações e consultas no MySQL/MariaDB do Brasa-Bar.

## Contexto conhecido
- Banco local: `proj`.
- Tabelas existentes incluem categorias, produtos, eventos,
  evento_produto, usuarios, mesas e reservas.
- Produtos relacionam-se com categorias.
- evento_produto relaciona eventos e produtos.
- Reservas relacionam usuários e mesas.

## Diretrizes
- Verificar nomes reais de tabelas e colunas antes de escrever SQL.
- Usar consultas preparadas para valores fornecidos por usuários.
- Preservar chaves primárias, estrangeiras, índices e restrições UNIQUE.
- Evitar consultas que permitam SQL injection.
- Não presumir que um registro existe sem verificar.
- Evitar exclusões que deixem relacionamentos inválidos.
- Explicar o propósito de scripts SQL e seus efeitos.
- Não executar DROP, TRUNCATE ou exclusões em massa sem autorização.
- Não acessar nem expor credenciais de conexão.

## Verificação
- Conferir a sintaxe SQL.
- Testar consultas com dados controlados.
- Verificar os relacionamentos e os resultados antes de considerar
  uma alteração concluída.
