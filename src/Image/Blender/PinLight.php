<?php
namespace Manticorp\Image\Blender;

class PinLight extends \Image\Blender
{
    public function _blend($opacity = 1, $fill = 1)
    {
        $opacity = min(max($opacity,0),1);

        if($opacity === 0){
            return $this->base->getImage();
        }

        $destX = ($this->base->getWidth()  - $this->top->getWidth()) / 2;
        $destY = ($this->base->getHeight() - $this->top->getHeight()) / 2;

        $w = $this->top->getWidth();
        $h = $this->top->getHeight();

        $baseImg    = $this->base->getImage();
        $overlayImg = $this->top->getImage();

        for ($x = 0; $x < $w; ++$x) {
            for ($y = 0; $y < $h; ++$y) {

                // First get the colors for the base and top pixels.
                $baseColor = $this->normalisePixel(
                    $this->getColorAtPixel($baseImg, $x + $destX, $y + $destY, $this->base->getIsTrueColor())
                );
                $topColor  = $this->normalisePixel(
                    $this->getColorAtPixel($overlayImg, $x, $y, $this->top->getIsTrueColor())
                );

                $destColor = $baseColor;
                foreach($destColor as $key => &$color){
                    if($color > 0.5)
                        $color = ($topColor[$key] < $color) ? $topColor[$key] : $color;
                    else
                        $color = ($topColor[$key] < $color) ? $topColor[$key] : $color;
                }
                if($opacity !== 1) {
                    $destColor = $this->opacityPixel($baseColor, $destColor, $opacity);
                }

                $destColor = $this->integerPixel($this->deNormalisePixel($destColor));

                // Now that we have a valid color index, set the pixel to that color.
                imagesetpixel(
                    $baseImg,
                    $x + $destX, $y + $destY,
                    $this->getColorIndex($baseImg, $destColor)
                );
            }
        }

        return $baseImg;
    }

    /**
     * @todo implement
     */
    public function _imagickBlend($opacity = 1, $fill = 1)
    {
        $baseImg    = $this->base->getImage();
        $overlayImg = $this->top->getImage();

        // $overlayImg->setImageOpacity($opacity);

        // $baseImg->compositeImage($overlayImg, \Imagick::COMPOSITE_E, 0, 0);

        return $baseImg;
    }
}