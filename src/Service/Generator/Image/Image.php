<?php

namespace App\Service\Generator\Image;

use SplFileObject;

/**
 * Avatar class
 */
abstract class Image
{
    /**
     * Picture
     *
     * @var string
     */
    protected $picture;

    /**
     * Render
     *
     * @return string
     */
    public function render(): string
    {
        return base64_encode($this->getPicture());
    }

    /**
     * Get picture
     *
     * @return string
     */
    private function getPicture(): string
    {
        if ($this->picture) {
            return $this->picture;
        }

        return $this->draw();
    }

    /**
     * Save image
     *
     * @param string $directory
     * @param mixed $filename
     * @return SplFileObject
     */
    public function save(string $directory, $filename = null): SplFileObject
    {
        $path = $directory . $this->createFileName($filename);
        $file = new SplFileObject($path, 'x');
        $file->fwrite(base64_decode($this->render()));

        return $file;
    }

    /**
     * Create filename
     *
     * @param string $filename
     * @return string
     */
    private function createFileName(string $filename): string
    {
        if ($filename) {
            return $filename;
        }

        return date('YmdHis') . uniqid() . '.jpg';
    }

    /**
     * Get current image
     *
     * @return Image
     */
    abstract public function image(): Image;

    /**
     * Draw
     *
     * @return string
     */
    abstract protected function draw(): string;
}