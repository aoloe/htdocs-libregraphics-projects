<?php
namespace aoloe\logger {
    /**
     * the default Slim logger writes to standard output... we need something persistent
     * --> We can probably use the "old" logger in Slim Extras
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

if (!function_exists('debug')) {
    function debug($label, $value) {
        echo("<pre>$label:".print_r($value, 1).'</pre>'); ob_flush();
    }
}

require ROOT.'/vendor/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$config = require 'config/config.php';

$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Twig(),
));
$app->config($config['slim']);

$app->view->parserExtensions = array(
    new \Slim\Views\TwigExtension()
);

// $log_path = $app->config('logger.path'); 

// $view = $app->view();
// $view->setTemplatesDirectory('./template');

// debug('_SERVER', $_SERVER);
// $req = $app->request;
// $rootUri = $req->getRootUri();
// debug('rootUri', $rootUri);
// $scriptName = $req->getScriptName();
// debug('scriptName', $scriptName);
// $pathInfo = $req->getPathInfo();
// debug('pathInfo', $pathInfo);
// $url = $req->getUrl();
// debug('url', $url);
// $rootUri = $req->getRootUri();
// debug('rootUri', $rootUri);

// when php is run as a module $_ENV is always empty

require(ROOT.'/vendor/Paris/idiorm.php'); // TODO: can paris and idiorm be used with the autoloader?
require(ROOT.'/vendor/Paris/paris.php');
ORM::configure($config['database']['url']);
ORM::configure('username', $config['database']['username']);
ORM::configure('password', $config['database']['password']);
require('models/Project.php');
Model::factory('Project');

$app->configureMode('production', function () use ($app) {
    $app->config(array(
            'log.enable' => true,
            'debug' => false
    ));
});


// $log = $app->getLog();
// $log->setWriter(new\aoloe\logger\Logger());

$app->get('/update', function () use ($app, $config) {
    define('GITAPIGET_LOCAL', true);
    $gitapiget = new \GitApiGet\GitApiGet($config['gitapiget'] + (GITAPIGET_LOCAL ? $config['gitapiget_local'] : $config['gitapiget_github']));
    // TODO: also show the reset time
    $ratelimit = $gitapiget->get_ratelimit();
    // debug('ratelimit', $ratelimit);
    $list_cached = $gitapiget->get_list_from_cache();
    $list_new = $gitapiget->get_list();
    // debug('list_new', $list_new);
    $list = $gitapiget->get_list_compared($list_new, $list_cached);
    // debug('list', $list);
    $action = $gitapiget->update_cache($list);

    $gitapiget->set_list_into_cache($list);

    // TODO: manage the proejcts list in the database?

    $app->render('update.html', array('ratelimit' => $ratelimit));
});

$app->get('/:project', function ($project) use ($app) {
    $app->view->appendData(array('project' => $project));
    $app->render('layout.html', array('test' => 'this is a test'));
});

// TODO: how to add a default root?
$app->get('.*', function () use ($app) {
    $app->render('layout.html', array('test' => 'this is a test'));
});
$app->run();

return $app;

}
