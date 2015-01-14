# run-it

Run shell commands on your machine by hitting a url.

## Setup

    git clone https://github.com/lyoshenka/run-it.git
    cd run-it
    composer install

    ./gencert.sh   # generate self-signed SSL key & cert
    cp nginx-sample.conf /etc/nginx/sites-available/run-it.conf
    sudo ln -s ../sites-available/run-it.conf /etc/nginx/sites-enabled/run-it.conf
    sudo vi /etc/nginx/sites-enabled/run-it.conf   # replace all values THAT ARE IN CAPS
    nginx -s reload

## Configuration

- Open `web/index.php`.
- Add at least one auth key to `$app['authKeys']` array.
- Configure the commands you want to run in `$app['commands']`.

## Using the API

`GET https://yourdomain.com/COMMAND?auth_token=TOKEN`

### Authentication

You must use one of your auth keys to access the API. The key may be sent using the `Authorization` HTTP header or the `auth_token` GET parameter.

### Format

Add the `format` GET param to specify the return format. Options are:

- `json` - This is the default if you don't specify a format. Return the output, return code, etc. as JSON.
- `raw`  - Just return the raw output.
- `pre`  - Return the output enclosed in `<pre>` tags and with HTML entities escaped. Useful for displaying output as text.

### Passing Parameters

You can't. Yet.
