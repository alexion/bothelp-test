`docker-compose up --build -d

docker-compose exec php-cli composer install

docker-compose exec php-cli php bin/console.php event:generate 1000 10000
`