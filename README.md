<p align="center">
    <a href="https://webheads.agency/" target="_blank">
        <img src="https://webheads.agency/files/images/LogoWebHeads.png" width="181" alt="WebHeads - Creative Web Agency">
    </a>
</p>

Yii2 extension for generating images based on Imagick

[![Latest Stable Version](https://poser.pugx.org/whagency/test/v/stable)](https://packagist.org/packages/whagency/test)
[![Total Downloads](https://poser.pugx.org/whagency/test/downloads)](https://packagist.org/packages/whagency/test)
[![License](https://poser.pugx.org/whagency/test/license)](https://packagist.org/packages/whagency/test)


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

or add to your `composer.json` file


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
        'class' => 'letyii\imagecache\imageCache',
        'cachePath' => '@app/web/files/cache',
        'cacheUrl' => '@web/files/cache',
    ],
]

~~~

