<?php

namespace App\Config;


class GenFun
{
    public static function log_data($data)
    {
        file_put_contents('/var/www/html/logs/info.log', print_r($data, true) . "\n", FILE_APPEND);
    }

    /**
     * Create a "Random" String
     *
     * @param    string    type of random string.  basic, alpha, alnum, numeric, nozero, unique, md5, encrypt and sha1
     * @param    int    number of characters
     * @return    string
     */
    public static function random_string($type = 'alnum', $len = 8)
    {
        switch ($type) {
            case 'basic':
                return mt_rand();
            case 'alnum':
            case 'numeric':
            case 'nozero':
            case 'alpha':
                switch ($type) {
                    case 'alpha':
                        $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'alnum':
                        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'numeric':
                        $pool = '0123456789';
                        break;
                    case 'nozero':
                        $pool = '123456789';
                        break;
                }
                return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
            case 'unique': // todo: remove in 3.1+
            case 'md5':
                return md5(uniqid(mt_rand()));
            case 'encrypt': // todo: remove in 3.1+
            case 'sha1':
                return sha1(uniqid(mt_rand(), true));
        }
    }

    public static function rootPath()
    {
        return '/var/www/html/';
    }

    public static function redirect(string $url){
        header('Location: '.$url);
        exit();
    }
}
