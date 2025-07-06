<?php
require_once "../../../BOOTSTRAP/backend/initializer_1.php";
require_once "../../../BOOTSTRAP/backend/initializer_2.php";

$first_name = $_REQUEST['firstname'] ?? null;
$last_name = $_REQUEST['lastname'] ?? null;
$phone_number = $_REQUEST['phone_number'] ?? null;
$fiscal_code = $_REQUEST['fiscal_code'] ?? null;

$email = $_REQUEST['email'] ?? null;


header('Content-Type: application/json');
if ($first_name) 
    echo $SERVICES_HANDLER->callService("updateUserName",        [$first_name,  $email]).PHP_EOL; 
if ($last_name)
    echo $SERVICES_HANDLER->callService("updateUserSurname",     [$last_name,   $email]).PHP_EOL; 
if ($phone_number) 
    echo $SERVICES_HANDLER->callService("updateUserPhoneNumber", [$phone_number,$email]).PHP_EOL;
if ($fiscal_code) 
    echo $SERVICES_HANDLER->callService("updateUserFiscalCode",  [$fiscal_code, $email]).PHP_EOL;

die();
