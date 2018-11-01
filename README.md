 Uber drivers are very eager to achieve the best targets in number of trips. It is a
good thing for Uber and for the drivers. But Uber is a well developed and
educated company that wants to know if some of the drivers are monopolists in
this competition. So that Uber decided to create a system that will detect and
display drivers who have made more than or equal to 10% of the trips. It also
wants this information on three levels; who exceeded 10% this month, this year,
and all time.

 They want to maintain a LIVE list so that anyone opens Uber website will see the
updated list.

 There are some requirements for this goal to be achieved:

 1. Uber needs a web service that the driver app will use to inform the system that
the driver made another trip. It uses POST method and it doesn't need any
parameters. Each call to this service means another trip for the calling driver. This
web function can only be accessed using the driver's credentials.
2. Another web service is needed that gets the current list of monopolists.
 Features:

- List of drivers exceeding 10% of the trips this month
- List of drivers exceeding 10% of the trips this year
- List of drivers exceeding 10% of the trips all time


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

- Install Composer dependencies:

```
composer install
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
- Create a Pusher app and add its credentials to .env:
```
PUSHER_KEY=xxxxxxxxxxxxxxxxxxxx
PUSHER_SECRET=xxxxxxxxxxxxxxxxxxxx
PUSHER_APP_ID=xxxxxx
```
- Now, your app is ready to use:
```
php artisan serve
```
- Run migrations:
```
php artisan migrate
```
- Seed the database:
```
php artisan db:seed
```
## Web Services:

|      Route        | Method |                Parameters                       |           Headers              |
| -------------------- | ------ | ----------------------------------------------- | ------------------------------ |
| /register            | POST   | *email, *password, *password_confirmation, name |                &nbsp;          |
| /login               | POST   | *email, *password                               |          &nbsp;                |
| /logout              | GET    |          &nbsp;                                 | Authorization : Bearer &lt;TOKEN&gt; |
| /trip/accept         | POST   |                &nbsp;                           | Authorization : Bearer &lt;TOKEN&gt; |
| /monopolists/{time}  | GET    |                      &nbsp;                     |    &nbsp;                      |




