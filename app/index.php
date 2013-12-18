<?php
namespace aoloe\logger {
    /**
     * the default Slim logger writes to standard output... we need something persistent
     */
    class Logger {
        protected $config = array(
            'path' => '',
        );
        public function config(array $config = null) {
            if (isset($config)) {
                $this->config = $config;
            }
        }
        public function write(mixed $message, int $level) {
            // TODO: implement it
        }
    }
}

namespace {

function debug($label, $value) {
    echo("<pre>$label:".print_r($value, 1).'</pre>'); ob_flush();
}

require ROOT.'/vendor/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$config = require 'config/config.php';

$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Twig(),
));
$app->config($config['slim']);
// $log_path = $app->config('logger.path'); 

// $view = $app->view();
// $view->setTemplatesDirectory('./template');

debug('_SERVER', $_SERVER);
$req = $app->request;
$rootUri = $req->getRootUri();
debug('rootUri', $rootUri);
$scriptName = $req->getScriptName();
debug('scriptName', $scriptName);
// $req->setScriptName('/public');
// $scriptName = $req->getScriptName();
// debug('scriptName', $scriptName);
$resourceUri = $req->getResourceUri();
debug('resourceUri', $resourceUri);

// when php is run as a module $_ENV is always empty
$app->configureMode('production', function () use ($app) {
    $app->config(array(
            'log.enable' => true,
            'debug' => false
    ));
});


// $log = $app->getLog();
// $log->setWriter(new\aoloe\logger\Logger());

// print_r($_SERVER);
// echo($_SERVER['SCRIPT_FILENAME']);
$app->get('/hello/:name', function ($name) use ($app) {
    // $app->view->setData(array('name' => $name));
    $app->view->appendData(array('name' => $name));
    // $app->render('layout.php', array('test' => 'this is a test'));
    $app->render('layout.html', array('test' => 'this is a test'));
});
$app->run();

return $app;

}
