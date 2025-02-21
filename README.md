# SeedsApp - Sistema de Gestão para Igrejas

Sistema web para gerenciamento de igrejas, incluindo controle de membros, visitantes, grupos e eventos.

## Estrutura do Projeto

```
seedsapp2/
├── app/                    # Lógica da aplicação
│   ├── controllers/       # Controladores
│   ├── models/           # Modelos
│   └── services/         # Serviços e regras de negócio
├── config/                 # Configurações
│   └── config.php        # Configurações gerais
├── database/              # Scripts do banco de dados
│   └── migrations/       # Migrações
├── public/                # Arquivos públicos
│   ├── css/             # Estilos CSS
│   ├── js/              # Scripts JavaScript
│   ├── images/          # Imagens e recursos
│   └── index.php        # Ponto de entrada
└── views/                 # Templates e views
    ├── components/       # Componentes reutilizáveis
    ├── dashboard/       # Views do dashboard
    ├── groups/          # Views de grupos
    ├── ministries/      # Views de ministérios
    └── visitors/        # Views de visitantes
```

## Requisitos

- PHP 8.2 ou superior
- MySQL 5.7 ou superior
- Apache/Nginx com mod_rewrite

## Instalação

1. Clone o repositório:
```bash
git clone https://github.com/velpaybrasil/seedsapp.git
```

2. Configure o banco de dados:
```sql
CREATE DATABASE u315624178_gcmanager;
USE u315624178_gcmanager;
source database/migrations/*.sql;
```

3. Configure as credenciais no arquivo .env:
```env
DB_HOST=localhost
DB_NAME=u315624178_gcmanager
DB_USER=u315624178_gcmanager
DB_PASS=sua_senha
```

## Módulos Principais

### 1. Gestão de Grupos
- CRUD completo de Grupos de Crescimento
- Atribuição de líderes e co-líderes
- Visualização rápida via modal
- Interface moderna e responsiva

### 2. Gestão de Visitantes
- Cadastro de visitantes (formulário público)
- Acompanhamento de visitantes
- Associação com Grupos de Crescimento
- Sistema de pré-inscrição em grupos

### 3. Gestão de Ministérios
- Cadastro de ministérios
- Atribuição de líderes
- Controle de membros

### 4. Sistema de Permissões
- Controle por papéis (Administrador, Líder, Membro)
- Permissões por módulo
- Gestão de acessos granular

## Páginas Públicas

### Cadastro de Visitantes
Página pública para cadastro de novos visitantes. Pode ser hospedada em qualquer local do servidor.

**Arquivo:** `views/visitors/register.php`

**Funcionalidades:**
- Formulário de cadastro com validação
- Campos: Nome, Gênero, Estado Civil, WhatsApp, E-mail, Bairro, Cidade
- Opção para interesse em participar de grupos
- Campo para pedidos de oração
- Design responsivo com Bootstrap 5
- Integração com a marca da igreja

**Configuração:**
1. Copie o arquivo para o diretório desejado
2. Ajuste as credenciais do banco de dados no início do arquivo:
```php
$db_host = 'localhost';
$db_name = 'u315624178_gcmanager';
$db_user = 'u315624178_gcmanager';
$db_pass = 'sua_senha';
```

**Personalização:**
- Logo da igreja no topo
- Link de suporte via WhatsApp no rodapé
- Estilo visual moderno e profissional

## Próximas Atualizações

1. **Sistema de Pré-inscrição**
   - Interface para líderes aprovarem/rejeitarem pré-inscrições
   - Notificações de novas pré-inscrições
   - Histórico de membros do grupo

2. **Dashboard com Estatísticas**
   - Visão geral dos grupos
   - Métricas de crescimento
   - Relatórios personalizados

3. **Sistema de Notificações**
   - Notificações para líderes
   - E-mails automáticos
   - Lembretes de eventos

## Suporte

Para suporte técnico:
- WhatsApp: (85) 99763-7850
- Email: suporte@seedsapp.com.br

## Licença

Este projeto é proprietário e seu uso é restrito aos clientes da SeedsApp.
