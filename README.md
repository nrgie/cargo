
This case study is created only for demonstation purposes directly to Cargo Media, Switzerland.

Author : Tibor Sulyok, Hungary
Date : 03.01.2014


Install notes:
~~~~~~~~~~~~~~

This example study is created with this server side technologies :

	- ubuntu server 13.10;
	- mongodb database;
	- nginx webserver;
	- php5-fpm php fast-cgi server;


Integration tests done in the client side after the data.json file was properly installed and the used servers are started and configured.


In ubuntu server 13.10 the following packages are needed to make this study work :

nginx
php5-fpm
php-apc
php-pear
php5-dev
php5-json
mongodb

To install them please start a console and insert this line :

apt-get install nginx php5-fpm php-apc php-pear php5-dev php5-json mongodb

After that we must install php's mongo driver via PECL. Please insert line into console to do it:

pecl install mongo


This study doesn't require any special configure to run in nginx and so php5-fpm, I mean Rewrite Rules in this case.
May you can drop the dirs and files into the default /var/www dir what nginx used by default.
But you must try to setup in nginx site config that the defaultIndex param is index.php not index.html.


To install the data.json into the database please run 

cd path/to/install/
./app.php

The database may be successfuly installed into the database.

To test the study please locate in a browser http://www.testdomain.com/test/index.php or http://localhost/test/index.php

or can watch it in my development environment at http://www.josagos.com/test/


Have a nice day!

Tibor Sulyok from Hungary.
