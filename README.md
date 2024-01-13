
## Gereklilikler

- PHP ^8.1
- Composer
- MySQL

## Kurulum

1. Depoyu klonla: `git clone https://github.com/atilganajan/ExampleSMS.git`

2. Proje klasörüne gidin: `cd ExampleSMS`

3. Bağımlılıklarını yükleyin: `composer install`

4. `.env.example` dosyasını kopyalayıp `.env` dosyası oluşturup database tanımlamalarını yapın.

5.  .env içindeki `QUEUE_CONNECTION=sync` `QUEUE_CONNECTION=database` olarak değiştirin.
     
6. Uygulama anahtarını oluşturun: `php artisan key:generate`

7. migrationları çalıştrın: `php artisan migrate`
 
10. `php artisan serve`

11.  `php artisan queue:work`

## Swagger UI
- Swagger url `BASE_URL/api/documentation`

## Unit Test
php artisan test

