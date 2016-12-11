<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite5a4babc42abd6cffe1af18d65ec9e4c
{
    public static $files = array (
        '4bae593bdb3198e8f81978f2215e9e50' => __DIR__ . '/..' . '/whatsbrand/php-shopify-api-client/shopify-api-client.php',
    );

    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite5a4babc42abd6cffe1af18d65ec9e4c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite5a4babc42abd6cffe1af18d65ec9e4c::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
