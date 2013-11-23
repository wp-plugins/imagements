<?php

function imagements_calculate_size($img, $percent, $constrain, $w, $h)
{
    // get image size of img
    $x = getimagesize($img);
    // image width
    $sw = $x[0];
    // image height
    $sh = $x[1];

    if ($percent > 0)
    {
        // calculate resized height and width if percent is defined
        $percent = $percent * 0.01;
        $w = $sw * $percent;
        $h = $sh * $percent;
    } else
    {
        if (isset($w) and !isset($h))
        {
            // autocompute height if only width is set
            $h = (100 / ($sw / $w)) * .01;
            $h = @round($sh * $h);
        } elseif (isset($h) and !isset($w))
        {
            // autocompute width if only height is set
            $w = (100 / ($sh / $h)) * .01;
            $w = @round($sw * $w);
        } elseif (isset($h) and isset($w) and isset($constrain))
        {
            // get the smaller resulting image dimension if both height
            // and width are set and $constrain is also set
            $hx = (100 / ($sw / $w)) * .01;
            $hx = @round($sh * $hx);

            $wx = (100 / ($sh / $h)) * .01;
            $wx = @round($sw * $wx);

            if ($hx < $h)
            {
                $h = (100 / ($sw / $w)) * .01;
                $h = @round($sh * $h);
            } else
            {
                $w = (100 / ($sh / $h)) * .01;
                $w = @round($sw * $w);
            }
        }
    }
    $result = array(
    'w' => $w,
    'h' => $h,
    'sw' => $sw,
    'sh' => $sh,
    );
    return $result;
}

function imagements_resize_image($img, $percent, $constrain, $w, $h)
{
    $size = imagements_calculate_size($img, $percent, $constrain, $w, $h);
    $w = $size['w'];
    $h = $size['h'];
    $sw = $size['sw'];
    $sh = $size['sh'];
    
    $im = @ImageCreateFromJPEG($img) or // Read JPEG Image
        $im = @ImageCreateFromPNG($img) or // or PNG Image
        $im = @ImageCreateFromGIF($img) or // or GIF Image
        $im = false; // If image is not JPEG, PNG, or GIF

    if (!$im)
    {
        // We get errors from PHP's ImageCreate functions...
        // So let's echo back the contents of the actual image.
        return false;
    } else
    {
        // Create the resized image destination
        $thumb = @ImageCreateTrueColor($w, $h);
        // Copy from image source, resize it, and paste to image destination
        @ImageCopyResampled($thumb, $im, 0, 0, 0, 0, $w, $h, $sw, $sh);
        // Output resized image
        @ImageJPEG($thumb, $img);
        return true;
    }
}

?>
