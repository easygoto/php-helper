<?php

namespace Tests\Core\Helper;

use Trink\Core\Helper\Image;

class ImageTest extends BaseTest
{
    public function test()
    {
        $imagePath = '';
        $image = Image::load($imagePath);
        $image->reset()->crop()->savePath();
        $image->reset()->scale(200)->savePath();
        $image->reset()->watermark($imagePath)->savePath();
        $image->reset()->watermark($imagePath, 'RU')->savePath();
        $image->reset()->watermark($imagePath, 'LD')->savePath();
        $image->reset()->watermark($imagePath, 'LU')->savePath();
        $image->reset()->crop()->scale(200)->savePath();
        $image->reset()->crop()->watermark($imagePath)->savePath();
        $image->reset()->scale(200)->crop()->savePath();
        $image->reset()->scale(200)->watermark($imagePath)->savePath();
        $image->reset()->crop()->scale(200)->watermark($imagePath)->savePath();
        $image->reset()->scale(200)->crop()->watermark($imagePath)->savePath();
        static::assertTrue(true);
    }
}
