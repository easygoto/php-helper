<?php

namespace Trink\Core\Helper;

/**
 * 建议先裁切再缩放
 * Class Image
 * @package Trink\Core\Helper
 * @author  trink
 */
class Image
{
    private string $fileName = '';
    private string $suffix = '';
    private string $mime = 'image/jpeg';

    private int $defaultColor = 0xffffff;
    private int $defaultAlphaColor = 0x40ffffff;

    /** @var Result|null */
    private ?Result $loadResult;

    /** @var static|null */
    private static ?Image $instance;

    private array $origin = [
        'width' => 0,
        'height' => 0,
        'resource' => null,
        'absolutePath' => '',
    ];

    private array $props = [
        'width' => 0,
        'height' => 0,
        'resource' => null,
        'extFix' => '',
        'absolutePath' => '',
    ];

    protected function __construct()
    {
    }

    public function __destruct()
    {
        static::destroy($this->props['resource']);
        static::destroy($this->origin['resource']);
    }

    protected function init($absolutePath)
    {
        $this->origin['absolutePath'] = $absolutePath;
        if (!file_exists($absolutePath)) {
            return Result::fail('文件不存在');
        }

        $fileData = pathinfo($absolutePath);
        if (!$fileData) {
            return Result::fail('文件错误');
        }

        ['extension' => $this->suffix, 'filename' => $this->fileName] = $fileData;

        $imageData = getimagesize($absolutePath);
        if (!$imageData) {
            return Result::fail('非图片格式');
        }

        [0 => $this->origin['width'], 1 => $this->origin['height'], 'mime' => $this->mime] = $imageData;

        $this->origin['resource'] = static::create($absolutePath, $this->mime);
        $this->reset();
        return Result::success();
    }

    public static function load(string $absolutePath)
    {
        $instance = new static();
        $instance->loadResult = $instance->init($absolutePath);
        static::$instance = $instance;
        return static::$instance;
    }

    public function isValid()
    {
        return $this->loadResult && $this->loadResult->isSuccess();
    }

    public function getResult()
    {
        return $this->loadResult;
    }

    /**
     * 使用绝对路径创建资源
     *
     * @param        $absolutePath
     * @param string $mime
     *
     * @return false|resource
     */
    public static function create($absolutePath, $mime = 'image/jpeg')
    {
        switch ($mime) {
            default:
            case 'image/jpeg':
                return imagecreatefromjpeg($absolutePath);
            case 'image/png':
                return imagecreatefrompng($absolutePath);
            case 'image/bmp':
                return imagecreatefrombmp($absolutePath);
            case 'image/gif':
                return imagecreatefromgif($absolutePath);
        }
    }

    /**
     * 销毁一个图片资源
     *
     * @param $imageResource
     */
    public static function destroy($imageResource)
    {
        if (is_resource($imageResource)) {
            imagedestroy($imageResource);
        }
    }

    /**
     * 保存一个图片资源到文件
     *
     * @param        $image
     * @param        $absolutePath
     * @param string $mime
     *
     * @return string
     */
    public static function save($image, $absolutePath, string $mime = 'image/jpeg')
    {
        switch ($mime) {
            default:
            case 'image/jpeg':
                imagejpeg($image, $absolutePath);
                break;
            case 'image/png':
                imagepng($image, $absolutePath);
                break;
            case 'image/bmp':
                imagebmp($image, $absolutePath);
                break;
            case 'image/gif':
                imagegif($image, $absolutePath);
                break;
        }
        return $absolutePath;
    }

    /**
     * 图片处理完成后保存到文件
     * @return string
     */
    public function savePath()
    {
        return static::save($this->props['resource'], $this->props['absolutePath'], $this->mime);
    }

    /**
     * 裁切
     *
     * @param int|null $width
     * @param int|null $height
     *
     * @return Image
     */
    public function crop($width = null, $height = null)
    {
        $width = $width ?: min($this->props['width'], $this->props['height']);
        $height = $height ?: $width;
        $srcImg = $this->props['resource'];
        $dstImg = imagecreatetruecolor($width, $height);
        imagefill($dstImg, 0, 0, $this->defaultColor);

        $targetWidth = (int)(($this->props['width'] - $width) / 2);
        $targetHeight = (int)(($this->props['height'] - $height) / 2);
        imagecopy($dstImg, $srcImg, 0, 0, $targetWidth, $targetHeight, $width, $height);

        $this->props['width'] = $width;
        $this->props['height'] = $height;
        $this->props['extFix'] .= '.crop';
        $this->props['resource'] = $dstImg;
        $originAbsDir = dirname($this->origin['absolutePath']);
        $this->props['absolutePath'] = "{$originAbsDir}/{$this->fileName}{$this->props['extFix']}.{$this->suffix}";
        return $this;
    }

