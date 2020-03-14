<?php
return array(
    // ************************************************************* Schema
    array(
        'regex' => '#^/contents/(?P<parentId>\d+)/histories/schema$#',
        'model' => 'Pluf_Views',
        'method' => 'getSchema',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'CMS_ContentHistory'
        )
    ),
    // ************************************************************* ContentHistory
    array(
        'regex' => '#^/contents/(?P<contentId>\d+)/histories$#',
        'model' => 'CMS_Views_ContentHistory',
        'method' => 'find',
        'http-method' => 'GET',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array(
        'regex' => '#^/contents/(?P<name>[^/]+)/histories$#',
        'model' => 'CMS_Views_ContentHistory',
        'method' => 'find',
        'http-method' => 'GET',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    ),
    array(
        'regex' => '#^/contents/(?P<contentId>\d+)/histories/(?P<historyId>\d+)$#',
        'model' => 'CMS_Views_ContentHistory',
        'method' => 'get',
        'http-method' => 'GET',
        'precond' => array(
            'User_Precondition::loginRequired'
        )
    )
);

