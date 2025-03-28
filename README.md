<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Terminal Todo

Aplikasi manajemen tugas untuk tim dengan fokus pada editorial calendar dan social media management.

## Production Deployment Guide

### Persyaratan Server
- PHP 8.2+
- MySQL 8.0+
- Redis 6.0+
- Node.js 16+ dan NPM
- Composer 2+
- Webserver (Nginx direkomendasikan)
- SSL Certificate

### Langkah Deployment

1. **Siapkan Environment**
   ```bash
   # Clone repository
   git clone [repository_url] /path/to/app
   cd /path/to/app
   
   # Install dependensi
   composer install --no-dev --optimize-autoloader
   npm install
   npm run build
   
   # Setup environment
   cp .env.example .env
   php artisan key:generate
   ```

2. **Konfigurasi Environment**
   Edit file `.env` dan sesuaikan:
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_secure_password
   
   CACHE_DRIVER=redis
   QUEUE_CONNECTION=redis
   SESSION_DRIVER=redis
   
   REDIS_HOST=127.0.0.1
   REDIS_PASSWORD=null
   REDIS_PORT=6379
   ```

3. **Setup Database**
   ```bash
   php artisan migrate --force
   php artisan db:seed --force # Jika diperlukan
   ```

4. **Optimasi**
   ```bash
   # Cache routes dan config
   php artisan route:cache
   php artisan config:cache
   php artisan view:cache
   
   # Set permission
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

5. **Setup Queue Worker**
   Buat sistem service untuk menjalankan queue worker:
   ```bash
   # /etc/systemd/system/laravel-queue.service
   [Unit]
   Description=Laravel Queue Worker
   After=network.target

   [Service]
   User=www-data
   Group=www-data
   Restart=always
   ExecStart=/usr/bin/php /path/to/app/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600

   [Install]
   WantedBy=multi-user.target
   ```

   ```bash
   # Aktifkan service
   sudo systemctl enable laravel-queue
   sudo systemctl start laravel-queue
   ```

6. **Setup Scheduler**
   Tambahkan cron job:
   ```
   * * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
   ```

7. **Konfigurasi Nginx**
   ```nginx
   server {
       listen 80;
       server_name yourdomain.com;
       return 301 https://$host$request_uri;
   }

   server {
       listen 443 ssl;
       server_name yourdomain.com;
       
       ssl_certificate /path/to/fullchain.pem;
       ssl_certificate_key /path/to/privkey.pem;
       
       root /path/to/app/public;
       index index.php;
       
       # Gzip Compression
       gzip on;
       gzip_types text/plain text/css application/json application/javascript text/xml application/xml text/javascript;
       
       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }
       
       # Cache static assets
       location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
           expires max;
           add_header Cache-Control "public, max-age=31536000";
       }
       
       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
           fastcgi_index index.php;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
           include fastcgi_params;
       }
       
       location ~ /\.(?!well-known).* {
           deny all;
       }
   }
   ```

8. **Monitoring Setup**
   - Gunakan supervisor untuk memastikan queue worker tetap berjalan
   - Setup monitoring untuk server (New Relic, Datadog, atau solusi lain)
   - Setup backup otomatis untuk database

9. **Security Checks**
   - Pastikan semua direktori memiliki permission yang benar
   - Periksa firewall settings
   - Gunakan Laravel Security Checker untuk memeriksa vulnerabilities

10. **Post-Deployment**
    - Clear cache
    ```bash
    php artisan cache:clear
    php artisan optimize
    ```
    
    - Test aplikasi di environment production
    - Setup monitoring dan alerts

### Maintenance

- **Updates**
  ```bash
  # Turn on maintenance mode
  php artisan down
  
  # Pull the latest changes
  git pull
  
  # Update dependencies
  composer install --no-dev --optimize-autoloader
  npm install
  npm run build
  
  # Run migrations
  php artisan migrate --force
  
  # Clear caches
  php artisan optimize:clear
  php artisan optimize
  
  # Turn off maintenance mode
  php artisan up
  ```

- **Backups**
  Setup backup otomatis menggunakan package seperti spatie/laravel-backup.

### Performance Monitoring

Untuk monitoring performa aplikasi, gunakan tool seperti:
- New Relic
- Datadog
- Laravel Telescope (hanya untuk development/staging)

### Security Best Practices

- Update dependencies secara rutin
- Aktifkan CSRF protection di semua form
- Gunakan sistem permissions untuk kontrol akses
- Gunakan prepared statements untuk query database
- Validasi semua input user
- Protect against XSS dan CSRF attacks dengan middleware
- Log semua aktivitas keamanan

## License

The Terminal Todo is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
