# Skill: PHP API

## Objetivo
Orientar a criação e manutenção das APIs PHP do Brasa-Bar.

## Diretrizes
- Preservar o padrão e a estrutura existentes em `api/`.
- Retornar JSON consistente com `Content-Type: application/json`.
- Utilizar códigos HTTP adequados para sucesso, validação,
  autenticação, recurso inexistente e erro interno.
- Validar os dados recebidos antes de executar operações no banco.
- Utilizar consultas preparadas com parâmetros.
- Tratar exceções sem expor detalhes internos ou credenciais ao cliente.
- Validar campos obrigatórios, tipos, limites e identificadores.
- Nas listagens paginadas, validar página e limite e impor limite máximo.
- Em atualizações e exclusões, confirmar que o recurso existe.
- Respeitar relacionamentos e impedir operações que violem integridade.
- Não modificar APIs existentes sem compreender seus consumidores.

## Verificação
- Executar `php -l` nos arquivos alterados.
- Testar entradas válidas e inválidas.
- Conferir os códigos HTTP e a estrutura JSON das respostas.
