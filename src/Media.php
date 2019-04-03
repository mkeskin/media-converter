<?php

namespace MediaConverter;

use MediaConverter\MimeType;

/**
 * Open source media converter for PHP
 * 
 * @package  mkeskin/media-converter
 * @author	 Mustafa Keskin <mustafa@keskin.work>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

class Media
{
    /**
     * @var array
     */
    public $media;

    /**
     * Instantiate a new instance.
     */
    public function __construct($file)
    {
        $this->media = (object) array(
            'file' => $file,
            'name' => $this->getName($file),
            'extension' => $this->getExtension($file),
            'size' => $this->getSize($file),
            'duration' => $this->getDuration($file)
            //'encoder' => $this->getEncoder($file)
        );
    }

    /**
     * Get the file extension.
     * 
     * @param   string $filename
     * @return  string
     */
    public function getExtension(string $filename) : string
    {
        $ext = strtolower(array_pop(explode('.', $filename)));

        return $ext;
    }

    /**
     * Get the file name.
     * 
     * @param   string $filename
     * @return  string
     */
    public function getName(string $filename) : string
    {
        $ext = $this->getExtension($filename);

        $name = basename($filename, '.'.$ext);

        return $name;
    }

    /**
     * Get the file size.
     * 
     * @param   string $filename
     * @return  int
     */
    public function getSize(string $filename) : int
    {
        $size = filesize($filename);

        return $size;
    }

    /**
     * Get the encoder via using file type.
     * 
     * @param   string $filetype
     * @return  string
     */
    public function getEncoder(string $filename) : string
    {
        $ext = $this->getExtension($filename);

        $mimetype = MimeType::getMimeType($ext);

        $type = array_shift(explode('/', $mimetype));

        $encoder = $type == 'audio' ? '-acodec audiocodec' : '-vcodec videocodec';

        return $encoder;
    }

    /**
     * Get the media duration with ffmpeg.
     *
     * @param   string $filename 
     * @return  string
     */
    public function getDuration(string $filename) : string
    {
        $command = "ffmpeg -i {$filename} 2>&1 | grep Duration | awk '{print $2}' | tr -d ,";
        exec($command, $result, $status);

        if ($status || !count($result))
            $result = array('00:00:00.00');

        return $result[0];
    }

    /**
     * Get the media duration without ffmpeg.
     *
     * @param   string $filename 
     * @return  string
     */
    public function getDurationWithoutFFMpeg(string $filename) : string
    {
        $handle = fopen($filename, 'r');
        $contents = fread($handle, $this->getSize($filename));
        fclose($handle);

        $make_hexa = hexdec(bin2hex(substr($contents, strlen($contents)-3)));

        if (strlen($contents) > $make_hexa)
            return '00:00:00';
        
        $pre_duration = hexdec(bin2hex(substr($contents, strlen($contents)-$make_hexa, 3)));
        $post_duration = $pre_duration/1000;
    
        $hours = $post_duration/3600;
        $hours = explode('.', $hours);

        $minutes =($post_duration % 3600)/60;
        $minutes = explode('.', $minutes);

        $seconds = ($post_duration % 3600) % 60;        
        $seconds = explode('.', $seconds);

        $duration = $hours[0].':'.$minutes[0].':'.$seconds[0];

        return $duration;
    }
}