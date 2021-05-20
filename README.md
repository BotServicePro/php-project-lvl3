## Laravel Web page analyzer for SEO 

<a href="https://codeclimate.com/github/BotServicePro/php-project-lvl3/maintainability"><img src="https://api.codeclimate.com/v1/badges/ec327a73209051c40214/maintainability"/></a>
<a href="https://codeclimate.com/github/BotServicePro/php-project-lvl3/test_coverage"><img src="https://api.codeclimate.com/v1/badges/ec327a73209051c40214/test_coverage"/></a>

[![CI](https://github.com/BotServicePro/php-project-lvl3/actions/workflows/main.yml/badge.svg?branch=main)](https://github.com/BotServicePro/php-project-lvl3/actions/workflows/main.yml)
[![Actions Status](https://github.com/BotServicePro/php-project-lvl3/workflows/hexlet-check/badge.svg)](https://github.com/BotServicePro/php-project-lvl3/actions)
[![Heroku App Status](http://heroku-shields.herokuapp.com/karakin-php-project-lvl3)](https://karakin-php-project-lvl3.herokuapp.com)
### This is my third project on Hexlet.io
Simple laravel application for web page analyze for SEO

### Requrements
<li> PHP ^7.3
<li> Composer
<li> SQLite for testing
<li> Database: PostgreSQL
<li> <a href="https://devcenter.heroku.com/articles/heroku-cli#download-and-install">Heroku CLI</a>

### <a href="http://karakin-php-project-lvl3.herokuapp.com/">Demo on Heroku</a>

### Setup

In console:

1) <code>git clone https://github.com/BotServicePro/php-project-lvl3.git </code>
2) <code>cd php-project-lvl3</code>
3) <code>make setup</code>
4) Edit this settings in .env file in root directory:
   
   DB_CONNECTION=pgsql
   
   DB_HOST=127.0.0.1
   
   DB_PORT=5432
   
   DB_DATABASE=yourDatabaseName
   
   DB_USERNAME=yourDataBaseUserName
   
   DB_PASSWORD=yourUserNamePass
5) <code>make migrate</code>
6) <code>make start</code>
   
