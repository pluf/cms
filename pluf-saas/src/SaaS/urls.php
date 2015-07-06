<?php
return array(
        array( // اطلاعات نرم‌افزار جاری
                'regex' => '#^/app$#',
                'base' => $base,
                'model' => 'SaaS_Views_Application',
                'method' => 'currentApplication',
                'http-method' => 'GET'
        ),
        array( // اطلاعات نرم‌افزار مورد نظر
                'regex' => '#^/app/(\d+)$#',
                'base' => $base,
                'model' => 'SaaS_Views_Application',
                'method' => 'get',
                'http-method' => 'GET'
        ),
        array( // اطلاعات نرم‌افزار مورد نظر
                'regex' => '#^/app/(\d+)$#',
                'base' => $base,
                'model' => 'SaaS_Views_Application',
                'method' => 'update',
                'http-method' => 'POST'
        ),
        array( // فهرستی از تمام نرم‌افزارهای موجود
                'regex' => '#^/app/list$#',
                'base' => $base,
                'model' => 'SaaS_Views_Application',
                'method' => 'applications'
        ),
        /* دسترسی‌ها به نرم‌افزار */
        array( // فهرستی از تمام اعضا
                'regex' => '#^/app/(\d+)/member/list$#',
                'base' => $base,
                'model' => 'SaaS_Views_Application',
                'method' => 'members'
        ),
        /* تنظیم‌ها */
        array( // فهرستی از تنظیم‌ها
                'regex' => '#^/app/(\d+)/config/list$#',
                'base' => $base,
                'model' => 'SaaS_Views_Configuration',
                'method' => 'configurations'
        ),
        array( // دسترسی به تنظیم‌ها
                'regex' => '#^/app/(\d+)/config$#',
                'base' => $base,
                'model' => 'SaaS_Views_Configuration',
                'method' => 'configuration'
        )
);