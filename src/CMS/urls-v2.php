<?php
$paths = array(
    'urls/content.php',
    'urls/term-taxonomy.php',
    'urls/term.php'
);

$cmsApi = array();

foreach ($paths as $path){
    $myApi = include $path;
    $cmsApi = array_merge($cmsApi, $myApi);
}

return $cmsApi;
