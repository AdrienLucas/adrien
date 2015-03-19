SensioLabs Jobs
===============

1. Install the vendors : composer.phar install

2. Create the folder app/sessions
		mkdir app/sessions
		chmod -R 755 app/sessions

3. Init the DB :
```bash
        php app/console do:da:cr
        php app/console do:sc:up --force
        php app/console do:fi:lo
```