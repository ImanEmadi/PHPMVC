<?php

namespace App\Utility;

class Sanitizer
{

    protected static $BBCodes = [
        'p',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'b',
        'i',
        'a',
    ];

    private static function convertBBCodeEnclosing(string $str)
    {
        $str = preg_replace("/\[/", "<", $str);
        $str = preg_replace("/\]/", ">", $str);
        return $str;
    }

    public static function convertBBCodes($string)
    {
        foreach (self::$BBCodes as $index => $bbcode) {
            // select the opening tag ( e.x : <a attr="" >)
            $string = preg_replace_callback("/\[$bbcode [\s\S]*\]/imxU", function ($match) {
                return htmlspecialchars_decode(self::convertBBCodeEnclosing($match[0]), ENT_QUOTES);
            }, $string);
            // selecting the closing tag (e.x : </a> )
            $string = preg_replace_callback("/\[\/$bbcode\]/imxU", function ($match) {
                return self::convertBBCodeEnclosing($match[0]);
            }, $string);
        }

        return $string;
    }
}
