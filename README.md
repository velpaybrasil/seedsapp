# SeedsApp - Sistema de Gestão para Igrejas

Sistema web para gerenciamento de igrejas, incluindo controle de membros, visitantes, grupos e eventos.

## Estrutura do Projeto

```
seedsapp/
├── app/                    # Lógica da aplicação
│   ├── controllers/       # Controladores
│   ├── models/           # Modelos
│   └── services/         # Serviços e regras de negócio
├── config/                 # Configurações
│   └── config.php        # Configurações gerais
├── database/              # Scripts do banco de dados
│   └── migrations/       # Migrações
├── public/                # Arquivos públicos
│   ├── index.php         # Ponto de entrada
│   ├── login.php         # Página de login
│   ├── dashboard.php     # Dashboard principal
│   └── logout.php        # Logout
└── views/                 # Templates e views
    └── components/       # Componentes reutilizáveis
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
CREATE DATABASE gcmanager;
USE gcmanager;
source database/schema.sql;
```

3. Configure o arquivo config/config.php:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'gcmanager');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
```

4. Credenciais padrão:
- Email: admin@gcmanager.com
- Senha: admin123

## Desenvolvimento

Para adicionar novas funcionalidades:

1. Crie o modelo em app/models/
2. Crie o controlador em app/controllers/
3. Crie a view em views/
4. Atualize as rotas em public/index.php

## Deploy

Para fazer deploy em produção:

1. Configure o arquivo .htaccess
2. Ajuste as permissões:
```bash
chmod -R 755 .
chmod -R 777 storage/
```

3. Configure o banco de dados de produção

## Segurança

- Todas as senhas são hasheadas com password_hash()
- Proteção contra SQL Injection usando PDO
- Proteção contra XSS usando htmlspecialchars()
- Sessões seguras com httponly e secure flags

## Suporte

Para suporte, entre em contato através do email: suporte@gcmanager.com
