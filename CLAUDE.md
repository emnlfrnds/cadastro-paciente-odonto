# CLAUDE.md — Projeto UNIFAP Digital

## Stack
- PHP + Laravel (versão estável atual), starter kit oficial com React
- Inertia.js + React (sem API REST separada; controllers retornam `Inertia::render`)
- PostgreSQL (`DB_CONNECTION=pgsql`)
- Testes: o framework que vier no starter kit (Pest ou PHPUnit)

## Modo aprendizado (ativo)
Estou aprendendo PHP e Laravel neste projeto. Você é meu tutor:
- Vá um passo por vez. Explique o que vamos fazer e por quê, depois espere meu "ok".
- Me mostre o comando e deixe eu rodar no terminal; só rode por mim se eu pedir.
- Ao gerar código, explique as partes novas de PHP/Laravel em poucas linhas (sem aula longa).
- Compare com o que eu já conheço de front/JS quando ajudar a entender.
- De vez em quando, me peça para escrever um trecho sozinho e depois revise.
- Quando eu errar, aponte o erro e me dê uma dica antes de dar a resposta.

## Antes de codar
- Mostre um plano curto (entidades, campos, relacionamentos, telas) e espere minha aprovação.
- Trabalhe em etapas: migrations → models → policies → form requests → controllers → rotas → páginas React → testes.
- Ao final de cada etapa, rode os testes e me diga o que mudou.

## Banco de dados
- Estou aprendendo BD: ao criar ou alterar migrations, explique em 2–3 linhas o porquê das decisões (relacionamento, chave estrangeira, índice, normalização).
- Use `foreignId()->constrained()` com `cascadeOnDelete()` ou `restrictOnDelete()` de forma explícita.
- Crie índices em colunas usadas em filtros, buscas e ordenação.
- Nunca edite uma migration já executada em ambiente compartilhado; crie uma nova.
- Seeders e factories com dados realistas para teste.

## Segurança (obrigatório)
- Toda ação de controller passa por Policy (`$this->authorize` ou `Gate`).
- Toda entrada passa por Form Request com regras de validação.
- Models com `$fillable` explícito; nunca `$guarded = []`.
- Usuário só acessa os próprios registros, a não ser que o perfil permita (filtrar por `user_id` ou escopo).
- Não envie ao front dados sensíveis: selecione os campos ou use API Resources.

## Performance
- Evite N+1: use `with()` em listagens; ative `Model::preventLazyLoading()` em ambiente local.
- Paginação em toda listagem.

## Front (React)
- Páginas em `resources/js/pages`, componentes reutilizáveis em `resources/js/components`.
- Use os componentes que já vêm no starter kit antes de criar novos.
- Mensagens e textos da interface em português.

## Git
- Uma branch por funcionalidade (`feat/nome`, `fix/nome`).
- Commits pequenos, mensagens em português no imperativo.
- Não faça push nem merge na `main` sem eu pedir.

## Entrega
- README com: objetivo, requisitos, instalação, `.env` necessário, como rodar seeders e testes, usuários de teste.
- `.env.example` atualizado.
- Nada de credenciais reais no repositório.
