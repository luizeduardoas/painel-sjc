<?php

// Imaging
class imaging {

    // Variables
    private $img_input;
    private $img_output;
    private $img_src;
    private $format;
    private $quality = 80;
    private $x_input;
    private $y_input;
    private $x_output;
    private $y_output;
    private $resize;

    // Set image
    public function set_img($img) {
        ini_set("memory_limit", "1024M");
        // Find format
        $ext = strtoupper(pathinfo($img, PATHINFO_EXTENSION));

        // JPEG image
        if (is_file($img) && ($ext == "JPG" OR $ext == "JPEG")) {
            $this->format = $ext;
            $this->img_input = ImageCreateFromJPEG($img);
            $this->img_src = $img;
        }

        // PNG image
        elseif (is_file($img) && $ext == "PNG") {
            $this->format = $ext;
            $this->img_input = ImageCreateFromPNG($img);
            $this->img_src = $img;
        }

        // GIF image
        elseif (is_file($img) && $ext == "GIF") {
            $this->format = $ext;
            $this->img_input = ImageCreateFromGIF($img);
            $this->img_src = $img;
        }
        // Get dimensions
        $this->x_input = imagesx($this->img_input);
        $this->y_input = imagesy($this->img_input);
    }

    public function set_resize($bool) {
        $this->resize = $bool;
        $this->x_output = imagesx($this->img_input);
        $this->y_output = imagesy($this->img_input);
    }

    // Set maximum image size (pixels)
    public function set_size($x_size = 100, $y_size = 100, $wide = true) {
        // Resize
//        if ($this->x_input > $size && $this->y_input > $size) {

        if ($wide) {
            // Wide
            if ($this->x_input >= $this->y_input) {
                $this->x_output = $x_size;
                $this->y_output = ($this->x_output / $this->x_input) * $this->y_input;
            }

            // Tall
            else {
                $this->y_output = $y_size;
                $this->x_output = ($this->y_output / $this->y_input) * $this->x_input;
            }
        } else {
            $this->x_output = $x_size;
            $this->y_output = $y_size;
        }
        // Ready
        $this->resize = TRUE;
//        }
//        // Don't resize
//        else {
//            $this->resize = FALSE;
//        }
    }

    // Set image quality (JPEG only)
    public function set_quality($quality) {
        if (is_int($quality)) {
            $this->quality = $quality;
        }
    }

    // Save image
    public function save_img($path, $x, $y, $w, $h) {
        ini_set("memory_limit", "1024M");

        $this->x_input = $w;
        $this->y_input = $h;

        // Resize
        if ($this->resize) {
            $this->img_output = ImageCreateTrueColor($this->x_output, $this->y_output);
            if ($this->format == "PNG") {
                imagealphablending($this->img_output, false);
                imagesavealpha($this->img_output, true);
                $source = imagecreatefrompng($this->img_src);
                imagealphablending($source, true);
                //ImageCopyResampled($this->img_output, $source, 0, 0, $x, $y, $this->x_output, $this->y_output, $this->x_input, $this->y_input);
                ImageCopyResampled($this->img_output, $this->img_input, 0, 0, $x, $y, $this->x_output, $this->y_output, $this->x_input, $this->y_input);
            } else
                ImageCopyResampled($this->img_output, $this->img_input, 0, 0, $x, $y, $this->x_output, $this->y_output, $this->x_input, $this->y_input);
        }

        // Save JPEG
        if ($this->format == "JPG" OR $this->format == "JPEG") {
            if ($this->resize) {
                imageJPEG($this->img_output, $path, $this->quality);
            } else {
                copy($this->img_src, $path);
            }
        }

        // Save PNG
        elseif ($this->format == "PNG") {
            if ($this->resize) {
                imagePNG($this->img_output, $path);
            } else {
                copy($this->img_src, $path);
            }
        }

        // Save GIF
        elseif ($this->format == "GIF") {
            if ($this->resize) {
                imageGIF($this->img_output, $path);
            } else {
                copy($this->img_src, $path);
            }
        }
    }

    public function rotate_img($degrees) {
        // Save JPEG
        if ($this->format == "JPG" OR $this->format == "JPEG") {
            $source = imagecreatefromjpeg($this->img_src);
            $rotate = imagerotate($source, $degrees, 0);
            imagejpeg($rotate, $this->img_src, $this->quality);
        }
        // Save PNG
        elseif ($this->format == "PNG") {
            $source = imagecreatefrompng($this->img_src);
//            imagesavealpha($source, true);            
            $rotate = imagerotate($source, $degrees, 0);
            imagepng($rotate, $this->img_src);
        }

        // Save GIF
        elseif ($this->format == "GIF") {
            $source = imagecreatefromgif($this->img_src);
            $rotate = imagerotate($source, $degrees, 0);
            imagegif($rotate, $this->img_src);
        }
    }

    // Get width
    public function get_width() {
        return $this->x_input;
    }

    // Get height
    public function get_height() {
        return $this->y_input;
    }

    // Clear image cache
    public function clear_cache() {
        @ImageDestroy($this->img_input);
        @ImageDestroy($this->img_output);
    }

}

?>