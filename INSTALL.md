# Shopify Product Import Generator for Gunfire

This project 

## System Requirements

PHP 8+
SQLite

### Install Composer

_If you do not have composer installed, please follow the instructions at:
[https://getcomposer.org/download](https://getcomposer.org/download/)._

```
$ composer i
```

### Create Database

Create the database at your desired location 
and use `./data/schema.sqlite.sql` to create the schema.

### Set Database Environment Variable

And point it to your database.

```
$ export GUNFIRE_DB_URI="sqlite:///data/gunfire.sqlite3"
```

### Download Product Information

This command is idempotent. Use it to fetch new product information.

```
$ php -dmemory_limit=2G download-product-info.php
```

### Update Product Information

This command is lighter than `download-product-info.php` and faster.
Use it to keep your product information up-to-date by running this command regularly.

```
$ php -dmemory_limit=2G update-product-info.php
```

### Create Shopify Product Import CSV

Create the CSV product import file and import it into Shopify.

```
$ php -dmemory_limit=2G create-shopify-import.php > shopify-product-import.csv
```