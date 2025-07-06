USE S5175710;
START TRANSACTION;

DELIMITER //

CREATE PROCEDURE check_name(IN value TEXT)
BEGIN
    DECLARE error_message VARCHAR(255);
    SET error_message = NULL;

    IF value IS NULL THEN
        SET error_message = 'Non può essere NULL';
    ELSEIF CHAR_LENGTH(value) > 128 THEN
        SET error_message = 'Lunghezza massima 128 caratteri';
    ELSEIF CHAR_LENGTH(value) < 2 THEN
        SET error_message = 'Lunghezza minima 2 caratteri';
    ELSEIF NOT value REGEXP "^[\\p{Letter}\\s\\-.\']{2,128}$" THEN
        SET error_message = 'Contiene caratteri non validi';
    END IF;

    SELECT error_message AS 'name';
END //

CREATE PROCEDURE check_surname(IN value TEXT)
BEGIN
    DECLARE error_message VARCHAR(255);
    SET error_message = NULL;

    IF value IS NULL THEN
        SET error_message = 'Non può essere NULL';
    ELSEIF CHAR_LENGTH(value) > 128 THEN
        SET error_message = 'Lunghezza massima 128 caratteri';
    ELSEIF CHAR_LENGTH(value) < 2 THEN
        SET error_message = 'Lunghezza minima 2 caratteri';
    ELSEIF NOT value REGEXP "^[\\p{Letter}\\s\\-.\']{2,128}$" THEN
        SET error_message = 'Contiene caratteri non validi';
    END IF;

    SELECT error_message AS 'surname';
END //

CREATE PROCEDURE check_email(IN value TEXT)
BEGIN
    DECLARE error_message VARCHAR(255);
    SET error_message = NULL;

    IF value IS NULL THEN
        SET error_message = 'Non può essere NULL';
    ELSEIF NOT value REGEXP "^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,63}$" THEN
        SET error_message = 'Non è un valido indirizzo email';
    ELSE
        IF EXISTS(SELECT 1 FROM users WHERE email = value) THEN
            SET error_message = 'Email già utilizzata';
        END IF;
    END IF;

    SELECT error_message AS 'email';
END //

CREATE PROCEDURE check_raw_password(IN value TEXT)
BEGIN
    DECLARE error_message VARCHAR(255);
    SET error_message = NULL;

    IF value IS NULL THEN
        SET error_message = 'Non può essere NULL';
    ELSEIF CHAR_LENGTH(value) < 4 THEN
        SET error_message = 'Lunghezza minima 4 caratteri';
    END IF;

    SELECT error_message AS 'password';
END //

CREATE PROCEDURE check_phone_number(IN value TEXT)
BEGIN
    DECLARE error_message VARCHAR(255);
    SET error_message = NULL;

    IF value IS NOT NULL THEN
        IF NOT value REGEXP "^[+]?[1-9]{1,3}[0-9' ]{1,15}$" THEN
            SET error_message = 'Non è un valido numero di telefono';
        END IF;
    END IF;

    SELECT error_message AS 'phone_number';
END //

CREATE PROCEDURE check_fiscal_code(IN value TEXT)
BEGIN
    DECLARE error_message VARCHAR(255);
    SET error_message = NULL;

    IF value IS NOT NULL THEN
        IF NOT value REGEXP '^[A-Z]{6}[0-9]{2}[ABCDEHLMPRST]{1}[0-9]{2}[A-Z][0-9]{3}[A-Z]{1}$' THEN
            SET error_message = 'Non è un valido codice fiscale';
        END IF;
    END IF;

    SELECT error_message AS 'fiscal_code';
END //

CREATE PROCEDURE check_price(IN value REAL)
BEGIN
    DECLARE error_message VARCHAR(255);
    SET error_message = NULL;

    IF value IS NULL THEN
        SET error_message = 'Non può essere NULL';
    ELSEIF value < 0.0 THEN
        SET error_message = 'Non può essere un valore negativo';
    END IF;

    SELECT error_message AS 'price';
END //

CREATE PROCEDURE check_discount(IN value REAL)
BEGIN
    DECLARE error_message VARCHAR(255);
    SET error_message = NULL;

    IF value IS NULL THEN
        SET error_message = 'Non può essere NULL';
    ELSEIF value < 0.0 THEN
        SET error_message = 'Non può essere un valore negativo';
    ELSEIF value > 100.0 THEN
        SET error_message = 'Non può essere superiore a 100';
    END IF;

    SELECT error_message AS 'discount';
END //

CREATE PROCEDURE check_quantity(IN value INT)
BEGIN
    DECLARE error_message VARCHAR(255);
    SET error_message = NULL;

    IF value IS NULL THEN
        SET error_message = 'Non può essere NULL';
    ELSEIF value <= 0 THEN
        SET error_message = 'Deve essere un valore positivo';
    END IF;

    SELECT error_message AS 'quantity';
END //

