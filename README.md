## JWT Sample - Symfony 5.1

Standard symfony install.

[Symfony CLI](https://symfony.com/download)

[Composer](https://getcomposer.org/download/)

Ensure node and yarn are installed.

Create your database and create file ``/.env.local``

Replace \[SOME_SECRET_KEY\] with a unique string

Replace \[DB_USERNAME\], \[DB_HOST\], \[DB_NAME\] with your Db information

```
// .env.local

###> symfony/framework-bundle ###
APP_ENV=dev
APP_SECRET=[SOME_SECRET_KEY_HERE]
#TRUSTED_PROXIES=127.0.0.0/8,10.0.0.0/8,172.16.0.0/12,192.168.0.0/16
#TRUSTED_HOSTS='^(localhost|example\.com)$'
###< symfony/framework-bundle ###

###> doctrine/doctrine-bundle ###
DATABASE_URL=mysql://[DB_USERNAME]:[DB_PASSWORD]@[DB_HOST]/[DB_NAME]?serverVersion=5.7
###< doctrine/doctrine-bundle ###

```

In the root folder

```bash
// install vendors, node_modules, setup assets and db
symfony composer install
yarn install
symfony console assets:install --symlink
symfony console doctrine:database:create
symfony console doctrine:schema:update --force

// Get webpack to build
yarn build

// Start the build in server
symfony server:start
```