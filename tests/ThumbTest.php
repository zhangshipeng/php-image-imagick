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

class ThumbTest extends TestCase
{
    public function testJpeg()
    {
        $pathname = TEST_PATH . 'tmp/thumb.jpg';
        // 直接传递文件路径而不是 File 对象
        $image    = Image::open($this->getJpeg());
        $image->thumb(200, 200)->save($pathname);

        $this->assertEquals(200, $image->width());
        $this->assertEquals(150, $image->height());

        $file = new \SplFileInfo($pathname);

        $this->assertTrue($file->isFile());

        //@unlink($pathname);
    }

    public function testJpeg2()
    {
        $pathname = TEST_PATH . 'tmp/thumb2.jpg';
        // 直接传递文件路径而不是 File 对象
        $image    = Image::open($this->getJpeg());
        $image->thumb(200, 200, Image::THUMB_CENTER)->save($pathname);

        $this->assertEquals(200, $image->width());
        $this->assertEquals(200, $image->height());

        $file = new \SplFileInfo($pathname);

        $this->assertTrue($file->isFile());

       // @unlink($pathname);
    }

    public function testJpeg3()
    {
        $pathname = TEST_PATH . 'tmp/thumb3.jpg';
        // 直接传递文件路径而不是 File 对象
        $image    = Image::open($this->getJpeg());
        $image->thumb(200, 200, Image::THUMB_FILLED)->save($pathname);

        $this->assertEquals(200, $image->width());
        $this->assertEquals(200, $image->height());

        $file = new \SplFileInfo($pathname);

        $this->assertTrue($file->isFile());

        //@unlink($pathname);
    }

    public function testJpeg4()
    {
        $pathname = TEST_PATH . 'tmp/thumb4.jpg';
        // 直接传递文件路径而不是 File 对象
        $image    = Image::open($this->getJpeg());
        $image->thumb(200, 200, Image::THUMB_NORTHWEST)->save($pathname);

        $this->assertEquals(200, $image->width());
        $this->assertEquals(200, $image->height());

        $file = new \SplFileInfo($pathname);

        $this->assertTrue($file->isFile());

        //@unlink($pathname);
    }

    public function testJpeg5()
    {
        $pathname = TEST_PATH . 'tmp/thumb5.jpg';
        // 直接传递文件路径而不是 File 对象
        $image    = Image::open($this->getJpeg());
        $image->thumb(200, 200, Image::THUMB_SOUTHEAST)->save($pathname);

        $this->assertEquals(200, $image->width());
        $this->assertEquals(200, $image->height());

        $file = new \SplFileInfo($pathname);

        $this->assertTrue($file->isFile());

        //@unlink($pathname);
    }

    public function testJpeg6()
    {
        $pathname = TEST_PATH . 'tmp/thumb6.jpg';
        // 直接传递文件路径而不是 File 对象
        $image    = Image::open($this->getJpeg());
        $image->thumb(200, 200, Image::THUMB_FIXED)->save($pathname);

        $this->assertEquals(200, $image->width());
        $this->assertEquals(200, $image->height());

        $file = new \SplFileInfo($pathname);

        $this->assertTrue($file->isFile());

        //@unlink($pathname);
    }
}
