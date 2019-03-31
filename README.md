# TicTacToe Backend

### instructions for setup
This is a laravel project ([laravel.com](laravel.com)).   
To run it, it requires `php` and `mysql` or any relational db like postgres, mssql etc.

After cloning the project, fill in the `.env` file with the necessary database details and run the following command.   
`php artisan migrate`

Everything is ready at this point.

They are sevaral options available for testing the API.

one with the least setup is running `php artisan serve`.

make sure to update the VUE_APP_API_URL url in [TicTacToe Frontend](https://github.com/sjmarve/roam-frontend)  `.env` 
which will be set to something like `VUE_APP_API_URL="http://127.0.0.1:8000"`
