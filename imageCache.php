<?php

namespace webheads\imagecache;

use Yii;
use yii\helpers\Html;
use yii\helpers\BaseFileHelper;

class imageCache extends \yii\base\Component
{
    public $defaultSize = '500x';
    public $cachePath;
    public $cacheUrl;
    public $graphicsLibrary = 'Imagick';
    
    public function init()
    {
        parent::init();
        
        if (empty($this->cachePath)) {
            throw new \yii\base\InvalidConfigException('Please, set "cachePath" at $config["components"]["imageCache"]["cachePath"].');
        }
        
        $this->cachePath = Yii::getAlias($this->cachePath);
    }

    public function img($srcImagePath, $size = null, $options = [], $imagick_options = []) 
    {
        return Html::img(self::imgSrc($srcImagePath, $size, $imagick_options), $options);
    }
    
    public function imgSrc($srcImagePath, $size = null, $imagick_options = []) 
    {
        $srcImagePath = Yii::getAlias($srcImagePath);

        if (!is_file($srcImagePath)) {
            return null;
        }

        if (is_file($this->getCachedFile($srcImagePath, $size, 'path', $imagick_options))) {
            return $this->getCachedFile($srcImagePath, $size, 'url', $imagick_options);
        } else {
            return null;
        }
    }
    
    private function getCachedFile($srcImagePath, $size, $type = 'url', $imagick_options) 
    {
        $file = pathinfo($srcImagePath);
        if (!$file['basename']) {
            return fasle;
        }

        if (isset($imagick_options['bw']) && $imagick_options['bw'] === true) {
            $file['basename'] = 'grey_'.$file['basename'];
        }
        if (isset($imagick_options['fit']) && !empty($imagick_options['fit'])) {
            $file['basename'] = 'fit'.$imagick_options['fit'].'_'.$file['basename'];
        }
        if (isset($imagick_options['watermark']) && file_exists($imagick_options['watermark'])) {
            $file['basename'] = 'wm_'.$file['basename'];
        }

        $file = $this->getDir($srcImagePath, $size) . DIRECTORY_SEPARATOR . $file['basename'];
        $cacheFilePath = $this->cachePath . DIRECTORY_SEPARATOR . $file;
        if (!is_file($cacheFilePath))
            $this->createCachedFile ($srcImagePath, $cacheFilePath, $size, $imagick_options);
            
        if ($type == 'path') {
            return $cacheFilePath;
        } elseif ($type == 'url') {
            return $this->cacheUrl . DIRECTORY_SEPARATOR . $file;
        } else {
            return null;
        }
    }
    
    private function getDir($srcImagePath, $size = null) 
    {
        $md5FileName = md5($srcImagePath);
        $dir = substr($md5FileName, 0, 2) . DIRECTORY_SEPARATOR . substr($md5FileName, 2, 2) . DIRECTORY_SEPARATOR . substr($md5FileName, 4, 2);
        if ($size) {
            $dir = $size . DIRECTORY_SEPARATOR . $dir;
        }
        return $dir;
    }

    private function createCachedFile($srcImagePath, $pathToSave, $size = null, $imagick_options)
    {
        BaseFileHelper::createDirectory(dirname($pathToSave), 0777, true);
        $size = $size ? $this->parseSize($size) : false;

        $image = new \Imagick($srcImagePath);
        $image->setImageCompressionQuality(100);
        if (isset($imagick_options['bw']) && $imagick_options['bw'] === true) {
            $image->modulateImage(100, 0, 100);
        }
        
        if (isset($imagick_options['fit']) && !empty($imagick_options['fit'])) {
            $sq = intval($imagick_options['fit']);
            $bg = new \Imagick();
            $bg->newImage($sq, $sq, new \ImagickPixel('white'));
            //$bg->setImageFormat('jpg');
            if ($image->getImageWidth() > $image->getImageHeight()) {
                $image->thumbnailImage($sq, 0);
                $nh = $image->getImageHeight();
                $image->extentImage($sq, $sq, 0, ($nh - $sq) / 2);
            } else {
                $image->thumbnailImage(0, $sq);
                $nw = $image->getImageWidth();
                $image->extentImage($sq, $sq, ($nw - $sq) / 2, 0);
            }
        } else if ($size) {
            if ($size['height'] && $size['width']) {
                $image->cropThumbnailImage($size['width'], $size['height']);
            } elseif ($size['height']) {
                $image->thumbnailImage(0, $size['height']);
            } elseif ($size['width']) {
                $image->thumbnailImage($size['width'], 0);
            } else {
                throw new \Exception('Error at $this->parseSize($sizeString)');
            }
        }

        if (isset($imagick_options['watermark']) && is_file($imagick_options['watermark'])) {

            $watermark = new \Imagick();
            $watermark->readImage($imagick_options['watermark']);
            $watermark->evaluateImage(\Imagick::EVALUATE_DIVIDE, 0, \Imagick::CHANNEL_ALPHA);

            $iWidth = $image->getImageWidth();
            $iHeight = $image->getImageHeight();
            $wWidth = $watermark->getImageWidth();
            $wHeight = $watermark->getImageHeight();

            if ($wWidth >= $wHeight) {
                $watermark->thumbnailImage($iWidth / 2, 0);
            } else {
                $watermark->thumbnailImage(0, $iHeight / 2);
            }   
            $wWidth = $watermark->getImageWidth();
            $wHeight = $watermark->getImageHeight();

            $x = ($iWidth - $wWidth) / 2;
            $y = ($iHeight - $wHeight) / 2;
            
            $image->compositeImage($watermark, \imagick::COMPOSITE_OVER, $x, $y);
        }

        $image->writeImage($pathToSave);

        if (!is_file($pathToSave)) {
            throw new \Exception('Error while creating cached file');
        }
        return $image;
    }

    private function parseSize($sizeString)
    {
        if (!$sizeString) {
            $sizeString = $this->defaultSize;
        }
        $sizeArray = explode('x', $sizeString);
        $part1 = (isset($sizeArray[0]) and $sizeArray[0] != '');
        $part2 = (isset($sizeArray[1]) and $sizeArray[1] != '');
        if ($part1 && $part2) {
            if (intval($sizeArray[0]) > 0 && intval($sizeArray[1]) > 0) {
                $size = [
                    'width' => intval($sizeArray[0]),
                    'height' => intval($sizeArray[1])
                ];
            } else {
                $size = null;
            }
        } elseif ($part1 && !$part2) {
            $size = [
                'width' => intval($sizeArray[0]),
                'height' => null
            ];
        } elseif (!$part1 && $part2) {
            $size = [
                'width' => null,
                'height' => intval($sizeArray[1])
            ];
        } else {
            throw new \Exception('Error parsing size.');
        }
        return $size;
    }
}