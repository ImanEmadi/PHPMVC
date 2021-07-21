<?php

// ************ routes must not be ended by /  : developer policy  (in router / in the end of string has been trimmed ) ************
return [
    '/' => [ // for connection and preflight test
        'method' => 'GET|POST',
        'controller' => 'HomeController@index',
        'middleware' => 'null@null',
    ]
];
