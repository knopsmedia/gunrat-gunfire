DROP TABLE IF EXISTS product_attributes;
DROP TABLE IF EXISTS product_images;
DROP TABLE IF EXISTS products;

CREATE TABLE products (
    external_id INTEGER UNSIGNED NOT NULL PRIMARY KEY,
    external_listing_url VARCHAR(64) NOT NULL,
    name VARCHAR(128) NOT NULL,
    description TEXT NOT NULL,
    category_external_id INTEGER UNSIGNED NOT NULL,
    category_name VARCHAR(128) NOT NULL,
    tags VARCHAR(128) NOT NULL,
    external_sku VARCHAR(16) NOT NULL,
    manufacturer_external_id INTEGER UNSIGNED NOT NULL,
    manufacturer_name VARCHAR(16) NOT NULL,
    price_amount DOUBLE,
    price_currency CHAR(3),
    stock_quantity INTEGER UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
);

CREATE TABLE product_images (
    product_external_id INTEGER UNSIGNED NOT NULL,
    position SMALLINT UNSIGNED NOT NULL,
    external_url VARCHAR(64) NOT NULL,
    FOREIGN KEY (product_external_id) REFERENCES products (external_id) ON DELETE CASCADE,
    PRIMARY KEY (product_external_id, position)
);

CREATE TABLE product_attributes (
    product_external_id INTEGER UNSIGNED NOT NULL,
    name VARCHAR(64) NOT NULL,
    value VARCHAR(128) NOT NULL,
    FOREIGN KEY (product_external_id) REFERENCES products (external_id) ON DELETE CASCADE,
    PRIMARY KEY (product_external_id, name)
);