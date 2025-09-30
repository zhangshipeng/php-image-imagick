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

namespace zhangshipeng;

use zhangshipeng\image\Exception as ImageException;
use zhangshipeng\image\gif\Gif;
use Imagick;
use ImagickPixel;
use ImagickDraw;

class Image
{

    /* 缩略图相关常量定义 */
    const THUMB_SCALING   = 1; //常量，标识缩略图等比例缩放类型
    const THUMB_FILLED    = 2; //常量，标识缩略图缩放后填充类型
    const THUMB_CENTER    = 3; //常量，标识缩略图居中裁剪类型
    const THUMB_NORTHWEST = 4; //常量，标识缩略图左上角裁剪类型
    const THUMB_SOUTHEAST = 5; //常量，标识缩略图右下角裁剪类型
    const THUMB_FIXED     = 6; //常量，标识缩略图固定尺寸缩放类型
    /* 水印相关常量定义 */
    const WATER_NORTHWEST = 1; //常量，标识左上角水印
    const WATER_NORTH     = 2; //常量，标识上居中水印
    const WATER_NORTHEAST = 3; //常量，标识右上角水印
    const WATER_WEST      = 4; //常量，标识左居中水印
    const WATER_CENTER    = 5; //常量，标识居中水印
    const WATER_EAST      = 6; //常量，标识右居中水印
    const WATER_SOUTHWEST = 7; //常量，标识左下角水印
    const WATER_SOUTH     = 8; //常量，标识下居中水印
    const WATER_SOUTHEAST = 9; //常量，标识右下角水印
    /* 翻转相关常量定义 */
    const FLIP_X = 1; //X轴翻转
    const FLIP_Y = 2; //Y轴翻转

    /**
     * 图像资源对象
     *
     * @var Imagick
     */
    protected $im;

    /** @var  Gif */
    protected $gif;

    /**
     * 图像信息，包括 width, height, type, mime, size
     *
     * @var array
     */
    protected $info;

    protected function __construct(\SplFileInfo $file)
    {
        //获取图像信息
        $info = @getimagesize($file->getPathname());

        //检测图像合法性
        if (false === $info || (IMAGETYPE_GIF === $info[2] && empty($info['bits']))) {
            throw new ImageException('Illegal image file');
        }

        //设置图像信息
        $this->info = [
            'width'  => $info[0],
            'height' => $info[1],
            'type'   => image_type_to_extension($info[2], false),
            'mime'   => $info['mime'],
        ];

        //打开图像
        if ('gif' == $this->info['type']) {
            $this->gif = new Gif($file->getPathname());
            $this->im = new Imagick();
            $this->im->readImageBlob($this->gif->image());
        } else {
            $this->im = new Imagick();
            $this->im->readImage($file->getPathname());
        }

        if (empty($this->im)) {
            throw new ImageException('Failed to create image resources!');
        }
    }

    /**
     * 打开一个图片文件
     * @param \SplFileInfo|string $file
     * @return Image
     */
    public static function open($file)
    {
        if (is_string($file)) {
            $file = new \SplFileInfo($file);
        }
        if (!$file->isFile()) {
            throw new ImageException('image file not exist');
        }
        return new self($file);
    }

    /**
     * 保存图像
     * @param string      $pathname  图像保存路径名称
     * @param null|string $type      图像类型
     * @param int         $quality   图像质量
     * @param bool        $interlace 是否对JPEG类型图像设置隔行扫描
     * @return $this
     */
    public function save($pathname, $type = null, $quality = 80, $interlace = true)
    {
        //自动获取图像类型
        if (is_null($type)) {
            $type = $this->info['type'];
        } else {
            $type = strtolower($type);
        }

        // 设置图像质量
        $this->im->setImageCompressionQuality($quality);

        //保存图像
        if ('jpeg' == $type || 'jpg' == $type) {
            //JPEG图像设置隔行扫描
            $this->im->setInterlaceScheme($interlace ? Imagick::INTERLACE_PLANE : Imagick::INTERLACE_UNDEFINED);
            $this->im->setImageFormat('jpeg');
            $this->im->writeImage($pathname);
        } elseif ('gif' == $type && !empty($this->gif)) {
            $this->gif->save($pathname);
        } elseif ('png' == $type) {
            $this->im->setImageFormat('png');
            $this->im->writeImage($pathname);
        } else {
            $this->im->setImageFormat($type);
            $this->im->writeImage($pathname);
        }

        return $this;
    }

