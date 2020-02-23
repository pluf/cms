<?php
$paths = array_filter(glob(__DIR__ . '/urls/*.php'), function ($file) {
    return is_file($file);
});

$cmsApi = array();

foreach ($paths as $path) {
    $myApi = include $path;
    $cmsApi = array_merge($cmsApi, $myApi);
}

return $cmsApi;
