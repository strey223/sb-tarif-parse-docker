<?php

namespace App\Acme;


final class Singleton
{

    /** @var Singleton $instance */
    private static $instance;

    private $path;

    /**
     * @param null $config
     * @return Singleton
     */
    public static function app($config = null)
    {

        if (static::$instance !== null) {
            return static::$instance;
        }

        $self = new static();

        $self->path = $config['path'] ?? '';

        static::$instance = $self;
        return static::$instance;
    }

    /**
     * @return string
     */
    public function appPath()
    {
        return self::$instance->path['appPath'] ?? null;
    }

    /**
     * @return string
     */
    public function basePath()
    {
        return self::$instance->path['basePath'] ?? null;
    }

    /**
     * is not allowed to call from outside to prevent from creating multiple instances,
     * to use the singleton, you have to obtain the instance from Singleton::getInstance() instead
     */
    private function __construct()
    {
    }

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }

    /**
     * prevent from being unserialized (which would create a second instance of it)
     */
    private function __wakeup()
    {
    }

}