<?php 
error_reporting(E_ALL);

function exception_error_handler($errno, $errstr, $errfile, $errline ) 
{
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");

date_default_timezone_set('Europe/Berlin');






require_once(dirname(__FILE__).'/../frameworks/predis/autoload.php');
Predis\Autoloader::register();

require_once(dirname(__FILE__).'/../my-frameworks/sugarloaf/lib/sugarloaf.php');
require_once(dirname(__FILE__).'/../my-frameworks/tiro-php-profiler/src/tiro.php');
require_once(dirname(__FILE__).'/../my-frameworks/php-scheduler-service/src/require.php');

//require_once($baseDir.'/../my-frameworks/brokenpottery/brokenpottery.php');

//require_once($baseDir.'/query-engine/ZeitfadenQueryEngine.php');
//require_once($baseDir.'/query-engine/context/Assembly.php');
//require_once($baseDir.'/query-engine/context/Handler.php');
//require_once($baseDir.'/query-engine/context/Interpreter.php');


require_once(dirname(__FILE__).'/ZeitfadenExceptions.php');
//require_once($baseDir.'/TimeService.php');
require_once(dirname(__FILE__).'/ZeitfadenRouter.php');
require_once(dirname(__FILE__).'/ZeitfadenApplication.php');




require_once(dirname(__FILE__).'/AbstractZeitfadenController.php');
require_once(dirname(__FILE__).'/ZeitfadenFrontController.php');


require_once(dirname(__FILE__).'/controller/TaskController.php');







//require_once($baseDir.'/ZeitfadenUUID.php');


