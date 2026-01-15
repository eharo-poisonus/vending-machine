# VENDING MACHINE
## Get Ready
### How to install with Makefile
- Clone the github project with: **git clone git@github.com:eharo-poisonus/vending-machine.git**
- Execute: **make build**
- Execute: **make migration-migrate**
### How to install without Makefile
- Clone the github project with: **git clone git@github.com:eharo-poisonus/vending-machine.git**
- Execute: **docker-compose up -d --build**
- Execute: **docker exec -it vending-machine-php /bin/bash**
- Execute: **composer install**
- Execute: **php ./vendor/bin/doctrine-migrations migrate**
## How to use
### How to use with Makefile
#### Insert money
- **make insert-money**
#### Buy product
- **make buy-product**
#### Return money
- **make return-money**
#### Maintenance
- **make machine-maintenance**
### How to use without Makefile
Execute: **docker exec -it vending-machine-php /bin/bash**\
Now you are ready to execute the following commands:
#### Insert money
- **php bin/console vending-machine:insert-money**
#### Buy product
- **php bin/console vending-machine:buy-product**
#### Return money
- **php bin/console vending-machine:return-money**
#### Maintenance
- **php bin/console vending-machine:maintenance**
## ABOUT THE PROJECT

