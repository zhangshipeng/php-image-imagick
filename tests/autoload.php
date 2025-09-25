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
define('TEST_PATH', __DIR__ . '/');
// 使用 Composer 自动加载
require __DIR__ . '/../vendor/autoload.php';

// 手动包含需要的文件
require_once __DIR__ . '/../src/Image.php';
require_once __DIR__ . '/../src/image/Exception.php';
require_once __DIR__ . '/../src/image/gif/Gif.php';
require_once __DIR__ . '/../src/image/gif/Decoder.php';
require_once __DIR__ . '/../src/image/gif/Encoder.php';
require_once __DIR__ . '/TestCase.php';
