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

ini_set('display_errors', '1');
error_reporting(E_ALL);

if (!function_exists('debug')) {
    function debug($label, $value) {
        echo("<pre>$label:".print_r($value, 1).'</pre>'); ob_flush();
    }
}

require ROOT.'/vendor/autoload.php';

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

// ORM::configure($config['database']['url']);
// ORM::configure('username', $config['database']['username']);
// ORM::configure('password', $config['database']['password']);
ORM::configure('sqlite:'.ROOT.'/storage/paris/projects.db');

$db = ORM::get_db();
try {
$db->exec("
    CREATE TABLE IF NOT EXISTS contact (
        id INTEGER PRIMARY KEY, 
        name TEXT, 
        email TEXT 
    );
    CREATE TABLE IF NOT EXISTS `projects` (
      `id` INTEGER PRIMARY KEY,
      `name` TEXT,
      `description` TEXT,
      `icon_path` TEXT,
      `license_type` INTEGER,
      `updated` INTEGER
    );

    CREATE TABLE IF NOT EXISTS `tag` (
      `id` INTEGER PRIMARY KEY,
      `name` TEXT
    );

    CREATE TABLE IF NOT EXISTS `project_tag` (
      `project_id` INTEGER,
      `tag_id` INTEGER
    );

    CREATE TABLE IF NOT EXISTS `license` (
      `id` INTEGER PRIMARY KEY,
      `name` TEXT,
      `type` INTEGER
    );

    CREATE TABLE IF NOT EXISTS `license_type` (
      `id` INTEGER PRIMARY KEY,
      `name` TEXT,
      `description` TEXT
    );

");
} catch (Exception $e) {
    debug('e', $e);
}



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
    // $gitapiget = new \Aoloe\GitApiGet\GitApiGet($config['gitapiget'] + (GITAPIGET_LOCAL ? $config['gitapiget_local'] : $config['gitapiget_github']));
    // $gitapiget = new \GitApiGet\GitApiGet($config['gitapiget'] + (GITAPIGET_LOCAL ? $config['gitapiget_local'] : $config['gitapiget_github']));
    // $gitapiget = new \Aoloe\GitApiGet($config['gitapiget'] + (GITAPIGET_LOCAL ? $config['gitapiget_local'] : $config['gitapiget_github']));
    // $gitapiget = new \Aoloe\GitApiGet($config['gitapiget'] + (GITAPIGET_LOCAL ? $config['gitapiget_local'] : $config['gitapiget_github']));
    $gitapiget = new GitApiGet($config['gitapiget'] + (GITAPIGET_LOCAL ? $config['gitapiget_local'] : $config['gitapiget_github']));
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

$app->get('/p/:project', function ($project) use ($app) {
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
