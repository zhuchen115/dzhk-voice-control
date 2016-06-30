<?php
define('MY_ROOT', dirname(__FILE__));
define('PACKAGE', 'font');
define('API','json');
require MY_ROOT . '/source/core.php';
$core = core::instance();
session_start();
list($class, $func) = $core->get_safe_input(INPUT_REQUEST, ['class', 'action'], ['fname', 'fname']);
header('Content-Type: application/json');
try {
    $pcode = $core->load_package($class, $func);
} catch (loader_exception $ex) {
    echo $ex->getMessage();
    exit();
}
if (is_callable($pcode)) {
    call_user_func($pcode);
} else {
        eval($pcode);   
}