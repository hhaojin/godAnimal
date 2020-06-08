<?php

namespace Core\Helper;

class FileHelper
{
    public static function getFile($dir, $ignore)
    {
        $files = glob($dir);
        $arr = [];

        foreach ($files as $file) {
            if (strpos($file, $ignore)) {
                continue;
            } elseif (is_dir($file) && strpos($file, $ignore) === false) {
                $arr[] = self::getFile($file . "/*", $ignore);
            } else if (pathinfo($file)['extension'] == 'php') {
                $arr[] = md5_file($file);
            }
        }
        return md5(implode(',', $arr));
    }
}