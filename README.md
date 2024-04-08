## Importação de CSV com Correspondência de Campos no Laravel

Um projeto de demonstração com Laravel 8 e o pacote Laravel Excel, para importar arquivo CSV e escolher os campos do banco de dados para corresponder à coluna do CSV.

### Como usar

Clone este projeto para o seu computador local.

```ps
git clone https://github.com/EDU3RDOBR/Laravel-Import-CSV

```

Navegue até a pasta do projeto.

```ps
cd Laravel-Import-CSV
```

Instale os pacotes necessários.

```ps
composer install
```

Crie um novo arquivo .env e edite as credenciais do banco de dados lá.

```ps
cp .env.example .env
```

Gere uma nova chave de aplicativo.

```ps
php artisan key:generate
```

Execute as migrações. (ele contém alguns dados semeados para seus testes)

```ps
php artisan migrate --seed
```

Isso é tudo: inicie a URL principal.

# Principal
# Principal
