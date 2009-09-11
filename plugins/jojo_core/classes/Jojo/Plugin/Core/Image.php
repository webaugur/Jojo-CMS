<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2008 Michael Cochrane <mikec@jojocms.org>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_core
 */

class Jojo_Plugin_Core_Image extends Jojo_Plugin_Core {

    /**
     * Output the Image file
     *
     */
    function __construct()
    {
        /* Read only session */
        define('_READONLYSESSION', true);

        /* Get requested filename */
        $file = urldecode(Jojo::getFormData('file', 'default.jpg'));
        $timestamp = strtotime('+1 day');

        /* Check file name has correct extension */
        $validExtensions = array('jpg', 'gif', 'jpeg', 'png');
        if (!in_array(Jojo::getFileExtension($file), $validExtensions)) {
            /* Not valid, 404 */
            header("HTTP/1.0 404 Not Found", true, 404);
            exit;
        }

        if (preg_match('/^([0-9]+|default)\/(.+)/', $file, $matches)) {
            /* Max size */
            $_GET['sz'] = $matches[1];
            $filename = $matches[2];
        } elseif (preg_match('/^([0-9]+)x([0-9]+)\/(.+)/', $file, $matches)) {
            /* Max width + max height*/
            $_GET['maxw'] = $matches[1];
            $_GET['maxh'] = $matches[2];
            $filename = $matches[3];
        } elseif (preg_match('/^w([0-9]+)\/(.+)/', $file, $matches)) {
            /* Max width */
            $_GET['maxw'] = $matches[1];
            $filename = $matches[2];
        } elseif (preg_match('/^h([0-9]+)\/(.+)/', $file, $matches)) {
            /* Max height */
            $_GET['maxh'] = $matches[1];
            $filename = $matches[2];
        } elseif (preg_match('/^v([0-9]+)\/(.+)/', $file, $matches)) {
            /* Max volume */
            $_GET['maxv'] = $matches[1];
            $filename = $matches[2];
        } elseif (preg_match('/^s([0-9]+)\/(.+)/', $file, $matches)) {
            /* Square */
            $_GET['sq'] = $matches[1];
            $filename = $matches[2];
        } elseif (preg_match('/^mh([0-9]+)\/(.+)/', $file, $matches)) {
            /* ?? */
            $_GET['sz'] = $matches[1];
            $filename = $matches[2];
        }

        if (isset($filename) && file_exists(_DOWNLOADDIR . '/' . $filename)) {
            /* Uploaded image file */
            $filename = _DOWNLOADDIR . '/' . $filename;
        } elseif (isset($filename) && $res = Jojo::listThemes('images/' . $filename)) {
            /* Found in a theme images folder */
            $filename = $res[0];
        } elseif (isset($filename) && $res = Jojo::listPlugins('images/' . $filename)) {
            /* Found in a plugin images folder */
            $filename = $res[0];
        } elseif ($res = Jojo::listPlugins($file)) {
            /* Found in a plugin somewhere */
            $filename = $res[0];
        } elseif (!isset($filename) || !self::isRemoteFile($filename)) {
            /* File from somewhere */
            $filename = $file;
        }

        /* filetype + mimetype */
        $filetype = Jojo::getFileExtension($filename);
        $mimetype = ($filetype == 'jpg') ? 'image/jpeg': 'image/' . $filetype;

        /* Quality */
        $quality = (isset($_GET['ql'])) ? $_GET['ql'] : Jojo::getOption('jpeg_quality', 85);

        /* size */
        if (isset($_GET['sz'])) {
            $size = $_GET['sz'];
            $s = $size;
        } elseif (isset($_GET['maxw']) && isset($_GET['maxh'])) {
            $maxw = $_GET['maxw'];
            $maxh = $_GET['maxh'];
            $s = $maxw.'x'.$maxh;
        } elseif (isset($_GET['maxw'])) {
            $maxw = $_GET['maxw'];
            $s = 'w'.$maxw;
        } elseif (isset($_GET['maxh'])) {
            $maxh = $_GET['maxh'];
            $s = 'h'.$maxh;
        } elseif (isset($_GET['sq'])) {
            $sq = $_GET['sq'];
            $size = str_replace('s','',$_GET['sq']);
            $s = 's'.$sq;
        } elseif (isset($_GET['maxv'])) {
            $maxv = $_GET['maxv'];
            $s = 'v' . $_GET['maxv'];
        } else {
            $size = 'default';
            $s = '';
        }

        if ($s && self::isRemoteFile($filename)) {
            $cachefile = _CACHEDIR . '/images/remote/' . $s . '/' . md5($filename) . '.' . Jojo::getFileExtension($filename);
        } elseif ($s) {
            $cachefile = _CACHEDIR . '/images/' . $s . '/' . str_replace(_DOWNLOADDIR . '/', '', $filename);
        } elseif (self::isRemoteFile($filename)) {
            $cachefile = _CACHEDIR . '/images/remote/' . md5($filename) . '.' . $filetype;
        } else {
            $cachefile = _CACHEDIR . '/images/' . str_replace(_DOWNLOADDIR . '/', '', $filename);
        }



        Jojo::runHook('jojo_core:imageCheckAccess', array('filename' => $filename));

        /* Check for existance of server-cached copy if user has not pressed CTRL-F5 */
        if (is_file($cachefile) && !Jojo::ctrlF5()) {
            Jojo::runHook('jojo_core:imageCachedFile', array('filename' => $cachefile));

            parent::sendCacheHeaders(filemtime($cachefile));

            /* output image data */
            $data = file_get_contents($cachefile);
            header('Last-Modified: ' . date('D, d M Y H:i:s \G\M\T', filemtime($cachefile)));
            header('Cache-Control: private, max-age=28800');
            header('Expires: ' . date('D, d M Y H:i:s \G\M\T', time() + 28800));
            header('Pragma: ');
            header('Content-type: ' . $mimetype);
            header('Content-Length: ' . strlen($data));
            header('Content-Disposition: inline; filename=' . basename($filename) . ';');
            header('Content-Description: PHP Generated Image (cached)');
            header('Content-Transfer-Encoding: binary');
            echo $data;
            exit();
        }

        /* for default sized images, read image data directly to save reprocessing */
        if (($s == 'default') || ($s == '')) {
            if (self::isRemoteFile($filename) || Jojo::fileExists($filename)) {
                Jojo::runHook('jojo_core:imageDefaultFile', array('filename' => $filename));

                parent::sendCacheHeaders(filemtime($filename));

                /* output image data */
                $data = file_get_contents($filename);

                //header('Cache-Control: private');
                if (!self::isRemoteFile($filename)) {
                    header('Last-Modified: ' . date('D, d M Y H:i:s \G\M\T', filemtime($filename)));
                }
                header('Cache-Control: private, max-age=28800');
                header('Expires: ' . date('D, d M Y H:i:s \G\M\T', time() + 28800));
                header('Pragma: ');
                header('Content-type: ' . $mimetype);
                header('Content-Length: ' . strlen($data));
                header('Content-Disposition: inline; filename=' . basename($filename) . ';');
                header('Content-Description: PHP Generated Image (cached)');
                header('Content-Transfer-Encoding: binary');
                echo $data;

                /* Cache for quicker response next time */
                Jojo::RecursiveMkdir(dirname($cachefile));
                file_put_contents($cachefile, $data);
                Jojo::publicCache($file, $data);
                exit();
            }

            foreach (Jojo::listThemes('images/' . $file) as $pluginfile) {
                Jojo::runHook('jojo_core:imageDefaultFile', array('filename' => $pluginfile));
                parent::sendCacheHeaders(filemtime($pluginfile));

                /* output image data */
                $data = file_get_contents($pluginfile);
                header('Last-Modified: '.date('D, d M Y H:i:s \G\M\T', filemtime($pluginfile)));
                header('Cache-Control: private, max-age=28800');
                header('Expires: ' . date('D, d M Y H:i:s \G\M\T', time() + 28800));
                header('Pragma: ');
                header('Content-type: ' . $mimetype);
                header('Content-Length: ' . strlen($data));
                header('Content-Disposition: inline; filename=' . basename($filename) . ';');
                header('Content-Description: PHP Generated Image (cached)');
                header('Content-Transfer-Encoding: binary');
                echo $data;

                /* Cache for quicker response next time */
                Jojo::RecursiveMkdir(dirname($cachefile));
                file_put_contents($cachefile, $data);
                Jojo::publicCache($file, $data);
                exit();
            }

            foreach (Jojo::listPluginsReverse('images/' . $file) as $pluginfile) {
                Jojo::runHook('jojo_core:imageDefaultFile', array('filename' => $pluginfile));
                parent::sendCacheHeaders(filemtime($pluginfile));

                /* output image data */
                $data = file_get_contents($pluginfile);
                header('Last-Modified: '.date('D, d M Y H:i:s \G\M\T', filemtime($pluginfile)));
                header('Cache-Control: private, max-age=28800');
                header('Expires: ' . date('D, d M Y H:i:s \G\M\T', time() + 28800));
                header('Pragma: ');
                header('Content-type: ' . $mimetype);
                header('Content-Length: ' . strlen($data));
                header('Content-Disposition: inline; filename=' . basename($filename) . ';');
                header('Content-Description: PHP Generated Image (cached)');
                header('Content-Transfer-Encoding: binary');
                echo $data;

                /* Cache for quicker response next time */
                Jojo::RecursiveMkdir(dirname($cachefile));
                file_put_contents($cachefile, $data);
                Jojo::publicCache($file, $data);
                exit();
            }
        }

        if (self::isRemoteFile($filename) || Jojo::fileExists($filename)) {
            /* the file exists - open it & create image handle*/
            if ($filetype == 'gif') {
                $im = imagecreatefromgif($filename);
            } elseif ($filetype == 'png') {
                $im = imagecreatefrompng($filename);
            } else {
                $im = imagecreatefromjpeg($filename);
            }
        } else {
            /* Search for matching files in the themes */
            $im = false;
            foreach (Jojo::listThemes('images/' . $filename) as $pluginfile) {
                $size = 'default';
                if ($filetype == 'gif') {
                    $im = imagecreatefromgif($pluginfile);
                    break;
                } elseif ($filetype == 'png') {
                    $im = imagecreatefrompng($pluginfile);
                    break;
                } else {
                    $im = imagecreatefromjpeg($pluginfile);
                    break;
                }
            }

            if (!$im) {
                /* Search for matching files in the plugins */
                foreach (Jojo::listPlugins('images/' . $filename, true) as $pluginfile) {
                    $size = 'default';
                    if ($filetype == 'gif') {
                        $im = imagecreatefromgif($pluginfile);
                        break;
                    } elseif ($filetype == 'png') {
                        $im = imagecreatefrompng($pluginfile);
                        break;
                    } else {
                        $im = imagecreatefromjpeg($pluginfile);
                        break;
                    }
                }
            }

            if ((!$im) && (preg_match('%.*/themes/([a-z0-9_-]+)\\.jpg$%i', $file, $result))) {
                /* if format is images/500/themes/theme_name.jpg search for theme screenshot */
                if (Jojo::fileexists(_THEMEDIR.'/'.$result[1].'/screenshot.jpg')) {
                    $im = imagecreatefromjpeg(_THEMEDIR.'/'.$result[1].'/screenshot.jpg');
                } elseif (Jojo::fileexists(_BASETHEMEDIR.'/'.$result[1].'/screenshot.jpg')) {
                    $im = imagecreatefromjpeg(_BASETHEMEDIR.'/'.$result[1].'/screenshot.jpg');
                } else {
                    $im = imagecreatefromjpeg(_BASEPLUGINDIR.'/jojo_core/images/cms/no-screenshot.jpg');
                }
            }
        }

        if (!$im) {
            /* Could not open image, 404 */
            header("HTTP/1.0 404 Not Found", true, 404);
            exit;
        }

        $im_width = imageSX($im);
        $im_height = imageSY($im);

        $startx = $starty = 0; //This is used as the start co-ordinates. Normally zero, but for cropped images this will differ

        if (!empty($sq)) {
            /* Cut the img square */
            $new_height = $new_width = $size;
            $shortest = min($im_height, $im_width);
            //find the offset for cropping
            $startx = ($im_width / 2) - ($shortest / 2);
            $starty = ($im_height / 2) - ($shortest / 2);
            //resize
            $im_height = $im_width = min($im_height, $im_width);
        } elseif (isset($maxv) && !empty($maxv)) {
            /* Image of a maximum total area */
            $currentv = $im_width * $im_height;
            $factor = max(sqrt($currentv/$maxv), 1);
            $new_height = $im_height / $factor;
            $new_width = $im_width / $factor;
        } elseif (!empty($maxw) && !empty($maxh)) {
            /* Scale to maximum dimensions, clipping to fit */
            $new_width = $maxw;
            $new_height = $maxh;
            $startx = 0;
            $starty = 0;
            $factor1 = $im_width/$maxw;
            $factor2 = $im_height/$maxh;
            if ($factor1 > $factor2) {
               $startx = ($im_width / 2);
               $im_width = $maxw * $factor2;
               $startx -= ($im_width / 2);
            } else {
                $starty = ($im_height / 2);
                $im_height = $maxh * $factor1;
                $starty -= ($im_height / 2);
            }
        } elseif (!empty($maxh)) {
            /* Resize tp maximum height */
            $factor = $maxh / $im_height;
            $new_height = $maxh;
            $new_width = $im_width * $factor;
        } elseif (!empty($maxw)) {
            /* Resize tp maximum width */
            $factor = $maxw/$im_width;
            $new_width = $maxw;
            $new_height = $im_height * $factor;
        } else {
            if ($size == 'default') {
                $size = max($im_width,$im_height);
            }
            if ($im_width >= $im_height) {
                $factor = $size/$im_width;
                $new_width = $size;
                $new_height = $im_height * $factor;
            } else {
                $factor = $size/$im_height;
                $new_height = $size;
                $new_width = $im_width * $factor;
            }
        }

        if ($new_width != imageSX($im) || $new_height != imageSY($im)) {
            /* Resize */
            $new_im = ImageCreateTrueColor($new_width, $new_height);
            ImageCopyResampled($new_im, $im, 0, 0, $startx, $starty, $new_width, $new_height, $im_width, $im_height);
            $nochange = false;
        } else {
            /* No change */
            $new_im = $im;
            $nochange = true;
        }

        /* create folders in cache */
        Jojo::RecursiveMkdir(dirname($cachefile));

        /* Allow custom watermark code to be inserted depending on the site */
        foreach(Jojo::listPlugins('config/watermark.inc.php') as $wmfile) {
            require_once($wmfile);
        }

        /* output image data */
        header('Content-type: ' . $mimetype);
        header('Content-Disposition: inline; filename=' . basename($filename) . ';');
        header('Content-Description: PHP Generated Image');
        header('Content-Transfer-Encoding: binary');

        header('Cache-Control: private, max-age=28800');
        header('Expires: ' . date('D, d M Y H:i:s \G\M\T', time() + 28800));
        header('Pragma: ');

        // output
        if ($filetype == "gif") {
            Imagegif($new_im);
            Imagegif($new_im, $cachefile);
            Imagegif($new_im, Jojo::publicCache($file));
        } else if ($filetype == "png") {
            imagesavealpha($new_im, true);
            Imagepng($new_im);
            Imagepng($new_im, $cachefile);
            Imagepng($new_im, Jojo::publicCache($file));
        } else {
            Imagejpeg($new_im, $cachefile, $quality);
            Imagejpeg($new_im, Jojo::publicCache($file), $quality);
            Imagejpeg($new_im,'',$quality);
        }

        // cleanup
        if ($new_im && !empty($new_im)) ImageDestroy($new_im);
        if ($im && !$nochange) ImageDestroy($im);

        exit();
    }


    //added by tim
    // TODO needs some more love
    static function isRemoteFile($filename) {
        return (preg_match('|^https?\://|i', $filename));
    }
}