    /**
     * http输出图片
     * @return void
     */
    public function output()
    {
        $type = $this->info['type'];
        header("content-type: image/{$type}");
        echo $this->im->getImageBlob();
        exit;
    }

    /**
     * 返回图像宽度
     * @return int 图像宽度
     */
    public function width()
    {
        return $this->im->getImageWidth();
    }

    /**
     * 返回图像高度
     * @return int 图像高度
     */
    public function height()
    {
        return $this->im->getImageHeight();
    }

    /**
     * 返回图像类型
     * @return string 图像类型
     */
    public function type()
    {
        return $this->info['type'];
    }

    /**
     * 返回图像MIME类型
     * @return string 图像MIME类型
     */
    public function mime()
    {
        return $this->info['mime'];
    }

    /**
     * 返回图像尺寸数组 0 - 图像宽度，1 - 图像高度
     * @return array 图像尺寸
     */
    public function size()
    {
        return [$this->width(), $this->height()];
    }

    /**
     * 旋转图像
     * @param int $degrees 顺时针旋转的度数
     * @return $this
     */
    public function rotate($degrees = 90)
    {
        do {
            $this->im->rotateImage(new ImagickPixel('none'), $degrees);
        } while (!empty($this->gif) && $this->gifNext());

        $this->info['width']  = $this->width();
        $this->info['height'] = $this->height();

        return $this;
    }

    /**
     * 翻转图像
     * @param integer $direction 翻转轴,X或者Y
     * @return $this
     */
    public function flip($direction = self::FLIP_X)
    {
        do {
            switch ($direction) {
                case self::FLIP_X:
                    $this->im->flopImage();
                    break;
                case self::FLIP_Y:
                    $this->im->flipImage();
                    break;
                default:
                    throw new ImageException('不支持的翻转类型');
            }
        } while (!empty($this->gif) && $this->gifNext());

        return $this;
    }

    /**
     * 裁剪图像
     *
     * @param  integer $w      裁剪区域宽度
     * @param  integer $h      裁剪区域高度
     * @param  integer $x      裁剪区域x坐标
     * @param  integer $y      裁剪区域y坐标
     * @param  integer $width  图像保存宽度
     * @param  integer $height 图像保存高度
     *
     * @return $this
     */
    public function crop($w, $h, $x = 0, $y = 0, $width = null, $height = null)
    {
        //设置保存尺寸
        empty($width) && $width   = $w;
        empty($height) && $height = $h;

        do {
            // 裁剪图像
            $this->im->cropImage($w, $h, $x, $y);

            // 如果需要调整大小
            if ($w != $width || $h != $height) {
                $this->im->scaleImage($width, $height);
            }
        } while (!empty($this->gif) && $this->gifNext());

        $this->info['width']  = (int) $width;
        $this->info['height'] = (int) $height;
        return $this;
    }

