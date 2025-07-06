USE S5175710;
START TRANSACTION;

/*OPZIONALI; SOLO SE SI VUOLE ATTIVARE IL LOG DEI SERVIZI SU SQL */

CREATE TABLE `services_requested` (
    `id_request` INTEGER NOT NULL AUTO_INCREMENT,
    `request_name` VARCHAR(128) DEFAULT NULL,
    `user` VARCHAR(256) DEFAULT NULL,
    `actual_privilege` VARCHAR(32) NOT NULL,
    `session_id` VARCHAR(35) DEFAULT NULL,
    `date_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id_request`),
    FOREIGN KEY (`user`) REFERENCES `users`(`email`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `services_parameters` (
    `id_request` INTEGER NOT NULL,
    `parameters_list` TEXT DEFAULT NULL,

    PRIMARY KEY (`id_request`),
    FOREIGN KEY (`id_request`) REFERENCES `services_requested`(`id_request`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `services_response` (
    `id_request` INTEGER NOT NULL,
    `result` VARCHAR(8) NOT NULL,
    /*`value` VARCHER VARCHAR() NOT NULL, // tolgo poiche occupa troppa memoria*/
    `additional_info` VARCHAR(128) DEFAULT NULL,

    PRIMARY KEY (`id_request`),
    FOREIGN KEY (`id_request`) REFERENCES `services_requested`(`id_request`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `query_requested` (
    `id_query` INTEGER NOT NULL AUTO_INCREMENT,
    `id_request` INTEGER NOT NULL,
    `query_name` VARCHAR(128) NOT NULL,
    `parameters` TEXT NOT NULL,
    /*`result` TEXT, // tolgo poiche occupa troppa memoria*/

    PRIMARY KEY (`id_query`),
    FOREIGN KEY (`id_request`) REFERENCES `services_requested`(`id_request`) ON DELETE CASCADE ON UPDATE CASCADE
);



COMMIT;