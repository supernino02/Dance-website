USE S5175710;
START TRANSACTION;

CREATE TABLE `users` (
    `email` VARCHAR(256) NOT NULL,
    `password` VARCHAR(256) NOT NULL,
    `name` VARCHAR(128) NOT NULL,
    `surname` VARCHAR(128) NOT NULL,
    `phone_number` CHAR(32) DEFAULT NULL,
    `fiscal_code` CHAR(16) DEFAULT NULL,
    `role` VARCHAR(32) NOT NULL DEFAULT 'user', /*{admin,user} ma puo essere estesa senza problemi*/
    PRIMARY KEY (`email`)
);

CREATE TABLE `tokens` (
    `user` VARCHAR(256) NOT NULL,
    `token` CHAR(32) NOT NULL,/*256 bit*/
    `token_expire` DATETIME NOT NULL,
    PRIMARY KEY (`token`),
    FOREIGN KEY (`user`) REFERENCES `users`(`email`) 
        ON DELETE CASCADE /*se l' utente viene cancellato, allora rimuovo i token*/
);

CREATE TABLE `levels` (
    `name` VARCHAR(128) NOT NULL,
    `difficulty_level` INT DEFAULT 0,
    PRIMARY KEY (`name`)
);

CREATE TABLE `product_types` (
    `type`        VARCHAR(128) NOT NULL,
    'icon_path'   VARCHAR(128) DEFAULT NULL,
    'description' TEXT DEFAULT NULL,
    PRIMARY KEY (`type`)
);

CREATE TABLE `dance_disciplines` (
    `type` VARCHAR(128) NOT NULL,
    PRIMARY KEY (`type`)
);


CREATE TABLE `products` (
    `id_product` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(128) NOT NULL,
    `poster_path` VARCHAR(128) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `level` VARCHAR(128) DEFAULT NULL,
    `type` VARCHAR(128) NOT NULL,
    `discipline` VARCHAR(128) DEFAULT NULL,
    `total_price` REAL NOT NULL DEFAULT 0,
    `discount` REAL NOT NULL DEFAULT 0,
    `expiration_date` DATETIME DEFAULT NULL, /*se è gia passata, non si può piu aquistare*/
    `location` VARCHAR(256) DEFAULT NULL,
    `location_link` VARCHAR(256) DEFAULT NULL,

    PRIMARY KEY (`id_product`),
    UNIQUE (`name`),
    /*sono fk e non di tipo ENUM poichè è un tipo non supportato*/
    FOREIGN KEY (`level`) REFERENCES `levels`(`name`)
        ON UPDATE CASCADE  /*se il nome del livello cambia, lo aggiorno*/
        ON DELETE RESTRICT,/*impedisco la cancellazione del livello se ci sono prodotti associati*/
    FOREIGN KEY (`type`) REFERENCES `product_types`(`type`)
        ON UPDATE CASCADE  /*se il nome del tipo di corso cambia, lo aggiorno*/
        ON DELETE RESTRICT,/*impedisco la cancellazione del tipo di corso se ci sono prodotti associati*/
    FOREIGN KEY (`discipline`) REFERENCES `dance_disciplines`(`type`)
        ON UPDATE CASCADE  /*se il nome del tipo di danza cambia, lo aggiorno*/
        ON DELETE RESTRICT,/*impedisco la cancellazione del tipo di danza se ci sono prodotti associati*/
);

CREATE TABLE `purchasable_files`(
    `id_product` INTEGER NOT NULL,
    `n_file` INTEGER NOT NULL DEFAULT 1,
    `path` VARCHAR(128)  NOT NULL,
    `name` VARCHAR(256)  NOT NULL,
    `description` TEXT DEFAULT NULL,
    PRIMARY KEY (`id_product`,`n_in_product`),
    FOREIGN KEY (`id_product`) REFERENCES `products`(`id_product`) 
        ON UPDATE CASCADE /*se il prodotto cambia id, lo aggiorno*/
        ON DELETE CASCADE /*se il prodotto viene cancellato, cancella anche il file associato*/
);

