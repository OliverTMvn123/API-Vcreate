### WELCOME TO VCREATE!

### REQUIREMENTS

php >= 7.4

apache

mysql >= 5.7

npm

install php extension:

```sudo apt install php7.4-common php7.4-mysql php7.4-xml php7.4-xmlrpc php7.4-curl php7.4-gd php7.4-imagick php7.4-cli php7.4-dev php7.4-imap php7.4-mbstring php7.4-opcache php7.4-soap php7.4-zip php7.4-intl -y```

### PROJECT SETUP

```git clone git@gitlab.com:chmh.dev/vcreate.git```

```composer install```

```update smtp mailer in env file```

```update db credentials in env file```

```set  APP_NAME in env file```

#### CREATE DATABASE:

```mysql -uroot```

```CREATE DATABASE vcreate CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;```

#### RUN MIGRATIONS:

```php artisan migrate```

### You can start project in localhost:

```php artisan serve```

project home page:

```http://127.0.0.1:8000```
