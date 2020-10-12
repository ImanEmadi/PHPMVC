<?php

namespace App\Services\View;

class view
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
        foreach (LINKS as $index => $scr) {
            array_push($links, $scr);
        }
        foreach (SCRIPTS as $index => $scr) {
            array_push($links, $scr);
        }

        if ($assets !== null) {
            foreach ($assets as $asset) {
                $folder_depth = substr_count($_SERVER['REQUEST_URI'], "/");
                $folder_depth -= ROOT_DEFAULT_DEPTH; // newly added
                if (!$folder_depth || $folder_depth < 0) { // second condition newly added
                    $folder_depth = 0; // newly changed - last value : 1
                }
                $pathNavigation = '';
                if (is_readable(CSS_PATH . $asset) && file_exists(CSS_PATH . $asset)) {
                    for ($i = 0; $i < $folder_depth; $i++) {
                        $pathNavigation .= '../';
                    }
                    array_push($links, '<link rel="stylesheet" href="' . $pathNavigation . CSS_PATH . $asset . '">');
                } elseif (is_readable(JS_PATH . $asset) && file_exists(JS_PATH . $asset)) {
                    for ($i = 0; $i < $folder_depth; $i++) {
                        $pathNavigation .= '../';
                    }
                    array_push($links, '<script src="' . $pathNavigation . JS_PATH . $asset . '"></script>');
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
