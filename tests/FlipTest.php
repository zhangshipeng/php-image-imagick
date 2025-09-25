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

class FlipTest extends TestCase
{
    public function testJpeg()
    {
        $pathname = TEST_PATH . 'tmp/flip.jpg';
        // 直接传递文件路径而不是 File 对象
        $image    = Image::open($this->getJpeg());
        $image->flip()->save($pathname);

        $file = new \SplFileInfo($pathname);

        $this->assertTrue($file->isFile());

        @unlink($pathname);
    }
}
