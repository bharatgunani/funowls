# Magento CJ Integration App
Its built on php framework.

## To Build
php -d memory_limit=1024M bin/magento 
setup:upgrade php bin/magento 
cache:clean php bin/magento cache:flus

## To run all test cases .
cd /Applications/MAMP/htdocs/magento2/
php vendor/phpunit/phpunit/phpunit --version
php -dmemory_limit=5G  vendor/phpunit/phpunit/phpunit
php vendor/phpunit/phpunit/phpunit app/code/Cj/UniversalTag


## Magento Pipeline jenkins to create build artifacts for prod
https://ebr.jenkins.cj.dev/job/Magento_Pipeline_Prod/