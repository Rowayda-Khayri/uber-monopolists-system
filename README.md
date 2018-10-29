## Server Requirements:

- PHP >= 7.1.3
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Ctype PHP Extension
- JSON PHP Extension

## Deployment Instructions:

- First, you should clone this repo on your document root.

- Run the following command:

```
composer update
```
- Rename .env.example to .env and run the following command:  
```
php artisan key:generate
```
- Create a database and add it to .env:
```
DB_DATABASE=db_name
DB_USERNAME=db_username
DB_PASSWORD=db_password
```
- Now, your app is ready to use:
```
php artisan serve
```
- Run migrations:
```
php artisan migrate
```
- Run Seeds:
```
php artisan db:seed
```
## Web Services:
```
|      Resource        | Method |               Parameters                       |           Headers              |
| -------------------- | ------ | ---------------------------------------------- | ------------------------------ |
| /register            | POST   |*email, *password, *password_confirmation, name |                &nbsp;          |
| /login               | POST   |*email, *password                               |          &nbsp;                |
| /logout              | GET    |         &nbsp;                                 | Authorization : Bearer <TOKEN> |
| /trip/accept         | POST   |               &nbsp;                           | Authorization : Bearer <TOKEN> |
| /monopolists/{time}  | GET    |                     &nbsp;                     |    &nbsp;                      |




