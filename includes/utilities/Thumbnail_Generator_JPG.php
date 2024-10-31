<?php

declare(strict_types=1);

namespace PWP\includes\utilities;

use PWP\includes\exceptions\Invalid_Input_Exception;
use PWP\includes\exceptions\Not_Found_Exception;
use Throwable;

class Thumbnail_Generator_JPG extends Abstract_Thumbnail_Generator
{
    protected string $suffix = '.jpg';

    public function __construct(int $quality = -1)
    {
        parent::__construct($quality);
    }
    public function generate(string $src, string $dest, string $name, int $tgtWidth, int $tgtHeight = null, int $quality = null): string
    {
        try {
            $thumbnail = $this->generate_thumbnail(
                $this->get_image($src),
                $tgtWidth,
                $tgtHeight
            );

            imagesavealpha($thumbnail, false);
            $path = $dest . '/' . $name . $this->suffix;
            if (!imagejpeg($thumbnail, $path, !is_null($quality) ? $quality : $this->quality)) {
                throw new Invalid_Input_Exception('Image could not be made for unknown reasons');
            }

            return $path;
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
