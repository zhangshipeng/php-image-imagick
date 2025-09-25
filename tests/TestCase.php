<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

namespace tests;

// 移除对 think\File 的依赖，使用原生 PHP 类
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function getJpeg()
    {
        // 直接返回文件路径而不是 File 对象
        return TEST_PATH . 'images/test.jpg';
    }

    protected function getPng()
    {
        // 直接返回文件路径而不是 File 对象
        return TEST_PATH . 'images/test.png';
    }

    protected function getGif()
    {
        // 直接返回文件路径而不是 File 对象
        return TEST_PATH . 'images/test.gif';
    }
}