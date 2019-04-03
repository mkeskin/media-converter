<?php

namespace MediaConverter;

use MediaConverter\Media;

/**
 * Open source media converter for PHP
 * 
 * @package  mkeskin/media-converter
 * @author	 Mustafa Keskin <mustafa@keskin.work>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

class Converter
{
    /**
     * @var string
     */
    protected $dir;

    /**
     * @var string
     */
    protected $input;

    /**
     * @var string
     */
    protected $output;

    /**
     * Set the output directory for created file.
     * 
     * @param   string $dir : Directory for output file
     * @return  void
     */
    public function setDirectory(string $dir) : void
    {
        try {
            if (! is_writeable($dir))
                throw new \Exception('Output folder is not writable. Permissions may have to be adjusted.');

            $this->dir = $dir;
        }
        catch(\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Main function in this class.
     * 
     * @param   string $inputFile
     * @param   string $outputFile
     * @return  void
     */
    public function convert(string $inputFile, string $outputFile) : void
    {
        $this->input  = $this->getMedia($inputFile);
        $this->output = $this->getMedia($this->dir . DIRECTORY_SEPARATOR . $outputFile);

        $this->execute();
    }

    /**
     * Execute command via shell and return the result as a boolean.
     * 
     * @param   boolean $showAll
     * @return  boolean
     */
    protected function execute(bool $showAll = false) : bool
    {
        try {
            $output = $this->dir . DIRECTORY_SEPARATOR . $this->input->media->name . '.txt';

            if (file_exists($output))
                return true;

            $command = "ffmpeg -i {$this->input->media->file} {$this->output->media->file} 1> {$output} 2>&1";

            if ($showAll)
                $result = shell_exec($command);
            else {
                exec($command, $result, $status);

                if ($status)
                    throw new \Exception($result[0]);
            }

            $this->output->media->size = $this->output->getSize($this->output->media->file);
            $this->output->media->duration = $this->output->getDuration($this->output->media->file);

            return true;
        }
        catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Get the media object from file directory.
     * 
     * @param   string $filename
     * @return  array $media->object
     */
    public function getMedia(string $filename) : Media
    {
        $media = new Media($filename);

        return $media;
    }
}