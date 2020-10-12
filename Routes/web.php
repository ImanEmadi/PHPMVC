<?php

// ************   lower case rule for Routes ! : developer policy ************
// ************ routes must not be ended by /  : developer policy  (in router / in the end of string has been trimmed ) ************
return [
    '/' => [ // for connection and preflight test
        'method' => 'GET',
        'controller' => 'HomeController@index',
        'middleware' => 'null@null',
    ]
];
