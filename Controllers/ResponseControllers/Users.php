<?php

use System\Application\App;
use System\Di\Di;
use System\Form\Form;
use System\Http\Response\ResponseInterface;
use System\Session\SessionInterface;

App::get('/users/profile/{id}/{action}', function (ResponseInterface $response, $id, $action) {
    /** @var SessionInterface $s */
    $s = Di::getInstance()->getShared('system.session');
    $s->addOneTime(['coa' => 'test', 'test' => 'asd']);
    $s->add(['coa' => 'zivanovic', 'marjan' => 'hrzic']);

    return $response;
});

App::get('/users/profile/{id}/view', function (ResponseInterface $response, $id) {

    $form = new Form('test_form', [
        'first_name' => [
            'validators' => [
                new \System\Form\Validators\NotBlank('First name may not be empty'),
            ],
            'type' => 'text',
            'attr' => [
                'placeholder' => 'First Name',
            ],
        ],
        'last_name' => [
            'type' => 'text',
            'attr' => [
                'placeholder' => 'Last Names',
            ],
        ],
        'email' => [
            'type' => 'email',
            'attr' => [
                'placeholder' => 'Email',
            ]
        ],
        'save' => [
            'type' => 'submit',
            'value' => 'save',
        ],
    ], new ModelTest(), Form::METHOD_GET, Di::getInstance()->getShared('system.router')->getCurrentRoute());

    $form->validate();

    var_dump($form->getModel()->getData());
    
    $one = new stdClass();

    $one->type = 'Wood';
    $one->position = 'Forest';

    $two = new stdClass();
    $two->type = 'Sand';
    $two->position = 'Desert';

    $three = new stdClass();
    $three->type = 'Water';
    $three->position = 'Ocean';

    return $response->render('Views/Partial/child.html', [
                'form' => $form->render(),
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
