# Planejamento do domínio — Sistema de Cadastro de Pacientes (Odonto)

Este documento registra as decisões de modelagem tomadas antes de escrever qualquer migration. Ele é a referência para saber o que o sistema guarda, como as tabelas se ligam e quem pode fazer o quê. Se uma decisão mudar, atualize este arquivo no mesmo commit.

## 1. Objetivo

Sistema fechado para uma clínica odontológica: dentistas e o dono cadastram pacientes, agendam consultas e acompanham o status delas. **Paciente nunca acessa o sistema.**

## 2. Perfis de acesso

| Perfil | Quem é | Acesso |
|---|---|---|
| `admin` | Dono da clínica | Tudo. Também pode atender como dentista (ver seção 9). |
| `dentista` | Profissional que atende | Apenas os **seus** pacientes e as **suas** consultas. |

- Não existe cadastro público. O `admin` cria as contas dos dentistas.
- Um paciente pertence a **um único dentista** (o responsável).

## 3. Entidades e campos

### `users` (já existe, vem do starter kit)

Acrescentar:

| Campo | Obrigatório | Observação |
|---|---|---|
| `perfil` | sim | `admin` ou `dentista` |

### `pacientes`

| Campo | Obrigatório | Observação |
|---|---|---|
| nome | sim | Campo separado do sobrenome |
| sobrenome | sim | |
| data de nascimento | sim | A idade é **calculada**, nunca guardada |
| bairro | sim | Região onde mora (sem rua e número, por minimização de dados) |
| telefone | sim | Também serve para confirmar a identidade do paciente |
| `dentista_id` | sim | Dentista responsável (aponta para `users`) |

Um paciente só existe depois de ter ido à clínica e marcado uma consulta. Quem só ligou para tirar dúvida não é cadastrado.

### `procedimentos`

| Campo | Obrigatório | Observação |
|---|---|---|
| nome | sim | Único |
| `status` | sim | `pendente`, `aprovado` ou `recusado` |
| `solicitado_por` | não | Dentista que pediu. Vazio nos procedimentos iniciais |

Procedimentos iniciais (seeder, já aprovados): **Limpeza** e **Restauração**.

Fluxo: o dentista solicita um procedimento novo (`pendente`), e o `admin` aprova ou recusa. Só os `aprovados` aparecem para escolha numa consulta.

### `consultas`

| Campo | Obrigatório | Observação |
|---|---|---|
| `paciente_id` | sim | |
| `dentista_id` | sim | Quem atendeu. Fica na consulta para manter o histórico caso o paciente troque de dentista |
| início | sim | Data e hora. O término é **calculado** (início + 1h30), não guardado |
| `status` | sim | `agendada`, `confirmada`, `realizada`, `cancelada` ou `faltou` |
| observações | não | Texto livre |

### `consulta_procedimento` (tabela de ligação)

| Campo | Obrigatório | Observação |
|---|---|---|
| `consulta_id` | sim | |
| `procedimento_id` | sim | |

Uma consulta pode ter vários procedimentos, e um procedimento aparece em várias consultas.

## 4. Relacionamentos

- Um dentista (`users`) tem **muitos** pacientes. Cada paciente tem **um** dentista (1:N).
- Um paciente tem **muitas** consultas (1:N).
- Um dentista tem **muitas** consultas (1:N).
- Consultas e procedimentos: **muitos para muitos**, por meio de `consulta_procedimento`.
- Um dentista pode **solicitar** vários procedimentos (1:N, campo `solicitado_por`).

## 5. Regras de negócio

1. **Duração fixa:** toda consulta dura **1h30**, para todos os dentistas.
2. **Sem conflito de horário:** um dentista não pode ter duas consultas no mesmo horário.
3. **Cancelada libera o horário:** consultas com status `cancelada` não contam para o conflito. Será implementado com um **índice único parcial** no PostgreSQL (ver seção 8, decisão pendente sobre sobreposição).
4. **Idade calculada:** a idade vem da data de nascimento.
5. **Dados mínimos (LGPD):** não se guardam CPF nem e-mail do paciente. A confirmação de identidade é feita por nome e telefone, e o contato é feito por WhatsApp fora do sistema.

## 6. Quem pode o quê (viram Policies)

| Ação | `admin` | `dentista` |
|---|---|---|
| Ver, cadastrar e editar pacientes | todos | só os seus |
| Ver, agendar e editar consultas | todas | só as suas |
| Mudar status da consulta | todas | só das suas |
| Ver procedimentos | todos | só os aprovados |
| Solicitar procedimento | sim | sim |
| Aprovar ou recusar procedimento | sim | não |
| Criar e gerenciar usuários | sim | não |

## 7. Telas

- Login (já existe no starter kit).
- Dashboard: agenda do dia para o dentista, painel geral para o `admin`.
- Pacientes: listar (com paginação), cadastrar, editar.
- Consultas: listar, agendar, mudar status.
- Procedimentos: listar e solicitar. Fila de aprovação só para o `admin`.
- Usuários: só para o `admin`.

## 8. Decisões pendentes

1. **Como garantir que duas consultas não se sobreponham.** Com duração fixa de 1h30, um índice único em (`dentista_id`, início) só impede o **mesmo horário de início**, e não impede 10h00 e 10h30. Caminhos possíveis:
   - Validar a sobreposição no Form Request e manter o índice único parcial como segunda barreira.
   - Limitar os horários de início a uma grade fixa (por exemplo, a cada 1h30), o que faz o índice único bastar.
   - Usar uma restrição de exclusão do PostgreSQL com intervalo de tempo (mais avançado).
   Decidir ao chegar na migration de `consultas`.

## 9. Fora do escopo por enquanto

- Botão para o `admin` alternar entre o painel administrativo e a visão de dentista. Será um acabamento de interface no fim, sem mudança no banco.
- Preço e duração variável de procedimentos.
- CPF, e-mail e endereço completo do paciente.

## 10. Ordem de implementação

Seguindo o `CLAUDE.md`: migrations → models → policies → form requests → controllers → rotas → páginas React → testes.

Migrations, na ordem das dependências:
1. Campo `perfil` em `users`
2. `pacientes`
3. `procedimentos`
4. `consultas`
5. `consulta_procedimento`
