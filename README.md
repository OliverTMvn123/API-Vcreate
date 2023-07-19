### WELCOME TO VCREATE!

### REQUIREMENTS

php >= 8.1

apache

mysql >= 8.0

npm

install php extension:

```sudo apt install php8.1-common php8.1-mysql php8.1-xml php8.1-xmlrpc php8.1-curl php8.1-gd php8.1-imagick php8.1-cli php8.1-dev php8.1-imap php8.1-mbstring php8.1-opcache php8.1-soap php8.1-zip php8.1-intl -y```

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
