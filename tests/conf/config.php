<?php 

// $cfg = include 'mysql.config.php';
$cfg = include 'sqlite.config.php';

$cfg['test'] = false;
$cfg['timezone'] = 'Europe/Berlin';
// Set the debug variable to true to force the recompilation of all
// the templates each time during development
$cfg['debug'] = true;
$cfg['installed_apps'] = array(
    'Pluf',
    'User',
    'Monitor',
    'CMS'
);

/*
 * Middlewares
 */
$cfg['middleware_classes'] = array(
    '\Pluf\Middleware\Session',
    'User_Middleware_Session'
);

$cfg['secret_key'] = 'simple key';

// Temporary folder where the script is writing the compiled templates,
// cached data and other temporary resources.
// It must be writeable by your webserver instance.
// It is mandatory if you are using the template system.
$cfg['tmp_folder'] = '/tmp';
$cfg['upload_path'] = '/tmp';

// The folder in which the templates of the application are located.
$cfg['template_folders'] = array(
    __DIR__ . '/../templates'
);

// Default mimetype of the document your application is sending.
// It can be overwritten for a given response if needed.
$cfg['mimetype'] = 'text/html';

return $cfg;
