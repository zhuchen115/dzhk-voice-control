<?php

/**
 * The Core Runner
 * Initialize all the objects
 * Register the templates
 * @author Zhu Chen <zhuchen@greatqq.com>
 * @package HomeSeer
 * @todo
 */
if (!defined('MY_ROOT')) {
    die('Access Denied');
}

require MY_ROOT . '/source/class/core/database.php';
require MY_ROOT . '/source/class/core/template.php';
require MY_ROOT . '/source/class/core/exceptions.php';
require MY_ROOT . '/source/packages/IPackage.php';
require MY_ROOT . '/source/function/common.php';

class core {

    /**
     * The Configuration in config.inc.php
     * @var array 
     */
    public static $config;

    /**
     * The object collection
     * @var object[] 
     */
    public static $GC;
    private static $obj = NULL;

    /**
     * Get the instance of this class
     * @return type
     */
    public static function instance() {
        if (self::$obj == NULL) {
            self::$obj = new core();
        }
        return self::$obj;
    }

    /**
     * The core class cannot be constructed.
     * To initialize all the things
     */
    private function __construct() {
        require(MY_ROOT . '/config/config.inc.php');
        putenv('LANG=' . $config['locale'] . 'utf-8');
        putenv('LANGUAGE=' . $config['locale']);
        putenv('LC_ALL=' . $config['locale']);
        setlocale(LC_ALL, $config['locale'] . '.utf8');
        self::$config = $config;
        if (defined('PACKAGE')) {          //初始化语言
            bindtextdomain(PACKAGE, MY_ROOT . '/data/language');
            textdomain(PACKAGE);
            bind_textdomain_codeset(PACKAGE, 'UTF-8');
        }
        DB::init($config['db']);         //初始化Mysql连接
    }

    /**
     * Get the handler of Smarty engine
     * @return Smarty
     */
    public static function tpl() {
        if (!(self::$GC['smarty'] instanceof template)) {
            self::$GC['smarty'] = new template();
        }
        return self::$GC['smarty'];
    }

    public static function get_module($module) {
        if (!(self::$GC[$module] instanceof $module)) {
            self::$GC[$module] = new $module();
        }
        return self::$GC[$module];
    }

    /**
     * The top Exception Catcher, Not Recoverable
     * @param Throwable $ex
     */
    public static function core_exception_txt(Throwable $ex) {
        echo "OOPS! \n An Uncaught Exception in file " . $ex->getFile() . " on line " . $ex->getLine() . ": " . $ex->getMessage() . "\n Trace Info:\n";
        if (_DEBUG_) {
            $trace = $ex->getTrace();
            print_r($trace);
        }
        die();
    }

    /**
     * Get The JSON style Exception Messages
     * @param Throwable $ex
     */
    public static function core_exception_json(Throwable $ex) {
        $ds = ['errorno' => $ex->getCode(), 'errmsg' => $ex->getMessage()];
        if (_DEBUG_) {
            $ds['file'] = $ex->getFile();
            $ds['line'] = $ex->getLine();
            $ds['trace'] = $ex->getTrace();
        }
        echo json_encode($ds);
        die();
    }

    /**
     * Echo The Exceptions for Web Page
     */
    public static function core_exception_html(Throwable $ex) {
        echo '<h1> An Error Occurred in the script</h1>';
        if(_DEBUG_){
            echo '<p> An Uncaught Exception: '.get_class($ex).'</p>';
            echo '<p>Error File: '.$ex->getFile() .' on Line'.$ex->getLine().'</p>';
            echo '<p>'.$ex->getMessage().'</p>';
            $str = $ex->getTraceAsString();
            echo 'Trace Info:<br><pre>'.$str.'</pre>';
        }
    }

    /**
     * Load the package code and generate code
     * Make Sure data/cache is writeable
     * 
     * @param string $class
     * @param string $action
     * @return string|callable The phpcode to run or the object handler of function
     * @throws loader_exception
     * @throws Exception
     */
    public function load_package($class, $action) {
        if (empty($class)) {
            throw new loader_exception($class, "Error in Loading Package, The class name was empty", 4003);
        }
        $sha = sha1('p_'.PACKAGE.':c_' . $class . ':a_' . $action);
        if (file_exists(MY_ROOT . "/data/cache/$sha.php")) {
            return 'require_once MY_ROOT.' . "'/data/cache/$sha.php';";
        }
        if (!preg_match('/[a-zA-Z0-9_]{3,20}/', $class)) {
            throw new loader_exception($class, "Error in Loading Package, The class name was invalid", 4003);
        }
        $fn = MY_ROOT . '/source/packages/' . PACKAGE . '/' . $class . '.php';
        if (!file_exists($fn)) {
            throw new loader_exception($class, "The Class was not founded", 4004);
        }
        require_once $fn;
        if (!class_exists($class)) {
            throw new loader_exception($class, "A Wrong Class Name, The filename and class name must be same!", 5001);
        }
        if (!in_array('IPackage', class_implements($class))) {
            throw new loader_exception($class, "The Loaded Class must Implements IPackage", 5002);
        }
        $hcls = new $class();
        $inc = $this->include_class($hcls, $action);
        if (!$fun = $hcls->get_function($action)) {
            throw new loader_exception($class, "The action was not Found", 4004);
        }
        $pcode = "<?php\n" . $inc . "\n" . 'require_once MY_ROOT.' . "'/source/packages/'.PACKAGE.'/$class.php';\n" . '$cls = new ' . $class . "();\n" . '$cls->' . $fun . '();' . "\n";
        if (!file_put_contents(MY_ROOT . "/data/cache/$sha.php", $pcode)) {
            throw new Exception("The directionary data/cache/ was not writeable");
        }
        eval($inc);
        return [$hcls, $fun];
    }

