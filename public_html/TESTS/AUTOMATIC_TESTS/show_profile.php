<?php
require_once "../../../BOOTSTRAP/backend/initializer_1.php";
require_once "../../../BOOTSTRAP/backend/initializer_2.php";

header('Content-Type: application/json');
die(
    $SERVICES_HANDLER->callService("getUserInfo")
);