CREATE PROCEDURE check_star_evaluation(IN value REAL)
BEGIN
    DECLARE error_message VARCHAR(255);
    SET error_message = NULL;

    IF value IS NULL THEN
        SET error_message = 'Non può essere NULL';
    ELSEIF value < 0.5 THEN
        SET error_message = 'Non può essere meno di 0.5';
    ELSEIF value > 5.0 THEN
        SET error_message = 'Non può essere più di 5';
    ELSEIF MOD(value, 0.5) <> 0 THEN
        SET error_message = 'Deve essere multiplo di 0.5';
    END IF;

    SELECT error_message AS 'star_evaluation';
END //


/* Definisco procedure di garbage collecting */

/* Rimuove i token scaduti */
CREATE PROCEDURE RemoveExpiredTokens()
BEGIN
    DELETE FROM tokens WHERE token_expire < NOW();
END //

/* La ripeto ogni giorno */
CREATE EVENT RemoveExpiredTokensEvent ON SCHEDULE EVERY 1 DAY DO
BEGIN
    DELETE FROM tokens WHERE token_expire < NOW();
END //

/* Elimina dal carrello ogni prodotto scaduto */
CREATE PROCEDURE RemoveExpiredProductsFromCart()
BEGIN
    DECLARE done BOOLEAN DEFAULT FALSE;
    DECLARE userId VARCHAR(256);
    DECLARE productId INTEGER;
    DECLARE cartCursor CURSOR FOR
    SELECT pic.`user`, pic.`id_product`
    FROM `products_in_carts` pic
    INNER JOIN `products` p ON pic.`id_product` = p.`id_product`
    WHERE NOW() >= p.`expiration_date`;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    OPEN cartCursor;
    cartLoop: LOOP
        FETCH cartCursor INTO userId, productId;
        IF done THEN
            LEAVE cartLoop;
        END IF;
        DELETE FROM `products_in_carts` WHERE `user` = userId AND `id_product` = productId;
    END LOOP;
    CLOSE cartCursor;
END //

/* La ripeto ogni giorno */
CREATE EVENT RemoveExpiredProductsEvent ON SCHEDULE EVERY 1 DAY DO
BEGIN
    CALL RemoveExpiredProductsFromCart();
END //

/* Funzione di ricerca prodotti */
CREATE PROCEDURE SearchProducts(IN searchString TEXT)
BEGIN
    DECLARE searchPattern TEXT;
    SET searchPattern = CONCAT('%', searchString, '%');
    SELECT * FROM purchasable_products
    WHERE name LIKE searchPattern
    OR description LIKE searchPattern
    OR level LIKE searchPattern
    OR type LIKE searchPattern
    OR discipline LIKE searchPattern;
END //

/*restistuisce gli id dei  migliori n prodotti; 
    per ora conta solo il numero di vendite*/
DELIMITER //

CREATE PROCEDURE BestProducts(IN N_ROWS INT)
BEGIN
    SELECT 
        p.id_product, 
        SUM(pp.quantity) AS total_quantity
    FROM products_purchased pp 
    JOIN purchasable_products p ON pp.id_product = p.id_product 
    GROUP BY 
        p.id_product
    ORDER BY total_quantity DESC 
    LIMIT N_ROWS;
END //

DELIMITER ;


/*** LOG ***/
/* Restituisce tutte le richieste che hanno avuto l'esito definito, chiamo con call e input {'OK','ERROR','FAIL'} */
CREATE PROCEDURE GetServicesByResult(IN type VARCHAR(32))
BEGIN
    SELECT
        sr.id_request,
        sr.request_name,
        sr.user,
        sr.actual_privilege,
        sr.session_id,
        sr.date_time,
        sres.result,
        sres.additional_info
    FROM
        services_requested sr
    JOIN
        services_response sres ON sr.id_request = sres.id_request
    WHERE
        type IS NULL OR sres.result = type;
END //

/* Restituisce il riepilogo delle sessioni */
CREATE PROCEDURE getSessionsSummary()
BEGIN
    SELECT
        session_id,
        MIN(date_time) AS first_request,
        MAX(date_time) AS last_request,
        COUNT(id_request) AS total_requests,
        GROUP_CONCAT(DISTINCT user ORDER BY user ASC SEPARATOR ', ') AS logged_as
    FROM
        services_requested
    WHERE
        session_id IS NOT NULL
    GROUP BY
        session_id;
END //


CREATE PROCEDURE `relatedProducts`(
    IN `id_product` INT,
    IN `max_items` INT
)
BEGIN
    -- Ottenere i prodotti correlati usando una JOIN
    SELECT pp.*
    FROM `products` AS p
    JOIN `products` AS pp
        ON p.`type` = pp.`type`
        AND p.`discipline` = pp.`discipline`
    WHERE p.`id_product` = id_product
      AND pp.`id_product` != id_product
    LIMIT max_items;
END //


COMMIT;