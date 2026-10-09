# Skill: Authentication and Security

## Objetivo
Orientar a segurança da autenticação e das rotas do Brasa-Bar.

## Diretrizes
- Proteger rotas privadas com a autenticação JWT existente.
- Não aceitar token ausente, inválido, expirado ou com assinatura
  incorreta.
- Obter a chave JWT do ambiente; nunca codificar a chave no PHP.
- Nunca revelar tokens, senhas, chaves secretas ou conteúdo do `.env`.
- Usar password_hash() para armazenar senhas e password_verify()
  para verificar senhas.
- Validar e-mail, CPF e requisitos de senha no servidor.
- Garantir que a edição de usuário utilize o identificador do token
  e não permita editar outro usuário.
- Impedir alteração de e-mail quando essa for a regra do sistema.
- Não confiar apenas em validações feitas no frontend.
- Não registrar senhas ou tokens em logs.
- Retornar mensagens de erro seguras sem detalhes internos.

## Verificação
- Testar login válido e inválido.
- Testar rota protegida sem token e com token inválido.
- Testar expiração e assinatura do JWT quando aplicável.
- Confirmar que senhas não são armazenadas em texto puro.
