rm -Rf app bin src tests var web .gitignore composer.json composer.lock composer.phar phpunit.xml.dist README.md web.config
curl -sS https://getcomposer.org/installer | php
php composer.phar install
