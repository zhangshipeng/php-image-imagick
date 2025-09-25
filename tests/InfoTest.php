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

use think\Image;

class InfoTest extends TestCase
{

    public function testJpeg()
    {
        // 直接传递文件路径而不是 File 对象
        $image = Image::open($this->getJpeg());
        $this->assertEquals(800, $image->width());
        $this->assertEquals(600, $image->height());
        $this->assertEquals('jpeg', $image->type());
    }

    public function testPng()
    {
        // 直接传递文件路径而不是 File 对象
        $image = Image::open($this->getPng());
        $this->assertEquals(800, $image->width());
        $this->assertEquals(600, $image->height());
        $this->assertEquals('png', $image->type());
    }

    public function testGif()
    {
        // 直接传递文件路径而不是 File 对象
        $image = Image::open($this->getGif());
        $this->assertEquals(380, $image->width());
        $this->assertEquals(216, $image->height());
        $this->assertEquals('gif', $image->type());
    }
}
