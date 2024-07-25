<?php 

//NOT THE BEST CLASS I EVER WRITTEN
//WHILE WRITING IT I DIDNT FULLY KNOW HOW PHP UPLOADS WORK
//THIS NEEDS TO BE RE-FACTORED...

class FileUploader {

    public static $uploadsPath = __DIR__ . "/uploads";

    public static function formUsesMultipart(){
        if($_SERVER['REQUEST_METHOD'] !== 'POST')
            return false;

        if(!isset($_SERVER['CONTENT_TYPE']) )
            return false;

        return str_starts_with($_SERVER['CONTENT_TYPE'], "multipart/form-data; boundary=");
    }
    
    public static function existsInUploads($filename){
        return file_exists(static::$uploadsPath ."/$filename");
    }
    
    public static function noFileAtAll(){
        return empty($_FILES);
    }
    
    public static function missingFile($file='file'){
        return empty($_FILES[$file]);
    }
    
    public static function getMimeType($file='file') {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $_FILES[$file]['tmp_name']);
        return $mime_type;
    }
    
    public static function getFilename($file='file'){
        $pathinfo = pathinfo($_FILES[$file]["name"]);
        return $pathinfo['filename'];
    }
    
    public static function getFileExtension($file='file'){
        $pathinfo = pathinfo($_FILES[$file]["name"]);
        return $pathinfo['extension'];
    }

    public static function getBasename($file='file'){
        $pathinfo = pathinfo($_FILES[$file]["name"]);
        return $pathinfo['basename'];
    }

    public static function createNewFilename($file='file'){

        $pathinfo = pathinfo($_FILES[$file]["name"]);
    
        $base = $pathinfo['filename'];
        $extension = $pathinfo['extension'];
    
        $base = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base);
        $base = mb_substr($base, 0, 200);
    
        $filename = $base . "." . $extension;
    
        $destination = static::$uploadsPath . "/$filename";
        $i = 1;
    
        while (file_exists($destination)) {
                $filename = $base . "-$i." . $extension;
                $destination = static::$uploadsPath . "/$filename";;
                $i++;
            }
        return $filename;
    }

    public static function moveFile($fname, $file='file'){
        $destination = static::$uploadsPath . "/$fname";
        return move_uploaded_file($_FILES[$file]['tmp_name'], $destination);
    }

    public static function validateSize($size, $file='file'){
        if ($_FILES[$file]['size'] > $size) 
            throw new Exception('File is too large');
        return true;
        }
    
    public static function validateErrors($file='file'){
        if (empty($_FILES)) {
            throw new Exception('Invalid upload');
        }

        switch ($_FILES[$file]['error']) {
            case UPLOAD_ERR_OK:
                break;

            case UPLOAD_ERR_NO_FILE:
                throw new Exception('No file uploaded');
                break;

            case UPLOAD_ERR_INI_SIZE:
                throw new Exception('File is too large (from the server settings)');
                break;

            default:
                throw new Exception('An error occurred');
        }
       return true;
    }
}