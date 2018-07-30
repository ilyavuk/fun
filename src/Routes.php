<?php

namespace App;

/**
 * Main routing class
 */

class Routes
{

    /**
     * exe routing
     */
    public static function Run()
    {

        $current_url = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);

        // Test url , home url
        if (preg_match('~^/{0,1}$~', $current_url)) {
            (new \App\Controller\Home())->init();
        } elseif (preg_match('~^\/([\w]+\.html)$~', $current_url, $m)) {
            (new \App\Controller\Home())->handlePost( $m[1] );
        } elseif (preg_match('~^\/login$~', $current_url, $m)) {
            (new \App\Controller\Home())->login();
        } elseif (preg_match('~^\/logout$~', $current_url, $m)) {
            (new \App\Controller\Home())->LogOut();
        } elseif (preg_match('~^\/signin$~', $current_url, $m)) {
            (new \App\Controller\Home())->signin();
        } elseif (preg_match('~^\/checkEmail$~', $current_url, $m)) {
            (new \App\Controller\Home())->checkEmail();
        } elseif (preg_match('~^\/Register$~', $current_url, $m)) {
            (new \App\Controller\Home())->Register();
        } elseif (preg_match('~^\/save-comment$~', $current_url, $m)) {
            (new \App\Controller\Home())->saveComment();

            // Admin panel
        } elseif (preg_match('~^\/admin(.*$)~', $current_url, $m)) {

            if ($m[1] == '/Login') {
                (new \App\Controller\Admin())->Login();
            } elseif ($m[1] == '/Logout') {
                (new \App\Controller\Admin())->LogOut();
            } elseif ($m[1] == '/my_posts') {
                (new \App\Controller\Admin())->MyPosts();
            } elseif ($m[1] == '/create_post') {
                (new \App\Controller\Admin())->AddPost();
            } elseif ($m[1] == '/updateUser') {
                (new \App\Controller\Admin())->UpdateUser();
            } elseif ($m[1] == '/CreatePost') {
                (new \App\Controller\Admin())->CreatePost();
            } elseif ($m[1] == '/DeletePost') {
                (new \App\Controller\Admin())->DeletePost();

            } else {
                (new \App\Controller\Admin())->init();
            }

        } elseif (preg_match('~^\/upload(.*$)~', $current_url, $m)) {
            if ($m[1] == '/uploadavatar') {
                (new \App\Controller\Upload())->uploadAvatar();
            } elseif ($m[1] == '/post') {
                (new \App\Controller\Upload())->post();
            }

        } elseif (preg_match('~^\/format$~', $current_url, $m)) {
            (new \App\Controller\JsFormat())->init();
        } elseif (preg_match('~^\/img\/([\w-]+\.\w{3,4})\/(\d+)$~', $current_url, $m)) {
            if (isset($m[1]) && isset($m[2])) {
                (new \App\Controller\Img())->resize($m[1], $m[2]);
            }
        }elseif (preg_match('~^\/imgwh\/([\w-]+\.\w{3,4})\/(\d+)/(\d+)$~', $current_url, $m)) {
            if (isset($m[1]) && isset($m[2])) {
                (new \App\Controller\Img())->resizeWH($m[1], $m[2], $m[3]);
            }
        }
        // testing twig instalation
        elseif (preg_match('~^/twig$~', $current_url)) {
            new \App\Controller\Twig();
        } elseif (preg_match('~^/test$~', $current_url)) {
            (new \App\Controller\Test())->init();
        } else {
            die('Error 404');
        }

    }

}
