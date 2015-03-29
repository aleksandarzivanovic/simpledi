<?php

use System\Application\App;
use System\Http\Response\ResponseInterface;
use System\Session\SessionInterface;

App::get('/users/profile/{id}/{action}', function (ResponseInterface $response, $id, $action) {
    /** @var SessionInterface $s */
    $s = \System\Di\Di::getInstance()->get('system.session');
    $s->addOneTime(['coa' => 'test', 'test' => 'asd']);
    $s->add(['coa' => 'zivanovic', 'marjan' => 'hrzic']);

    return $response;
});

App::get('/users/profile/{id}/view', function (ResponseInterface $response, $id) {
    $di = System\Di\Di::getInstance();
    /* @var $config System\Config\ConfigInterface */
    $config = $di->get('system.config');

    $config->loadConfig('data/storage.json');
    var_dump($config->get('mysql'));

    return $response->render('Views/Partial/child.html', [
                'array' => [
                    [
                        'type' => 'Wood',
                        'position' => 'Forest',
                    ],
                    [
                        'type' => 'Sand',
                        'position' => 'Desert',
                    ],
                    [
                        'type' => 'Water',
                        'position' => 'Ocean',
                    ],
                ],
    ]);
});
