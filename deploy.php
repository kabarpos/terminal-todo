<?php
namespace Deployer;

require 'recipe/laravel.php';

// Config
set('repository', 'YOUR_REPOSITORY_URL');
set('git_tty', true);
set('keep_releases', 5);

// Shared files/dirs between deploys
add('shared_files', []);
add('shared_dirs', [
    'storage',
    'public/uploads'
]);

// Writable dirs by web server
add('writable_dirs', [
    'bootstrap/cache',
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'public/uploads'
]);

// Hosts
host('production')
    ->set('remote_user', 'YOUR_SSH_USER')
    ->set('hostname', 'YOUR_SERVER_IP')
    ->set('deploy_path', '/var/www/terminal-todo');

// Tasks
task('build', function () {
    cd('{{release_path}}');
    run('npm install');
    run('npm run build');
});

// Optimize Laravel
task('artisan:optimize', function () {
    cd('{{release_path}}');
    run('php artisan optimize');
    run('php artisan view:cache');
    run('php artisan config:cache');
    run('php artisan route:cache');
});

// Database migrations
task('artisan:migrate', function () {
    cd('{{release_path}}');
    run('php artisan migrate --force');
});

// Clear cache
task('deploy:cache:clear', function () {
    cd('{{release_path}}');
    run('php artisan cache:clear');
    run('php artisan config:clear');
    run('php artisan view:clear');
    run('php artisan route:clear');
});

// Deployment flow
desc('Deploy the application');
task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'deploy:cache:clear',
    'artisan:storage:link',
    'artisan:view:cache',
    'artisan:config:cache',
    'artisan:route:cache',
    'artisan:migrate',
    'build',
    'deploy:publish'
]); 