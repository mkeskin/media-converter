<?php

namespace MediaConverter;

/**
 * Open source media converter for PHP
 * 
 * @package  mkeskin/media-converter
 * @author	 Mustafa Keskin <mustafa@keskin.work>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

class MimeType
{
    /**
     * Mimetypes for only audio and video files.
     * 
     * @var array
     */
    protected static $mimetypes = array(
        'aac' => 'audio/aac',
        'wav' => 'audio/wav',
        'mp3' => 'audio/mpeg',
        'mp4' => 'video/mp4',
        'avi' => 'video/avi',
        'flv' => 'video/x-flv',
        'mov' => 'video/quicktime',
        'qt' => 'video/quicktime',
    );

    /**
     * Get the mime type for spesific extension.
     * 
     * @param   string $ext
     * @return  string
     */
    public static function getMimeType($ext) : string
    {   try {
            if (! key_exists($ext, self::$mimetypes))
                throw new \Exception("This file extension ({$ext}) is not supported.");

            return self::$mimetypes[$ext];
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}