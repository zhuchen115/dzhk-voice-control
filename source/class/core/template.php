<?php

/**
 * @author Zhu Chen <zhuchen@greatqq.com>
 * @package ZFramework
 */
if (!defined('MY_ROOT')) {
    die('Access Denied');
}
define('SMARTY_DIR', MY_ROOT . "/source/class/template/");
require(SMARTY_DIR . 'Smarty.class.php');

/**
 * Initial and config Smarty Engine
 * @see http://www.smarty.net/docs/zh_CN/
 */
class template extends Smarty {

    function __construct() {
        parent::__construct();
        $this->setTemplateDir(MY_ROOT . "/template/");
        $this->setCompileDir(MY_ROOT . "/data/smarty/template_c/");
        $this->setCacheDir(MY_ROOT . "/data/smarty/cache/");
        $this->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
        $this->setConfigDir(MY_ROOT . "/data/language/".core::$config['locale']."/smarty/");
    }

}
