<?php

use System\Application\App;
use System\Http\Response\ResponseInterface;
use System\Session\SessionInterface;

App::get('/users/profile/{id}/{action}', function (ResponseInterface $response, $id, $action) {
    /** @var SessionInterface $s */
    $s = \System\Di\Di::getInstance()->getShared('system.session');
    $s->addOneTime(['coa' => 'test', 'test' => 'asd']);
    $s->add(['coa' => 'zivanovic', 'marjan' => 'hrzic']);

    return $response;
});

App::get('/users/profile/{id}/view', function (ResponseInterface $response, $id) {

    $one = new \stdClass();
    $one->type = 'Wood';
    $one->position = 'Forest';

    $two = new \stdClass();
    $two->type = 'Sand';
    $two->position = 'Desert';

    $three = new \stdClass();
    $three->type = 'Water';
    $three->position = 'Ocean';

    return $response->render('Views/Partial/child.html', [
                'array' => [
                    [
                        'type' => 'id',
                        'position' => $id,
                    ],
                    [
                        'type' => 'asdasdasd',
                        'position' => 'testing',
                    ],
                    $one,
                    $two,
                ],
    ]);
});
