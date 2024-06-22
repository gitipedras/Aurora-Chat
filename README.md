## Running

To run the aurora chat php server you can use `localhost` with `php -S localhost:PORT` (replace PORT with port)

You can also run it on localhost with apache2:
1. Install Apache2
`sudo apt-get install apache2`

2. Move aurora-chat's files into `/var/www/html/`

3. Start apache2
`sudo systemctl start apache2.service`


## Dependicies

`php`(php version 8 or up)
You can install dependicies by doing:

```sudo apt-get install php```

