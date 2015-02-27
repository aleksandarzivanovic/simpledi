<?php

use System\Application\App;
use System\Http\Response\ResponseInterface;

App::get('/images/{id}/view', function (ResponseInterface $response, $id, $action) {
    echo 'This is GET method for url /users/profile/{id}/{action}<br/>';
    echo 'Id is ' . $id . '<br />';
    echo 'Action is ' . $action . '<br />';
    echo '<form method="post"><button type="submit">View POST route</button></form>';

    return $response;
});

App::get('/images/{id}/copy', function (ResponseInterface $response, $id) {
    echo 'This is POST method for url /users/profile/{id}/view<br/>';
    echo 'Id is ' . $id . '<br />';
    echo '<a href="index.php?route=/users/profile/' . $id . '/view">View GET route</a>';

    return $response;
});
