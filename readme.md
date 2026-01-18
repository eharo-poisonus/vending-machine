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

- **make vending-machine-insert-money MONEY=0.1** \
  *accepted values: 0.05, 0.1, 0.25, 1*

#### Buy product

- **make vending-machine-buy CODE=GET-WATER** \
  *accepted values: GET-WATER, GET-SODA, GET-JUICE*

#### Return money

- **make vending-machine-return-money**

#### Maintenance

- **make vending-machine-maintenance**

#### Machine Status

- **make vending-machine-status**

#### Total balance

- **make vending-machine-total-balance**

### How to use without Makefile

Execute: **docker exec -it vending-machine-php /bin/bash**\
Now you are ready to execute the following commands:

#### Insert money

- **php bin/console vending-machine:insert-money 0.1** \
  *accepted values: 0.05, 0.1, 0.25, 1*

#### Buy product

- **php bin/console vending-machine:buy-product GET-WATER**
  *accepted values: GET-WATER, GET-SODA, GET-JUICE*

#### Return money

- **php bin/console vending-machine:return-money**

#### Maintenance

- **php bin/console vending-machine:maintenance**

#### Machine Status

- **php bin/console vending-machine:status**

#### Total balance

- **php bin/console vending-machine:total-balance**

## ABOUT THE PROJECT

This project is a simulation of a Vending Machine built with **PHP 8.4** and **Symfony**, designed to demonstrate a
robust architecture following industry best practices.

### Key Features

- **Domain-Driven Design (DDD)**
- **Hexagonal Architecture:**
- **SOLID Principles:**
- **TDD (Test-Driven Development):**
- **Dockerized Environment:**

### Tech Stack

- **Language:** PHP 8.4
- **Framework:** Symfony
- **Database:** Mysql (with Doctrine ORM)
- **Containerization:** Docker & Docker Compose
- **Testing:** PHPUnit
- **Automation:** Makefile for common tasks

## WHAT WOULD I CHANGE?

If I had more time or if this were a production-ready project, I would implement the following improvements:

- Introduce Domain Events to decouple side effects from the primary actions.
- The current change return logic is simple; I would implement a more robust algorithm to ensure the machine always
  returns the optimal combination of coins based on available stock.
- The in memory PaymentSessionId I don't know how right now, but I wanted to simulate a frontend that creates the Uuids
  and send it to backend.
- How I return the product and the change once the payment is completed. I don't feel comfortable with the current
  implementation.
- The Tables need a consistent indexes like unique composite keys.

## SOME DIFFICULTIES ENCOUNTERED

- I tried to have all the modules decoupled, and I tried to send an event when the payment is done to update the machine
  status, but I couldn't make it work with the current Domain Structure.
- I didn't implement integration tests yet.

## WHAT MAKES ME HAPPY?
- I think it's a cool project to practice my skills and I'm happy to share it with you.
- I enjoyed working and designing the domain and thinking about the architecture.

## Requirements
- Docker
- Docker Compose
