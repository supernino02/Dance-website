<?php
//è un elenco di test che il DB SQL sia correttamente caricato
require_once "../../../BOOTSTRAP/backend/initializer.php" ;

$tests = [
    "query_errata" => [
        ["InvalidRequestedQueryException", [1]],
    ],     
    "get_product" => [
        ["BindQueryException", ["invalid_id"]],
        ["BindQueryException", ["invalid_id", 1]],
        ["BindQueryException", []],
        ["NULL", [-1]],
        ["NULL", [100000]],
        ["array", [1]]
    ],
    "check_product_not_expired" => [
        ["BindQueryException", ["invalid_id"]],
        ["BindQueryException", ["invalid_id", 1]],
        ["BindQueryException", []],
        ["0", [-1]],
        ["0", [100000]],
        ["1", [1]]
    ],


    "get_quantity_from_cart" => [
        ["BindQueryException", ["user", "-1"]],
        ["BindQueryException", [1, 1]],
        ["BindQueryException", ["-1", "bb@bb.bb"]],
        ["BindQueryException", []],
        ["NULL", ["bb@bb.bb", 4]],
        ["NULL", ["bb@bb.bb", 1000]],
        ["10", ["bb@bb.bb", 1]]
    ],
    "insert_into_cart" => [
        ["BindQueryException", ["user", "1", "1"]],
        ["BindQueryException", [1, "bb@bb.bb", 1]],
        ["BindQueryException", [1, -1, "bb@bb.bb"]],
        ["BindQueryException", []],
        ["mysqli_sql_exception", ["user", 4, -1]],
        ["mysqli_sql_exception", ["bb@bb.bb", -4, 1]],
        ["mysqli_sql_exception", ["bb@bb.bb", 5, -1]],
        ["1", ["bb@bb.bb", 4, 10]]
    ],
    "update_cart" => [
        ["BindQueryException", ["-1", "user", "1"]],
        ["BindQueryException", ["bb@bb.bb", 1, 1]],
        ["BindQueryException", [1, -1, "bb@bb.bb"]],
        ["BindQueryException", []],
        ["mysqli_sql_exception", [-10, "bb@bb.bb", 4]],
        ["0", [1, "bb@bb.bb", 5]],
        ["1", [1, "bb@bb.bb", 4]]
    ],
    "remove_from_cart" => [
        ["BindQueryException", ["user", "1"]],
        ["BindQueryException", [1,"bb@bb.bb"]],
        ["0", ["bb@bb.bb",5]],
        ["1", ["bb@bb.bb",1]],
    ],
    "get_cart" => [
        ["BindQueryException", [1]],
        ["BindQueryException", ["invalid_id", 1]],
        ["BindQueryException", []],
        ["array", ["@@@.bb"]], //is empty
        ["array", ["-1"]], //is empty
        ["array", ["bb@bb.bb"]]
    ],
    "empty_cart" => [
        ["BindQueryException", [1]],
        ["BindQueryException", [1,"bb@bb.bb"]],
        ["0", ["user"]],
        ["3", ["bb@bb.bb"]],
        ["0", ["bb@bb.bb"]],
    ],
];
$array_risultati = [];

$DB->beginTransaction();
//! TODO: rifarla, per ora non va
//$SERVICES_HANDLER->LOG->ID_SERVICE_ENTRY = 99999999;
foreach ($tests as $funzione=>$array_test) {
    foreach ($array_test as $pair) {
        $e_message = "";
        $expected = $pair[0]; $test = $pair[1]; $result = null;
        try {  
            $result = call_user_func_array([$DB,"executeQuery"],[$funzione,$test]);
            $result = is_scalar($result) ? $result : gettype($result);
        } catch (Throwable $t) {$result = get_class($t); $e_message = $t->getMessage();}
        $array_risultati[] = [$funzione,json_encode($test),$expected,$result,$e_message];
    }
}
$DB->rollBack();
display_test($array_risultati);

function display_test($testResults) {
    echo '<table border="1">';
    echo '<tr>
        <th>QUERY</th>
        <th>PARAMETRI</th>
        <th>Expected</th>
        <th>Result</th>
        <th>error message</th>
    </tr>';
    
    foreach ($testResults as $test) {
        $query = $test[0];
        $parameters = $test[1];
        $expected = $test[2];
        $result = $test[3];
        $e_message = $test[4];
        
        // Determine row color based on result match
        $rowColor = ($result == $expected) ? 'lime' : 'red';
        
        echo "<tr style='background-color: $rowColor;'>";
        echo "<td>$query</td>";
        echo "<td>$parameters</td>";
        echo "<td>$expected</td>";
        echo "<td>$result</td>";
        echo "<td>$e_message</td>";
        echo '</tr>';
    }
    
    echo '</table>';
}