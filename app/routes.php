<?php

return [
    '/' => [
        [
            'type' => 'GET',
            'handler' => 'FormController@home',
        ],
    ],
    '/form' => [
        [
            'type' => 'GET',
            'handler' => 'FormController@index',
        ],
    ],
    '/results' => [
        [
            'type' => 'POST',
            'handler' => 'FormController@submit',
        ],
    ],
];
