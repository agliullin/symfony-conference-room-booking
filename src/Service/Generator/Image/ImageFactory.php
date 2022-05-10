<?php

namespace App\Service\Generator\Image;

use Exception;

/**
 * Image factory class
 */
class ImageFactory
{
    /**
     * Image
     *
     * @var Image
     */
    private Image $image;

    /**
     * Factory
     *
     * @param Image $image
     * @return Image
     */
    protected function factory(Image $image): Image
    {
        $this->image = $image;

        return $this->image->image();
    }

    /**
     * Generate
     *
     * @param Image $image
     * @return Image
     * @throws Exception
     */
    public function generate(Image $image): Image
    {
        if (!$this->isGDLoaded()) {
            throw new Exception(
                'GD extension not loaded.'
            );
        }

        return $this->factory($image);
    }

    /**
     * Check for the GD extension
     *
     * @return boolean
     */
    public function isGDLoaded(): bool
    {
        return extension_loaded('gd') && function_exists('gd_info');
    }
}