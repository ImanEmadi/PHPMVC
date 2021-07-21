<?php
// Setting
error_reporting(E_ALL);
ini_set('display_errors', 1);
define("LANGUAGE", 'FA'); // site language

// PATHs
define("BASE_PATH", __DIR__ . "/../" . "/"); // DIRECTORY_SEPARATOR
define("REAL_BASE_PATH", realpath(BASE_PATH)); // newly added
define("CONTROLLER_PATH",  "\App\Controller\\");
define("MIDDLEWARE_PATH",  "\App\Middleware\\");
define("PATH_ERR_404", "Views/Errors/404.html");
define("BODY_PATH", BASE_PATH . "Views/Bodies/");
define("LAYOUT_PATH", BASE_PATH . "Views/Layouts/");
define("ERROR_PATH", BASE_PATH . "Views/Errors/");
define("CSS_PATH",  "Assets/Css/");
define("JS_PATH",   "Assets/Js/");
define("IMAGES_PATH",  "Assets/Images/");
define("FONTS_PATH",  "Assets/Fonts/");
define("FILES_PATH",  "Assets/Files/");
define("VIDEOS_PATH",  "Assets/Videos/");

// Rules

define("MIDDLEWARE_REQUIRED", 1);
define("PASS_OPTIONS_METHODS", 1); // send HTTP 200 status code and terminate document on OPTIONS request (used for preflight requests)

// Strings 
define("SERVER_KEY", "");
define("SECRET_KEY", "");
define("TIMEZONE", "Asia/Tehran"); // this is used in index 
define("DATA_SLICE_SIZE", 5);

// production phase 
define("SITE_URL", "");
define("SITE_NAME", "");
define("DOMAIN", "");

// development phase
// define("DOMAIN", "");
// define("SITE_URL", "");
// define("SITE_NAME", "");