    /**
     * 生成缩略图
     *
     * @param  integer $width  缩略图最大宽度
     * @param  integer $height 缩略图最大高度
     * @param int      $type   缩略图裁剪类型
     *
     * @return $this
     */
    public function thumb($width, $height, $type = self::THUMB_SCALING)
    {
        //原图宽度和高度
        $w = $this->width();
        $h = $this->height();

        /* 计算缩略图生成的必要参数 */
        switch ($type) {
            /* 等比例缩放 */
            case self::THUMB_SCALING:
                //原图尺寸小于缩略图尺寸则不进行缩略
                if ($w < $width && $h < $height) {
                    return $this;
                }
                //计算缩放比例
                $scale = min($width / $w, $height / $h);
                //设置缩略图的坐标及宽度和高度
                $width  = (int) ($w * $scale);
                $height = (int) ($h * $scale);
                $this->im->scaleImage($width, $height);
                break;
            /* 居中裁剪 */
            case self::THUMB_CENTER:
                //计算缩放比例
                $scale = max($width / $w, $height / $h);
                //设置缩略图的坐标及宽度和高度
                $new_w = (int) ($w * $scale);
                $new_h = (int) ($h * $scale);
                $x = (int) (($new_w - $width) / 2);
                $y = (int) (($new_h - $height) / 2);
                $this->im->scaleImage($new_w, $new_h);
                $this->im->cropImage($width, $height, $x, $y);
                break;
            /* 左上角裁剪 */
            case self::THUMB_NORTHWEST:
                //计算缩放比例
                $scale = max($width / $w, $height / $h);
                //设置缩略图的坐标及宽度和高度
                $new_w = (int) ($w * $scale);
                $new_h = (int) ($h * $scale);
                $x = $y = 0;
                $this->im->scaleImage($new_w, $new_h);
                $this->im->cropImage($width, $height, $x, $y);
                break;
            /* 右下角裁剪 */
            case self::THUMB_SOUTHEAST:
                //计算缩放比例
                $scale = max($width / $w, $height / $h);
                //设置缩略图的坐标及宽度和高度
                $new_w = (int) ($w * $scale);
                $new_h = (int) ($h * $scale);
                $x = (int) ($new_w - $width);
                $y = (int) ($new_h - $height);
                $this->im->scaleImage($new_w, $new_h);
                $this->im->cropImage($width, $height, $x, $y);
                break;
            /* 填充 */
            case self::THUMB_FILLED:
                //计算缩放比例
                if ($w < $width && $h < $height) {
                    $scale = 1;
                } else {
                    $scale = min($width / $w, $height / $h);
                }
                //设置缩略图的坐标及宽度和高度
                $neww = (int) ($w * $scale);
                $newh = (int) ($h * $scale);
                $x    = ($width - $neww) / 2;
                $y    = ($height - $newh) / 2;

                // 创建一个填充背景的图像
                $canvas = new Imagick();
                $canvas->newImage($width, $height, new ImagickPixel('white'));
                $canvas->setImageFormat($this->info['type']);

                // 调整原图大小
                $this->im->scaleImage($neww, $newh);

                // 合并图像
                $canvas->compositeImage($this->im, Imagick::COMPOSITE_OVER, $x, $y);
                $this->im = $canvas;
                break;
            /* 固定 */
            case self::THUMB_FIXED:
                $this->im->scaleImage($width, $height);
                break;
            default:
                throw new ImageException('不支持的缩略图裁剪类型');
        }

        $this->info['width']  = (int) $width;
        $this->info['height'] = (int) $height;
        return $this;
    }

    /**
     * 添加水印
     *
     * @param string    $source 水印图片路径
     * @param int|array $locate 水印位置
     * @param int       $alpha  透明度
     * @return $this
     */
    public function water($source, $locate = self::WATER_SOUTHEAST, $alpha = 100)
    {
        if (!is_file($source)) {
            throw new ImageException('水印图像不存在');
        }
        //获取水印图像信息
        $info = getimagesize($source);
        if (false === $info || (IMAGETYPE_GIF === $info[2] && empty($info['bits']))) {
            throw new ImageException('非法水印文件');
        }

        //创建水印图像资源
        $water = new Imagick($source);
        // 使用 setImageAlphaChannel 和 evaluateImage 替代已弃用的 setImageOpacity
        $water->setImageAlphaChannel(Imagick::ALPHACHANNEL_ACTIVATE);
        $water->evaluateImage(Imagick::EVALUATE_MULTIPLY, $alpha / 100, Imagick::CHANNEL_ALPHA);

        /* 设定水印位置 */
        switch ($locate) {
            /* 右下角水印 */
            case self::WATER_SOUTHEAST:
                $x = $this->width() - $water->getImageWidth();
                $y = $this->height() - $water->getImageHeight();
                break;
            /* 左下角水印 */
            case self::WATER_SOUTHWEST:
                $x = 0;
                $y = $this->height() - $water->getImageHeight();
                break;
            /* 左上角水印 */
            case self::WATER_NORTHWEST:
                $x = $y = 0;
                break;
            /* 右上角水印 */
            case self::WATER_NORTHEAST:
                $x = $this->width() - $water->getImageWidth();
                $y = 0;
                break;
            /* 居中水印 */
            case self::WATER_CENTER:
                $x = ($this->width() - $water->getImageWidth()) / 2;
                $y = ($this->height() - $water->getImageHeight()) / 2;
                break;
            /* 下居中水印 */
            case self::WATER_SOUTH:
                $x = ($this->width() - $water->getImageWidth()) / 2;
                $y = $this->height() - $water->getImageHeight();
                break;
            /* 右居中水印 */
            case self::WATER_EAST:
                $x = $this->width() - $water->getImageWidth();
                $y = ($this->height() - $water->getImageHeight()) / 2;
                break;
            /* 上居中水印 */
            case self::WATER_NORTH:
                $x = ($this->width() - $water->getImageWidth()) / 2;
                $y = 0;
                break;
            /* 左居中水印 */
            case self::WATER_WEST:
                $x = 0;
                $y = ($this->height() - $water->getImageHeight()) / 2;
                break;
            default:
                /* 自定义水印坐标 */
                if (is_array($locate)) {
                    list($x, $y) = $locate;
                } else {
                    throw new ImageException('不支持的水印位置类型');
                }
        }

        do {
            //添加水印
            $this->im->compositeImage($water, Imagick::COMPOSITE_OVER, $x, $y);
        } while (!empty($this->gif) && $this->gifNext());

        //销毁水印资源
        $water->clear();
        return $this;
    }

