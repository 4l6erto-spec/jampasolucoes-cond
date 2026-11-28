# Implantação e uso

A aplicação exige autenticação. Para disponibilizar o primeiro acesso, crie um usuário administrador pelo seeder ou pelo console Tinker e defina uma senha segura; não use credenciais padrão em produção.

## Módulos operacionais
- Visitantes: morador autoriza visitante; portaria valida token em `/portaria/visitantes/{token}` e registra entrada/saída. O endereço de validação pode ser usado como destino de um QR Code.
- Reservas: solicitações aguardam aprovação do administrador e impedem sobreposição de horários para o mesmo espaço.
- Comunicados: publicação imediata/agendada, prioridade e expiração.
- Chamados: morador abre e acompanha seus chamados; administração atualiza status; comentários ficam no histórico.
- Encomendas: portaria registra recebimento e registra retirada pela unidade.
- Acessos: portaria/admin pode registrar movimentações; validações de visitantes e encomendas geram registros de auditoria.

Após atualizar o código no servidor:

```bash
docker compose up -d --build
docker compose exec app php artisan migrate --force
docker compose exec app php artisan optimize:clear
```
