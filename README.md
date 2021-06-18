# Shopify Product Import Generator for Gunfire

This program provides commands to:
- download product information from [gunfire.com B2B portal](https://b2b.gunfire.com).
- export product information to a Shopify store.

## System Requirements

- PHP v8.0+
  - with SQLite v2.0+
- Composer v2.0+
  - Install composer from [https://getcomposer.org/download](https://getcomposer.org/download/)

## Installation

### Install Dependencies

```
$ composer i
OR
$ php composer.phar i
```

### Set Environment Variables

Copy `.env.dist` to `.env` and edit the values to match your environment.

```
$ cp .env.dist .env
```

### Create Database

If not using sqlite, you must specify mysql. Otherwise it will fail.

```
$ ./bin/create-database [-v sqlite|mysql]
```

## Commands

### Download Product Information

This command is idempotent. Use it to fetch/update new product information.

```
$ ./bin/download-product-info
```

### Update Product Information

This command only updates stock and price information.

```
$ ./bin/update-product-info
```

### Generate Shopify Product Import CSV

Generates CSV product import file(s) each under 15MB to not exceed the upload limit.

When complete, you can find the generated files under `build/`.

```
$ ./bin/create-shopify-import
```

For more information on the available options run:

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