    /**
     * 缩放
     *
     * @param int|null $width
     * @param int|null $height
     *
     * @return Image
     */
    public function scale($width = null, $height = null)
    {
        if (!$width && !$height) {
            $width = $this->props['width'];
            $height = $this->props['height'];
        } elseif ($width) {
            $height = (int)($width * $this->props['height'] / $this->props['width']);
        } elseif ($height) {
            $width = (int)($height * $this->props['width'] / $this->props['height']);
        }
        $srcImg = $this->props['resource'];
        $this->props['width'] = $width;
        $this->props['height'] = $height;
        $this->props['extFix'] .= '.scale';
        $this->props['resource'] = imagescale($srcImg, $width, $height);
        $originAbsDir = dirname($this->origin['absolutePath']);
        $this->props['absolutePath'] = "{$originAbsDir}/{$this->fileName}{$this->props['extFix']}.{$this->suffix}";
        return $this;
    }

    /**
     * 水印
     *
     * @param          $markImagePath
     * @param string   $position LU(左上), RU(右上), LD(左下), RD(右下)
     * @param int|null $width
     *
     * @return Image
     */
    public function watermark($markImagePath, string $position = 'RD', $width = null)
    {
        $alpha = 50;
        $width = $width ?: (int)($this->props['width'] / 5);
        $srcImgObj = static::load($markImagePath)->scale($width);
        ['width' => $dstW, 'height' => $dstH, 'resource' => $dstImg, 'extFix' => $extFix] = $this->props;
        ['width' => $srcW, 'height' => $srcH, 'resource' => $srcImg] = $srcImgObj->props;
        switch ($position) {
            default:
            case 'RD':
                imagecopymerge($dstImg, $srcImg, $dstW - $srcW, $dstH - $srcH, 0, 0, $srcW, $srcH, $alpha);
                break;
            case 'RU':
                imagecopymerge($dstImg, $srcImg, $dstW - $srcW, 0, 0, 0, $srcW, $srcH, $alpha);
                break;
            case 'LD':
                imagecopymerge($dstImg, $srcImg, 0, $dstH - $srcH, 0, 0, $srcW, $srcH, $alpha);
                break;
            case 'LU':
                imagecopymerge($dstImg, $srcImg, 0, 0, 0, 0, $srcW, $srcH, $alpha);
                break;
        }
        $extFix .= '.mark';
        $this->props['extFix'] = $extFix;
        $this->props['resource'] = $dstImg;
        $originAbsDir = dirname($this->origin['absolutePath']);
        $this->props['absolutePath'] = "{$originAbsDir}/{$this->fileName}{$extFix}.{$position}.{$this->suffix}";
        return $this;
    }

    /**
     * 重置操作, 恢复最开始的状态
     * @return $this
     */
    public function reset()
    {
        static::destroy($this->props['resource']);
        $this->props = $this->origin;
        $this->props['extFix'] = '';
        $this->origin['resource'] = static::create($this->origin['absolutePath'], $this->mime);
        return $this;
    }

    /**
     * 文件转码到base64
     *
     * @param string $absolutePath
     *
     * @return string
     */
    public static function toBase64(string $absolutePath): string
    {
        $content = file_get_contents($absolutePath);
        return base64_encode($content);
    }

    /**
     * base64转码到文件, 指定的路径不需要后缀名
     *
     * @param string $base64String
     * @param string $absolutePath
     *
     * @return string
     */
    public static function fromBase64(string $base64String, string $absolutePath): string
    {
        if (str_contains($base64String, ',')) {
            [$type, $data] = explode(',', $base64String);
            $start = strpos($type, '/') + 1;
            $afterFix = substr($type, $start, strpos($type, ';') - $start);
        } else {
            $data = $base64String;
            $afterFix = 'jpeg';
        }

        if (!is_dir(dirname($absolutePath)) &&
            !mkdir($concurrentDirectory = dirname($absolutePath), 0777, true) &&
            !is_dir($concurrentDirectory)
        ) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        $filePath = "{$absolutePath}.{$afterFix}";
        file_put_contents($absolutePath, base64_decode($data));
        return $filePath;
    }
}
