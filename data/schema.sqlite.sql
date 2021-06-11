PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS product_attributes;
DROP TABLE IF EXISTS product_images;
DROP TABLE IF EXISTS products;

CREATE TABLE products (
    external_id INTEGER NOT NULL PRIMARY KEY,
    external_listing_url TEXT NOT NULL,
    name TEXT NOT NULL,
    description TEXT NOT NULL,
    category_external_id INTEGER NOT NULL,
    category_name TEXT NOT NULL,
    tags TEXT NOT NULL,
    external_sku TEXT NOT NULL,
    manufacturer_external_id INTEGER NOT NULL,
    manufacturer_name TEXT NOT NULL,
    price_amount REAL,
    price_currency TEXT,
    stock_quantity INTEGER NOT NULL
);

CREATE TABLE product_images (
    product_external_id INTEGER NOT NULL,
    position INTEGER NOT NULL,
    external_url TEXT NOT NULL,
    FOREIGN KEY (product_external_id) REFERENCES products (external_id) ON DELETE CASCADE,
    PRIMARY KEY (product_external_id, position)
);

CREATE TABLE product_attributes (
    product_external_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    value TEXT NOT NULL,
    FOREIGN KEY (product_external_id) REFERENCES products (external_id) ON DELETE CASCADE,
    PRIMARY KEY (product_external_id, name)
);