    /**
     * Include All the class required by package function
     * Generate a cached include list
     * @param IPackage $pack
     * @param string $action
     * @return string php code of includes
     * @throws loader_exception
     */
    private function include_class(IPackage $pack, $action) {
        $inclist = $pack->get_includes($action);
        if (!is_array($inclist)) {
            if (!is_string($inclist)) {
                throw new loader_exception(get_class($pack), "Error in include packages, returned data is not a vaild type");
            } else {
                //require_once MY_ROOT.'/source/class/'.$inclist.'.php'; //Assume the source was trusted
                $inc = str_replace([';', '?', ':', '{', '}', '(', ')', '#', '*'], '', $inclist);
                $inc = preg_replace('|[\.\\\/]+|', '/', $inc);
                return 'require_once MY_ROOT.' . "'/source/class/$inclist.php';\n";
            }
        } else {
            $pcode = '';
            foreach ($inclist as $inc) {
                if (!is_string($inc)) {
                    throw new loader_exception(get_class($pack), "Error in include packages, returned data is not a 1-D string or string");
                } else {
                    //require_once MY_ROOT.'/source/class/'.$inc.'.php';
                    $inc = str_replace([';', '?', ':', '{', '}', '(', ')', '#', '*'], '', $inc);
                    $inc = preg_replace('|[\.\\\/]+|', '/', $inc);
                    $pcode .= 'require_once MY_ROOT.' . "'/source/class/$inc.php';\n";
                }
            }
            return $pcode;
        }
    }

    /**
     * Filter the input 
     * @param int $source
     * @param string $keys 
     * @param  int[]|string[] $type The Filter Args
     * @param boolean $array Whether to accept arrays
     * @return filterd data
     */
    public static function get_safe_input($source, $keys, $type,$array=false) {
        $in = [];
        switch ($source) {
            case INPUT_GET:
                $in = $_GET;
                break;
            case INPUT_POST:
                $in = $_POST;
                break;
            case INPUT_COOKIE:
                $in = $_COOKIE;
                break;
            case INPUT_REQUEST:
            default:
                $in = $_REQUEST;
        }
        $r = [];
        foreach ($keys as $i => $k) {
            $r[$i] = isset($in[$k]) ? $in[$k] : NULL;
            $filter = !empty($type[$i]) ? $type[$i] : FILTER_UNSAFE_RAW;
            $opt = null;
            if (is_string($filter)) {
                switch ($filter) {
                    case 'boolean':
                    case 'bool':
                        $filter = FILTER_VALIDATE_BOOLEAN;
                        break;
                    case 'integer':
                    case 'int' :
                        $filter = FILTER_SANITIZE_NUMBER_INT;
                        break;
                    case 'float':
                        $filter = FILTER_SANITIZE_NUMBER_FLOAT;
                        break;
                    case 'fname':
                        $filter = FILTER_VALIDATE_REGEXP;
                        $opt = ['options' => ['regexp' => '/^[a-zA-Z0-9_]+$/']];
                        break;
                    case 'words':
                        $filter = FILTER_VALIDATE_REGEXP;
                        $opt = ['options' => ['regexp' => '/^[a-zA-Z0-9\s]+$/']];
                        break;
                    case 'email':
                        $filter = FILTER_SANITIZE_EMAIL;
                        break;
                    case 'raw':
                    default:
                        $filter = FILTER_UNSAFE_RAW;
                }
            }
            if(is_array($r[$i])&&$array){
                foreach($r[$i] as $k=>$ra){
                    $r[$i][$k] = filter_var($ra, $filter, $opt);
                }
            }else{
                $r[$i] = filter_var($r[$i], $filter, $opt);
            }
            
        }
        return $r;
    }

}

error_reporting(E_ALL & ~E_STRICT);
if (defined('API')) {
    if (API == 'json') {
        set_exception_handler(array('core', 'core_exception_json'));
    } else {
        set_exception_handler(array('core', 'core_exception_txt'));
    }
} elseif (php_sapi_name() == 'cli') {
    set_exception_handler(array('core', 'core_exception_txt'));
} else {
    set_exception_handler(array('core', 'core_exception_html'));
}
