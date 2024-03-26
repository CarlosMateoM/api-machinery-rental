<?php

class Autoloader
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            // Cambia 'App' al namespace base de tu proyecto
            $namespace = 'api\\';
            $baseDir = __DIR__ . '/'; // Cambia esto si tu estructura de directorios es diferente

            $class = str_replace($namespace, '', $class);
            $file = $baseDir . str_replace('\\', '/', $class) . '.php';

            if (file_exists($file)) {
                require $file;
            }
        });
    }
}

Autoloader::register();