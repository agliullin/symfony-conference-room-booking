<?php

namespace App\Service\Generator\Image\User;

use App\Service\Generator\Image\Image;

/**
 * User avatar class
 */
class Avatar extends Image {
    /**
     * Values for normalize background color
     *
     * @var array
     */
    const BACKGROUND = ['r' => 100, 'g' => 100, 'b' => 150];

    /**
     * Values for normalize content color
     *
     * @var array
     */
    const CONTENT  = ['r' => 250, 'g' => 250, 'b' => 250];

    /**
     * Image matrix size
     *
     * @var int
     */
    const MATRIX_SIZE = 9;

    /**
     * Code for unique content in image
     *
     * @var int
     */
    protected int $code;

    /**
     * Image size
     *
     * @var int
     */
    protected int $size;


    /**
     * Canvas for drawing
     *
     * @var mixed
     */
    protected $canvas;

    /**
     * Construct
     *
     * @param int $size
     * @param int $code
     */
    public function __construct(int $size, int $code)
    {
        $this->size = $size;
        $this->code = $code;
        $this->canvas = imagecreatetruecolor($size, $size);
    }

    /**
     * Get avatar image
     *
     * @return Image
     */
    public function image(): Image
    {
        return $this;
    }

    /**
     * Draw picture
     *
     * @return string
     */
    protected function draw(): string
    {
        $this->createBackground();
        $this->createContent();

        ob_start();
        imagejpeg($this->canvas);
        $picture = ob_get_contents();
        ob_end_clean();

        $this->picture = $picture;

        return $this->picture;
    }

    /**
     * Create background on image
     *
     * @return void
     */
    private function createBackground()
    {
        $backgroundColor = $this->getRandomColor($this->canvas, self::BACKGROUND);
        imagefill($this->canvas, 0, 0, $backgroundColor);
    }

    /**
     * Create content on image
     *
     * @return void
     */
    private function createContent()
    {
        $contentColor = $this->getRandomColor($this->canvas, self::CONTENT);
        $imageMatrix = $this->getFilledMatrix();
        $canvasCellSize = $this->size / self::MATRIX_SIZE;

        $coordinates = array(
            'x1' => 0, 'y1' => 0,
            'x2' => $canvasCellSize, 'y2' => $canvasCellSize,
        );

        foreach ($imageMatrix as $matrixRow) {
            foreach ($matrixRow as $matrixCell) {
                if ($matrixCell) {
                    $this->fillRectangle($coordinates, $contentColor);
                }
                $coordinates['x1'] += $canvasCellSize;
                $coordinates['x2'] += $canvasCellSize;
            }
            $coordinates = array(
                'x1' => 0, 'y1' => $coordinates['y1'] += $canvasCellSize,
                'x2' => $canvasCellSize, 'y2' => $coordinates['y2'] += $canvasCellSize,
            );
        }
    }

    /**
     * Get random color
     *
     * @param resource $canvas
     * @param array $mixColor
     * @return int
     */
    protected function getRandomColor($canvas, array $mixColor): int
    {
        $red = (rand(100, 255)  + $mixColor['r']) / 2;
        $green = (rand(100, 255) + $mixColor['g']) / 2;
        $blue = (rand(100, 255) + $mixColor['b']) / 2;

        return imagecolorallocate($canvas, $red, $green, $blue);
    }

    /**
     * Fill rectangle on image
     *
     * @param array $coordinates
     * @param $color
     * @return void
     */
    private function fillRectangle(array $coordinates, $color)
    {
        imagefilledrectangle(
            $this->canvas,
            $coordinates['x1'],
            $coordinates['y1'],
            $coordinates['x2'],
            $coordinates['y2'],
            $color
        );
    }

    /**
     * Get array of unique mirror matrix
     *
     * @return array
     */
    private function getFilledMatrix(): array
    {
        $mirrorMatrix = [];
        // Make a unique mirror matrix
        for ($i = 0; $i < self::MATRIX_SIZE; $i++) {
            for ($j = 0; $j <= floor(self::MATRIX_SIZE / 2); $j++) {
                if ($j == floor(self::MATRIX_SIZE / 2)) {
                    $mirrorMatrix[$i][$j] = !$mirrorMatrix[$i][$j - 1];
                    continue;
                }
                $mirrorMatrix[$i][$j] = (bool) (($this->code * ($j + 1) / ($j + $i + self::MATRIX_SIZE)) % 2);
                $mirrorMatrix[$i][(self::MATRIX_SIZE - 1) - $j] = $mirrorMatrix[$i][$j];
            }
        }

        // Key sorted matrix
        $keySortedMatrix = [];
        for ($i = 0; $i < self::MATRIX_SIZE; $i++) {
            for ($j = 0; $j < self::MATRIX_SIZE; $j++) {
                $keySortedMatrix[$i][$j] = $mirrorMatrix[$i][$j];
            }
        }

        return $keySortedMatrix;
    }

}