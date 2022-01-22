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
            header('HTTP/1.1 405');
            exit();
        }
        // get current URL
        self::$currentRoute = self::currentRoute();
        // get all valid URLs
        // self::$allRoutes = require BASE_PATH . "Routes/Web.php";
        $tempRoutesArr = require BASE_PATH . "Routes/Web.php";
        foreach ($tempRoutesArr as $route => $routeArr)
            self::$allRoutes[strtolower($route)] = $routeArr;

        // check if URL is valid
        if (!self::routeExists()) {
            header("HTTP/1.0 404");
            // include BASE_PATH . PATH_ERR_404;
            die();
        }
        // check if access method is invalid
        $validMethods = explode("|", strtoupper(self::getRouteMethod()));
        if (!in_array($_SERVER['REQUEST_METHOD'], $validMethods)) { // OPTIONS is required for pre-flight requests - archived : && $_SERVER['REQUEST_METHOD'] !== 'OPTIONS'
            header("HTTP/1.0 400");
            die("ERROR 400 BAD REQUEST - Invalid request Method !");
        }
        // call middleWares
        GlobalMiddleWares::runGlobalMiddleWares();

        // look for common middlewares in this route
        $commonMiddleWaresArr = require BASE_PATH . "Routes/MW.php";
        foreach ($commonMiddleWaresArr as $route => $mws) {
            $route_pattern = preg_replace("/\//", "\/", $route);
            if (preg_match("/^(" . $route_pattern  . ")/", self::$currentRoute) === 1) {
                list($middleWaresStr, $middleWaresMethodsStr) = explode("@", $mws);
                $middleWares = explode(".", $middleWaresStr);
                $middleWaresMethods = explode(".", $middleWaresMethodsStr);
                foreach ($middleWares as $index => $middleware) {
                    $middlewarePath = MIDDLEWARE_PATH . $middleware;
                    if (class_exists($middlewarePath)) {
                        $middlewareInstance = new $middlewarePath;
                        $method = $middleWaresMethods[$index];
                        $middlewareInstance::$method();
                    } elseif (MIDDLEWARE_REQUIRED === 1) {
                        die("common Middleware doesn't exist !");
                    }
                } // end foreach
            }
        }
        list($allMiddleWares, $allMiddleWaresMethod) = explode("@", self::getRouteMiddleware());
        if ($allMiddleWares !== "null") {
            $middlewares = explode(".", $allMiddleWares);
            $middlewaresMethod = explode(".", $allMiddleWaresMethod);

            foreach ($middlewares as $index => $middleware) {
                $middlewarePath = MIDDLEWARE_PATH . $middleware;
                if (class_exists($middlewarePath)) {
                    $middlewareInstance = new $middlewarePath;
                    $method = $middlewaresMethod[$index];
                    $middlewareInstance::$method();
                } elseif (MIDDLEWARE_REQUIRED === 1) {
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
        return $_SERVER['REQUEST_URI'] !== '/' ? rtrim(strtolower(strtok(urldecode($_SERVER['REQUEST_URI']), "?")), '\\') : '/';
    }

    public static function routeExists()
    {
        foreach (self::$allRoutes as $route => $routeArr)
            if (self::$currentRoute === strtolower($route))
                return true;
        // check for parameterized  Routes
        // loop through them and create a Regex pattern
        foreach (self::$allRoutes as $route => $routeInfo) {
            if (strpos($route, "{")) { // if route has at least one parameter
                $routePattern = preg_replace('/\//i', '\\/', $route); // escaping slashes in order to use as regex pattern
                $routePattern = preg_replace('/({[^\?]+})/i', "([^\s\/]+)", $routePattern); // create route pattern for obligated params
                $routePattern = preg_replace('#(\\\/{[^\s\/]+\?})#i', "(\/[^\s\/]*)?", $routePattern); // create route pattern for optional params
                $routePattern = $routePattern . "\/?"; // predict a possible / at the end of url
                preg_match_all("#^" . $routePattern . "#i", self::$currentRoute, $matches);
                if (isset($matches[0][0]) && $matches[0][0] === self::$currentRoute) { // if URI and regex match
                    $explodedRoute = explode('/', $route);
                    $explodedCurrentRoute = explode('/', self::$currentRoute);
                    foreach ($explodedRoute as $explodedRouteDirIndex => $explodedRouteDir) {
                        if (strpos($explodedRouteDir, "{") !== false) { // if the current dir is parameterized  add it to RP array
                            if (isset($explodedCurrentRoute[$explodedRouteDirIndex]))
                                $_GET['RP'][trim($explodedRouteDir, "{}?")] = $explodedCurrentRoute[$explodedRouteDirIndex];
                        }
                    }
                    self::$currentRoute = $route; // required in further steps such as getRouteMethod
                    return true;
                }
            }
        } // end allRoutes foreach
        return false;
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
