-Weather Recommendation API
It fetches weather data from the LHMT, processes forecasts into meaningful weather categories, and matches those categories to relevant products stored in a database.

-How it works
Fetch weather forecast from LHMT API
Normalize forecast into daily entries
Convert weather conditions into tags
Match tags against product database
Return structured recommendation response

-Technologies Used
PHP 8.3
Symfony 6+
Doctrine ORM
MySQL (via Docker)
PHPUnit 12
Symfony HttpClient
Symfony Cache Component
Docker & Docker Compose

-Setup Guide
git clone
cd weather-rec-app
docker compose up -d
docker compose exec app composer install
docker compose exec app php bin/console doctrine:migrations:migrate
docker compose exec app php bin/console doctrine:fixtures:load
docker compose exec app php bin/phpunit

-Example Request
curl "http://localhost:8000/api/recommendations?city=kaunas"
