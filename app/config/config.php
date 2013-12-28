<?php
return array (
    'database' => array (
        'url' => 'mysql:localhost;dname=projects',
        'username' => 'test',
        'password' => 'test',
    ),
    'slim' => array (
        'debug' => true,
        'templates.path' => ROOT.'/app/views',
        // 'log.enabled' => 'true',
    ),
    'logger' => array (
        // 'path' => '',
    ),
    'gitapiget' => array (
        'cache.path' => ROOT.'/storage/gitapiget/',
        'repository.user' => 'aoloe',
        'repository.repository' => 'libregraphics-projects',
        'repository.branch' => 'master',
    ),
    'gitapiget_github' => array (
        'url.ratelimit' => 'https://api.github.com/rate_limit',
        'url.filelist' => 'https://api.github.com/repos/$user/$repository/git/trees/$branch?recursive=1',
        'url.fileraw' => 'https://raw.github.com/$user/$repository/$branch/',
    ),
    'gitapiget_local' => array (
        'url.ratelimit' => 'http://ww.graphicslab.org/projects/gitapi/api_ratelimit.php',
        'url.filelist' => 'http://ww.graphicslab.org/projects/gitapi/api_filelist.php',
        'url.fileraw' => 'http://ww.graphicslab.org/projects/gitapi/api_fileraw.php',
        'url.method' => 'get', // if not set, rewrite
    ),
);
