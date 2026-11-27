# Jampa Condomínio

Sistema web responsivo e PWA para gestão de condomínios.

## Módulos previstos

- Acessos e controle de portas/câmeras
- Visitantes e QR Code
- Encomendas
- Reservas e aprovação
- Comunicados
- Chamados com comentários e privacidade
- Financeiro
- Usuários e permissões
- Unidades, portas e câmeras

## Implantação

Pré-requisitos:

- Docker
- Rede externa do Traefik chamada `rede`
- DNS apontando para o servidor

```bash
cp .env.example .env
# configure APP_KEY e credenciais seguras
docker compose up -d --build
```

O sistema ficará disponível em `https://condominio.jampasolucoes.com.br`.
