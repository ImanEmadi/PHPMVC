<?php

namespace App\Services\View;

class View
{
    public static function load($layout = null, $body, array $data = null, array $assets = null, $TITLE = SITE_NAME)
    {
        if ($layout !== null) {
            $layout = LAYOUT_PATH . $layout;
            self::pathIsValid($layout);
        } else {
            $layout = 'default.php';
            $layout = LAYOUT_PATH . $layout;
            self::pathIsValid($layout);
        }
        $body = BODY_PATH . $body;
        self::pathIsValid($body);
        $links = array();
        if ($assets !== null) {
            foreach ($assets as $asset) {
                if (is_array($asset)) {
                    list($asset, $attr) = $asset;
                } else {
                    $attr = '';
                }

                $pathNavigation = '/';
                if (is_readable(CSS_PATH . $asset) && file_exists(CSS_PATH . $asset)) {
                    array_push($links, '<link ' . $attr . ' rel="stylesheet" href="' . $pathNavigation . CSS_PATH . $asset . '">');
                } elseif (is_readable(JS_PATH . $asset) && file_exists(JS_PATH . $asset)) {
                    array_push($links, '<script ' . $attr . ' src="' . $pathNavigation . JS_PATH . $asset . '"></script>');
                } else {
                    $url = filter_var($asset, FILTER_SANITIZE_URL);
                    if (filter_var($url, FILTER_VALIDATE_URL)) {
                        $parsedURL = parse_url($url);
                        $path = $parsedURL['path'];
                        $ext = substr($path, -4);
                        $ext = strtolower($ext);
                        if (strpos($ext, 'css')) {
                            $urlSrc = '<link rel="stylesheet" href=' . $url . '>';
                            array_push($links, $urlSrc);
                        } elseif (strpos($ext, 'js')) {
                            $urlSrc = '<script src=' . $url . '></script>';
                            array_push($links, $urlSrc);
                        }
                    }
                }
            }
        }
        if ($layout !== null) {
            ob_start();
            include $body;
            $RenderedBody = ob_get_contents();
            ob_clean();
            include $layout;
            $fullView = ob_get_contents();
            ob_end_clean();
            echo $fullView;
        } else {
            // ob_start() ;
            include $body;
            // $RenderedBody = ob_get_contents();
            // ob_end_clean();
            // echo $RenderedBody ;

        }
    }

    private static function pathIsValid($path)
    {
        if (is_readable($path) && file_exists($path)) {
            return $path;
        } else {
            die("Invalid path : ERR_VIEW ");
        }
    }
}
