# Test task

## Prerequisite steps

1. You must have installed Docker and Docker compose or, if you have installed php8.3 locally, u can jump to the step 2 of part How to run app

## Installation steps:

0. Clone the project
1. Run `cp .env.example .env` from the main root level [.evn.example](.env.example)
2. Run `cp www/.env.example www/.env` from the main root level [www/.evn.example](.env.example)
3. Register to https://exchangeratesapi.io/ and create API key, put in here `www/.wnv` the key `API_EXCHANGE_KEY`
4. Run `docker-compose up -d`

### How to run app

1. In your console please execute `docker exec -it group-bwt-task-php8.3-fpm bash`
2. Install packages `composer install`
3. Run `php src/app.php src/example.txt` FYI: Due to the currency rate and BIN API limitation, you may not get the data as expected
4. Test run `composer run-script test-unit`. In the folder `www/code-coverage/unit` you can the test report
