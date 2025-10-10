# Teste prático para Back-End — Implementação

Este repositório contém uma implementação em Laravel 11 com Blade (SPA-like) para gerenciar Produtos e Categorias, com autenticação via Sanctum (cookies stateful) e importação a partir da FakeStore API.

Principais tecnologias: Laravel 11, Sanctum, Blade + Tailwind, Nginx, MySQL, Docker.

## Funcionalidades

- Autenticação (login, registro, logout) e edição de perfil (nome, e-mail, telefone, senha)
- Produtos: CRUD com validação, soft delete, filtros (nome, categoria, com/sem imagem), ordenação determinística, detalhe
- Categorias: CRUD com validação
- Importação: comando `php artisan products:import` (todos) e `--id=<ext_id>` (um); endpoints `POST /api/import/all` e `POST /api/import/{id}` com deduplicação e restauração de soft-deletes
- Dashboard: métricas via service com checagem de schema (evita erros em migrações)
- Testes: suite PHPUnit cobrindo flows principais

## Como executar com Docker (Makefile)

Pré-requisitos: Docker e Docker Compose.

1) Subir e configurar tudo de uma vez:

```
make setup
```

O `setup` cria .env (se precisar), sobe containers, instala deps, gera APP_KEY e migra. E cria um servidor em:

- http://localhost:8080

Comandos úteis:
- `make up` / `make down` / `make destroy`
- `make migrate` / `make seed` / `make fresh`
- `make test`
- `make import` ou `make import-one ID=1`
- `make logs` / `make app-logs` / `make web-logs`

---

