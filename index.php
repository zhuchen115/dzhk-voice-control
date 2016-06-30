<?php

define('MY_ROOT', dirname(__FILE__));
define('PACKAGE', 'html');
require MY_ROOT . '/source/core.php';
$core = core::instance();
session_start();
list($class, $func) = $core->get_safe_input(INPUT_REQUEST, ['class', 'action'], ['fname', 'fname']);
if(!$class){
    $class = 'ucenter';
}
if(!$func){
    $func = 'dashboard';
}

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