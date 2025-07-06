<?php

require_once "../../../BOOTSTRAP/backend/initializer_1.php" ;
require_once "../../../BOOTSTRAP/backend/initializer_2.php";
try {
    echo $DB->__toString();
    $DB->executeQuery("get_product", [9]);
    $DB->executeQuery("get_product", [10]);
    $DB->executeQuery("get_best_products_ids", [3,"ALL"]);
    echo $DB->__toString();
 
} catch(Throwable $t) {
    echo get_class($t).PHP_EOL;
    echo $t;
}

print_r($GLOBALS);
