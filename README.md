## Battle Dev

### Installation

#### Docker Setup

1. `make docker_create` to create the necessary containers
2. `make docker_up` to spin up the containers and open and shell
NOTE: To stop docker run `make docker_down`
NOTE: To see all  commands at this level, look at Makefile in the project root
3. Add the following to your hosts file
```
127.0.0.1  battledev.test
127.0.0.1  mysql
```
Note: To connect to mysql database use: host: `127.0.0.1`, username: `root`, password: `root`

#### Application Setup

now you are in the workspace shell, you can use make targets from application/Makefile
1. `make create_local`
2. Add the following variables to your application/.env
    * GITHUB_CLIENT_ID
    * GITHUB_CLIENT_SECRET
    * PUSHER_APP_ID
    * PUSHER_APP_KEY
    * PUSHER_APP_SECRET
3. run `php artisan migrate` to migrate database tables

### Workflow Commands
These commands are executed in the workspace shall

`npm run watch`

`make test`

`make clear_cache`
