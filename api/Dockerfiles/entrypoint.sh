#! /usr/local/bin/php

<?php

if(file_exists("/api/vendor") && file_exists("/api/composer.lock")) {
    echo("\n\n *** You already have service dependencies. *** \n\n\n\n");
} else {
    echo("\n\n *** INSTALLING LARAVEL DEPENDENCIES *** \n\n\n\n");
    
    shell_exec("composer install --ignore-platform-reqs");
    
    echo("\n\n\n *** DONE INSTALLING LARAVEL DEPENDENCIES *** \n\n\n\n");
}

if(!file_exists("/api/.env")) {
    echo("\n\n *** MISSING /api/.env FILE *** \n\n\n\n");
    
    die;
}

echo(shell_exec("php artisan key:generate --force && php artisan cache:clear && php artisan config:cache && php artisan route:cache"));

echo("\nNew Service started...\n");

shell_exec("php artisan serve --host=0.0.0.0 --port=80");

?>