<?php

use Symfony\Component\HttpFoundation\Response;

require __DIR__.'/../../src/include/routerTop.php';

$router = \EdmondsCommerce\MockServer\Factory::getStaticRouter();

$router->addStaticRoute('/test.unknownExtension', __DIR__.'/htdocs/test.unknownExtension');

$router->addCallbackRoute('/callbackRoute', function (): Response {
    return new Response('callback response');
});

$router->addRoute('/routed', 'Routed');

$router->addRoute('/admin', 'Admin Login');

$router->addFileDownloadRoute('/download', __DIR__.'/files/downloadfile.extension');

$router->addStaticRoute('/jsonfile.json', __DIR__.'/files/jsonfile.json', 'application/json');

/**
 * IMPORTANT - you have to `return` the required routerBottom
 */
return require __DIR__.'/../../src/include/routerBottom.php';
