<?php

define('API', 'txt');
define('MY_ROOT', dirname(__FILE__));
define('PACKAGE', 'api');
require MY_ROOT . '/source/core.php';
require MY_ROOT . '/source/function/api.php';
$core = core::instance();
list($class, $func) = $core->get_safe_input(INPUT_REQUEST, ['class', 'action'], ['fname', 'fname']);
try {
    $pcode = $core->load_package($class, $func);
} catch (loader_exception $ex) {
    echo $ex->getMessage();
    exit();
}
if (is_callable($pcode)) {
    call_user_func($pcode);
} else {
    try {
        eval($pcode);
    } catch (Error $ex) {
        echo "Error in executing Cache Code" . $ex->getMessage();
    }
}
