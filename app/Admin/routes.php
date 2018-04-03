<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->get('/qr', 'QrController@index');
    $router->post('/qr/complete','QrController@qr');
    $router->get('/homework','HomeworkController@index');
    $router->post('/homework/complete','HomeworkController@complete');
    $router->get('/import','ImportController@index');
    $router->post('/import/complete','ImportController@complete');
    $router->get('/statistic','QrListController@index');
});
