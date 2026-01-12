# Distributed E-Commerce Order System (Chronicle)

This is a Laravel Backend REST API to simulate a simple product sales. There are 2 API made in this project which are Product and Order API.

## Product API

The product API has 5 endpoints to allow the user to get product list, create, get, update and delete a product from/to mysql database. This API uses Cache system with Redis as the storage to store product details. Therefore, when there is a request to get a product, the API will check the cache first before running a query on database. The API will save a cache after getting or updating a product with 5 minute time to live (ttl), and will delete product details cache when the product is deleted or when the ttl expired.

The documentation for this API can be found in `product-api.yaml` file in `docs` directory in this project or through this [Postman Collection](https://cevin2004jambi-8760479.postman.co/workspace/Vinn's-Workspace~8d7f1df9-e9c7-4933-8b5d-6eb622b1030a/collection/48552734-03691eb2-15ab-4a94-a8c9-c49247e49d84?action=share&source=copy-link&creator=48552734)

## Order API

The order API only has 1 endpoint to allow the user make a purchase. To handle data inconsistency due to concurrent request, MySQL DB Transaction with Serializable isolation level is used on the service. Any query within MySQL DB Transaction will not saved to the database until there is commit. If there are errors within the transaction, all the query within will rollbacked and won't affect the database. The Serializeable isolation level is used to handle concurrent request because it will make sure all the transactions handled synchronously. Therefore, when there is a query on a transaction, the transaction will lock that query to make sure the other transactions can't run a related query until the first query transaction is ended.

The Order API also simulating a 5 second delay (simulate external API call) on the background using Laravel Queue (A Celery alternative for Laravel environment). When an order is saved on the database, the service will send the job to a queue (with redis as the storage), so that the worker can handle the jobs queue on the background. This will preventing user to wait the job done before continuing, as the service will run asynchronously.

The documentation for this API can be found in `order-api.yaml` file in `docs` directory in this project or through this [Postman Collection](https://cevin2004jambi-8760479.postman.co/workspace/Vinn's-Workspace~8d7f1df9-e9c7-4933-8b5d-6eb622b1030a/collection/48552734-fb5a9422-89d0-42e4-8565-682ade4271e2?action=share&source=copy-link&creator=48552734)

## Docker Setup

Laravel Sail is used for this project to automaticaly setup Laravel project on Docker environment using terminal. To setup Docker on your computer, please follow the instruction below:

1. Download/clone the project from GitHub.
2. Make sure you have _composer_ installed on your computer, open the project and type `composer install` on the terminal to install the required dependencies.
3. Make `.env` file by copy paste `.env.example` file in the directory.
4. Run the Docker Engine and run Docker containers using `vendor/bin/sail up` command in your project terminal (Windows: must be on wsl2). By running that command, Laravel Sail will run `docker-compose up` in the background.
5. Run `vendor/bin/sail artisan migrate` command to prepare the database for the project.
6. From this point, the API already can be used. This project uses laravel queue on the API. Therefore, before using it with postman, etc, make sure to run `vendor/bin/sail artisan queue:work` on the terminal to make sure the worker will do the job on the queue.
