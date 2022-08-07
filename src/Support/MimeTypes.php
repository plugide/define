<?php

namespace Plugide\Define\Support;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array getMimeTypes($extension)
 * @method static array getExtensions($mime_type)
 *
 * @see \Symfony\Component\Mime\MimeTypes
 */
class MimeTypes extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return \Symfony\Component\Mime\MimeTypes::class;
    }

    /**
     * Get the first MIME type that matches the given file extension.
     *
     * @param string $extension The file extension to check.
     *
     * @return string|null
     */
    public static function getMimeType($extension)
    {
        return count($mimes = self::getMimeTypes($extension)) ? $mimes[0] : null;
    }

    /**
     * Get the first file extension (without the dot) that matches the given MIME type.
     *
     * @param string $mime_type The MIME type to check.
     *
     * @return string|null
     */
    public static function getExtension($mime_type)
    {
        return count($extensions = self::getExtensions($mime_type)) ? $extensions[0] : null;
    }
}
