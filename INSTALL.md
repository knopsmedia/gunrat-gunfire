# Shopify Product Import Generator for Gunfire

This program provides commands to download product information 
from [gunfire.com B2B portal](https://b2b.gunfire.com) to a local SQLite3 database.

From this database we generate a Shopify product import CSV.

## System Requirements

- PHP 8+
- SQLite

## Installation

### Install Dependencies

_If you do not have composer installed, please follow the instructions at:
[https://getcomposer.org/download](https://getcomposer.org/download/)._

```
$ composer i
```

### Create Local Database

Create the database at your desired location 
and use `./data/schema.sqlite.sql` to create the schema.

### Set Environment Variables

```
$ export GUNRAT_DB_URI="sqlite:///data/gunfire.sqlite3"
$ export GUNFIRE_PRODUCTS_URL="https://b2b.gunfire.com/xml/products_en.xml"
$ export GUNFIRE_PRICES_URL="https://b2b.gunfire.com/xml/B2B002052/light.xml"
```

## Commands

### Download Product Information

This command is idempotent. Use it to fetch new product information.

```
$ ./bin/download-product-info
```

### Update Product Information

This command is lighter than `bin/download-product-info` and faster.

Use it to keep your product information up-to-date by running this command regularly.

```
$ ./bin/update-product-info
```

### Create Shopify Product Import CSV

Create the CSV product import file and import it into Shopify. Tested with 2GB of RAM.

```
$ ./bin/create-shopify-import > shopify-product-import.csv
```