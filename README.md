<p align="center">
    <a href="https://webheads.agency/" target="_blank">
        <img src="https://webheads.agency/files/images/LogoWebHeads.png" width="181" alt="WebHeads - Creative Web Agency">
    </a>
</p>

# yii2-imagecache

Yii2 extension for generating images based on Imagick

[![Latest Stable Version](https://poser.pugx.org/whagency/yii2-imagecache/v/stable)](https://packagist.org/packages/whagency/yii2-imagecache)
[![Total Downloads](https://poser.pugx.org/whagency/yii2-imagecache/downloads)](https://packagist.org/packages/whagency/yii2-imagecache)
[![License](https://poser.pugx.org/whagency/yii2-imagecache/license)](https://packagist.org/packages/whagency/yii2-imagecache)


Capabilities
------------

- [x] FIT - resize image WITH proportion and based on BOTH sides
- [x] Scale - resize image WITH proportion and based on ONE sides
- [x] Crop - crop image WITH proportion
- [x] generate black-and-white image
- [x] add watermark to the image

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require "whagency/yii2-imagecache" "*"
```

or add to your composer.json file


```json
...
"require": {
    "whagency/yii2-imagecache": "*"
},
```

Config
------

~~~php
'components' => [
    ...
    'imageCache' => [
        'class' => 'webheads\imagecache\imageCache',
        'cachePath' => '@app/web/files/cache',
        'cacheUrl' => '@web/files/cache',
    ],
]
~~~

Usage Example
-------------

~~~php
echo Yii::$app->imageCache->imgSrc('@app/web/files/image.jpg', '', ['fit' => 300, 'bw' => true, 'watermark' => '@app/web/files/watermark-image.png']);
// Result: path to black-and-white image 300 x 300 without cropping, with watermark.

echo Yii::$app->imageCache->img('@app/web/files/image.jpg', '400x', ['class'=>'my-class', 'alt' => 'Image']);
// Result: scaled image with width = 400, alt and class.

echo Yii::$app->imageCache->img('@app/web/files/image.jpg', '100x150', ['alt' => 'Image'], ['bw' => true]);
// Result: resized and cropped black-and-white image 100 x 150 with alt.
~~~