    /**
     * 图像添加文字
     *
     * @param string        $text   添加的文字
     * @param string        $font   字体路径
     * @param integer       $size   字号
     * @param ImagickPixel         $color  文字颜色
     * @param int|array     $locate 文字写入位置
     * @param integer|array $offset 文字相对当前位置的偏移量
     * @param integer       $angle  文字倾斜角度
     *
     * @return $this
     * @throws ImageException
     */
    public function text(
        $text,
        $font,
        $size,
        $color = '#00000000',
        $locate = self::WATER_SOUTHEAST,
        $offset = 0,
        $angle = 0
    ) {

        if (!is_file($font)) {
            throw new ImageException("不存在的字体文件：{$font}");
        }

        //创建Draw对象
        $draw = new ImagickDraw();
        $draw->setFont($font);
        $draw->setFontSize($size);

        //解析颜色
        if (is_string($color) && 0 === strpos($color, '#')) {
            $fill_pixel = new ImagickPixel($color);
        } else {
            $fill_pixel = new ImagickPixel('#000000');
        }
        $draw->setFillColor($fill_pixel);

        //获取文字信息
        $metrics = $this->im->queryFontMetrics($draw, $text);
        $w = $metrics['textWidth'];
        $h = $metrics['textHeight'];

        /* 设定文字位置 */
        switch ($locate) {
            /* 右下角文字 */
            case self::WATER_SOUTHEAST:
                $x = $this->width() - $w;
                $y = $this->height() - $h;
                break;
            /* 左下角文字 */
            case self::WATER_SOUTHWEST:
                $x = 0;
                $y = $this->height() - $h;
                break;
            /* 左上角文字 */
            case self::WATER_NORTHWEST:
                $x = 0;
                $y = $h;
                break;
            /* 右上角文字 */
            case self::WATER_NORTHEAST:
                $x = $this->width() - $w;
                $y = $h;
                break;
            /* 居中文字 */
            case self::WATER_CENTER:
                $x = ($this->width() - $w) / 2;
                $y = ($this->height() + $h) / 2;
                break;
            /* 下居中文字 */
            case self::WATER_SOUTH:
                $x = ($this->width() - $w) / 2;
                $y = $this->height() - $h;
                break;
            /* 右居中文字 */
            case self::WATER_EAST:
                $x = $this->width() - $w;
                $y = ($this->height() + $h) / 2;
                break;
            /* 上居中文字 */
            case self::WATER_NORTH:
                $x = ($this->width() - $w) / 2;
                $y = $h;
                break;
            /* 左居中文字 */
            case self::WATER_WEST:
                $x = 0;
                $y = ($this->height() + $h) / 2;
                break;
            default:
                /* 自定义文字坐标 */
                if (is_array($locate)) {
                    list($posx, $posy) = $locate;
                    $x = $posx;
                    $y = $posy;
                } else {
                    throw new ImageException('不支持的文字位置类型');
                }
        }

        /* 设置偏移量 */
        if (is_array($offset)) {
            $offset        = array_map('intval', $offset);
            list($ox, $oy) = $offset;
        } else {
            $offset = intval($offset);
            $ox     = $oy     = $offset;
        }

        $x += $ox;
        $y += $oy;

        do {
            /* 写入文字 */
            $this->im->annotateImage($draw, $x, $y, $angle, $text);
        } while (!empty($this->gif) && $this->gifNext());

        return $this;
    }

    /**
     * 切换到GIF的下一帧并保存当前帧
     */
    protected function gifNext()
    {
        $this->gif->image($this->im->getImageBlob());
        $next = $this->gif->nextImage();
        if ($next) {
            $this->im->clear();
            $this->im->readImageBlob($next);
            return $next;
        } else {
            $this->im->clear();
            $this->im->readImageBlob($this->gif->image());
            return false;
        }
    }

    /**
     * 析构方法，用于销毁图像资源
     */
    public function __destruct()
    {
        empty($this->im) || $this->im->clear();
    }
}
