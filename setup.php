<?php

if (php_sapi_name() != 'cli') {
    exit("CLI Only");
}
define('MY_ROOT', dirname(__FILE__));
require MY_ROOT . '/source/core.php';
require MY_ROOT . '/source/class/member/member.php';
require MY_ROOT . '/source/class/homeseer/hs_base.php';
$core = core::instance();

//Setup Root User
class T_User extends member {

    function rpasswd($pass) {
        parent::passwd('root', $pass);
    }

}
/**
echo "Installing The Root Password\n";
echo "Input the new password: ";
$pass = trim(fgets(STDIN));

$huser = new T_User();
$huser->rpasswd($pass);
echo "Testing Logining\n";
$uid = $huser->login('root', $pass, $rslt);
echo "User root uid: $uid\nOther Information: ";
foreach ($rslt as $k => $r) {
    echo "$k : $r\n";
}**/

HSobj_collection::hs_init();

echo "Finished ! Delete the setup script!!";
