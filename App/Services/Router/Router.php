<?php

namespace App\Services\Router;

use App\Middleware\GlobalMiddleWares;

class Router
{
    public static $currentRoute;
    public static $allRoutes;
    public static function start()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS' && PASS_OPTIONS_METHODS) { // disallow any operation for OPTIONS requests (developer policy) : in order to prevent userSignCheck on preflight requests
            header('HTTP/1.1 200');
            exit();
        }
        // get current URL
        self::$currentRoute = self::currentRoute();
        // get all valid URLs
        self::$allRoutes = require BASE_PATH . "Routes/web.php";
        // check if URL is valid
        if (!self::routeExists()) {
            header("HTTP/1.0 404");
            // include BASE_PATH . PATH_ERR_404;
            die();
        }
        // check if access method is invalid
        $validMethods = explode("|", strtoupper(self::getRouteMethod()));
        if (!in_array($_SERVER['REQUEST_METHOD'], $validMethods)) { // OPTIONS is required for pre-flight requests - archived : && $_SERVER['REQUEST_METHOD'] !== 'OPTIONS'
            header("HTTP/1.0 403");
            die("ERROR 403 BAD REQUEST !");
        }
        // call middleWares 
        GlobalMiddleWares::runGlobalMiddleWares();
        list($Allmiddlewares, $AllmiddlewaresMethod) = explode("@", self::getRouteMiddleware());
        if ($Allmiddlewares !== "null") {
            $middlewares = explode(".", $Allmiddlewares);
            $middlewaresMethod = explode(".", $AllmiddlewaresMethod);

            foreach ($middlewares as $index => $middleware) {
                $middlewarePath = MIDDLEWARE_PATH . $middleware;
                if (class_exists($middlewarePath)) {
                    $middlewareInstance = new $middlewarePath;
                    $method = $middlewaresMethod[$index];
                    $middlewareInstance::$method();
                } elseif (MIDDLEWARE_REQUIRED == 1) {
                    die("middleware doesn't exist !");
                }
            } // end foreach
        }

        // call controller
        list($controller, $controllerMethod) = explode("@", self::getRouteController());
        $controllerPath = CONTROLLER_PATH . $controller;
        if (class_exists($controllerPath)) {
            $controllerInstance = new $controllerPath;
            $controllerInstance::$controllerMethod();
        } else {
            die('missing controller');
        }
    }
    public static function getRouteMethod()
    {
        return self::$allRoutes[self::$currentRoute]['method'];
    }
    public static function currentRoute()
    {
        return $_SERVER['REQUEST_URI'] !== '/' ? rtrim(strtolower(strtok(urldecode($_SERVER['REQUEST_URI']), "?")), '\\/') : '/'; //  #BETA
    }

    public static function routeExists()
    {
        // return array_key_exists(self::$currentRoute, self::$allRoutes); // origin
        if (array_key_exists(self::$currentRoute, self::$allRoutes)) {
            return true;
        } else { // check for parameterized  Routes
            // get all web.php
            // $allRoutes = self::$allRoutes; // was used in foreach loop
            // loop through them and create a Regex pattern
            foreach (self::$allRoutes as $route => $routeInfo) {
                if (strpos($route, "{")) { // if route has at least one parameter
                    $routePattern = preg_replace('/({.+})/iU', "([^\s\/]+)", $route); // create route pattern
                    $routePattern = preg_replace('/\//i', '\\/', $routePattern); // convert slashes to escaped slashes in order to use as regex pattern
                    $routePattern = preg_replace("#\\\/#i", "\/?", $routePattern, 1); // add optional (?) to regex for slash at the beginning of regex to match URI
                    // die("#^" . $routePattern . "#i");
                    if (preg_match("#^" . $routePattern . "#i", self::$currentRoute)) { // if URI and regex match
                        // turn both route and URI to arrays splitted by / 
                        $explodedRoute = explode('/', $route);
                        $explodedCurrentRoute = explode('/', self::currentRoute());
                        foreach ($explodedRoute as $explodedRouteDirIndex => $explodedRouteDir) {
                            if (strpos($explodedRouteDir, "{") !== false) { // if the current dir is parameterized  add it to RP array
                                $_GET['RP'][trim($explodedRouteDir, "{}")] = $explodedCurrentRoute[$explodedRouteDirIndex];
                            }
                        }
                        self::$currentRoute = $route;
                        return true;
                    }
                }
            } // end allRoutes foreach
            return false;
        }
    }

    public static function getRouteController()
    {
        return self::$allRoutes[self::$currentRoute]['controller'];
    }

    public static function getRouteMiddleware()
    {
        return self::$allRoutes[self::$currentRoute]['middleware'];
    }
}