/*sono le foto associate a un prodotto, ovvero la sua vetrina*/
CREATE TABLE `public_files`(
    `id_product` INTEGER NOT NULL,
    `path` VARCHAR(128)  NOT NULL,
    `description` TEXT DEFAULT NULL,
    PRIMARY KEY (`id_product`,`path`),
    FOREIGN KEY (`id_product`) REFERENCES `products`(`id_product`) 
        ON DELETE CASCADE /*se il prodotto cambia id, lo aggiorno*/
        ON UPDATE CASCADE /*se il prodotto viene cancellato, cancella anche il file associato*/
);

CREATE VIEW `purchasable_products` AS
    SELECT *
    FROM products
    WHERE
        `expiration_date` > NOW()
        OR `expiration_date` IS NULL;

CREATE VIEW `purchasable_courses` AS
    SELECT *
    FROM purchasable_products
    WHERE
        `type` != "Eventi";

CREATE VIEW `purchasable_events` AS
    SELECT *
    FROM purchasable_products
    WHERE
        `type` = "Eventi";

CREATE TABLE `products_in_carts` (
    `user` VARCHAR(256) NOT NULL,
    `id_product` INTEGER NOT NULL,
    `quantity` INTEGER NOT NULL DEFAULT 1,
    PRIMARY KEY (`user`, `id_product`),
    FOREIGN KEY (`user`) REFERENCES `users`(`email`) 
        ON DELETE CASCADE,/*se un utente viene cancellato, lo cancello dal carrello*/
    FOREIGN KEY (`id_product`) REFERENCES `products`(`id_product`) 
        ON UPDATE CASCADE /*se il prodotto cambia id, lo aggiorno*/
        ON DELETE CASCADE,/*se il prodotto viene cancellato, lo cancello dal carrello*/
);

CREATE TABLE `purchases`(
    `id_purchase` INTEGER NOT NULL AUTO_INCREMENT,
    `user` VARCHAR(256) NOT NULL,
    `date_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `total_price` REAL NOT NULL DEFAULT 0.0,
    PRIMARY KEY (`id_purchase`),
    FOREIGN KEY (`user`) REFERENCES `users`(`email`) 
        ON DELETE RESTRICT /*se un utente ha effettuato almeno un acquisto, non può essere eliminato*/
);

CREATE TABLE `products_purchased` (
    `id_purchase` INTEGER NOT NULL,
    `id_product` INTEGER NOT NULL,
    `quantity` INTEGER NOT NULL DEFAULT 1,
    `unitary_price` REAL NOT NULL DEFAULT 0.0,
    PRIMARY KEY (`id_purchase`, `id_product`),
    FOREIGN KEY (`id_purchase`) REFERENCES `purchases`(`id_purchase`) 
        ON UPDATE CASCADE   /*se un acquisto aggiorna il suo id, lo aggiorno*/
        ON DELETE RESTRICT, /*se un acquisto ha almeno un prodotto associato, non può essere eliminato*/
    FOREIGN KEY (`id_product`)  REFERENCES `products`(`id_product`) 
        ON UPDATE CASCADE   /*se un prodotto aggiorna il suo id, lo aggiorno*/
        ON DELETE RESTRICT  /*se un acquisto ha almeno un prodotto associato, non può essere eliminato*/
);

CREATE TABLE `reviews` (
    `id_purchase` INTEGER NOT NULL,
    `id_product` INTEGER NOT NULL,
    `star_evaluation` REAL NOT NULL DEFAULT 5, /*autocelebrazione*/
    `note` VARCHAR(1024) DEFAULT NULL,
    `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_purchase`, `id_product`),
    FOREIGN KEY (`id_purchase`,`id_product`) REFERENCES `products_purchased`(`id_purchase`,`id_product`) 
        ON UPDATE CASCADE  /*se un acquisto aggiorna il suo id, lo aggiorno*/
        ON DELETE CASCADE /*se un acquisto viene cancellato, cancella anche la recensione*/
);


COMMIT;
