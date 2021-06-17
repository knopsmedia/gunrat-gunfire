# Shopify Product Import Generator for Gunfire

This program provides commands to download product information 
from [gunfire.com B2B portal](https://b2b.gunfire.com) to a local SQLite3 database.

From this database we generate a Shopify product import CSV.

## System Requirements

- PHP v8.0+
  - with SQLite v2.0+
- Composer v2.0+

Install composer from [https://getcomposer.org/download](https://getcomposer.org/download/)

## Installation

### Install Dependencies

```
$ composer i
OR
$ php composer.phar i
```

### Set Environment Variables

Open `bin/envars` and change the environment variables.

```
#!/usr/bin/env bash
export GUNRAT_DB_URI="sqlite3:///data/gunrat.sqlite3"
# for mysql use
# export GUNRAT_DB_URI="mysql:///user:pass@localhost:3600/gunrat"
export GUNFIRE_PRODUCTS_URL="{YOUR PRODUCTS.XML LOCATION}"
export GUNFIRE_PRICES_URL="{YOUR PRICES.XML LOCATION}"
export SHOPIFY_SHOP_URL="{YOUR SHOP URL}"
export SHOPIFY_API_VERSION="2021-04"
export SHOPIFY_API_ACCESS_TOKEN="{YOUR API ACCESS TOKEN}"
```

### Create Local Database

Create the local database optionally specifying the DB vendor (specify mysql if not using sqlite).

```
$ ./bin/create-database [-v sqlite|mysql]
```

## Commands

Before executing any of the below commands make sure to first add the 
environment variables to your shell. If you don't the command will throw an error.

```
$ source bin/envars
```

### Download Product Information

This command is idempotent. Use it to fetch new product information.

```
$ ./bin/download-product-info
```

### Update Product Information

This command is lighter than `bin/download-product-info` and faster.

Use it to keep your price and stock information up-to-date by running this command regularly.

```
$ ./bin/update-product-info
```

### Create Shopify Product Import CSV

Create the CSV product import file(s) and import them into Shopify.
This command creates multiple files under 15MB each.

```
$ ./bin/create-shopify-import
```

You can find the files under the `build/` directory.

For more information on the uses of this command run:

```
$ ./bin/create-shopify-import -h
```

To send only newly updated information:

```
$ ./bin/create-shopify-import -s "$(cat .lastupdate)"
```

### Push Shopify Product Updates Directly

Send all product updates to the Shopify API.

```
$ ./bin/push-shopify-product-updates
```

For more information on the uses of this command run:

```
$ ./bin/push-shopify-product-updates -h
```

To send only newly updated information:

```
$ ./bin/push-shopify-product-updates -s "$(cat .lastupdate)"
```