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
    /** @var SessionInterface $s */
    $s = \System\Di\Di::getInstance()->get('system.session');

    var_dump($s->getOneTime(['coa', 'test']));
    var_dump($s->get(['coa', 'marjan']));

    return $response;
});
