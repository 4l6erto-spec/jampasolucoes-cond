# Jampa Condomínio

Sistema web responsivo para gestão de condomínios. Este commit contém a base executável em Laravel 12, PostgreSQL, Redis, Nginx e Docker Compose integrado à rede externa `rede` do Traefik. Os módulos funcionais serão implementados por etapas.

## Implantação

1. Clone o repositório no diretório do serviço e entre nele.
2. Verifique que a rede externa `rede` já existe (`docker network ls`).
3. Copie e edite as variáveis de ambiente:

   ```bash
   cp .env.example .env
   nano .env
   ```

   Configure uma senha forte em `DB_PASSWORD`. Não publique nem compartilhe o `.env`.
4. Suba os serviços e gere uma chave Laravel:

   ```bash
   docker compose up -d --build
   docker compose exec app php artisan key:generate --force
   docker compose exec app php artisan migrate --force
   ```
5. Confira o estado e os logs:

   ```bash
   docker compose ps
   docker compose logs --tail=100 app web db
   ```

O host Traefik precisa ter acesso à rede `rede`, e o DNS de `condominio.jampasolucoes.com.br` deve apontar para o servidor.

## Estado atual

A página inicial é um painel visual inicial. Autenticação e módulos de negócio ainda não estão implementados; as caixas apresentadas são apenas o mapa do produto.
