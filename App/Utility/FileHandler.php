<?php

namespace App\Utility;


class FileHandler
{


    protected function returnResult($status = 0, $response = '', $returnedValue = null)
    {
        $result = (object) [];
        $result->status = $status;
        $result->response = $response;
        $result->returnedValue = $returnedValue;
        return $result;
    }

    public function checkFileCounts($name, $min, $max = null, $allowEmpty = false)
    {
        $fileCounts = sizeof((array) $_FILES[$name]['name']);
        $max = $max === null ? 99999 : $max;
        return $fileCounts >= $min && $fileCounts <= $max ? $this->returnResult(1, 1) : $this->returnResult(0, 22);
    }


    public function validateFilesMimeTypes($filesAndMimeTypes)
    {
        $validationResult = true;
        foreach ($filesAndMimeTypes as $fileName => $mimeTypes) {
            foreach ((array) $_FILES[$fileName]['tmp_name'] as $temp_name) {
                $validationResult = $validationResult && in_array(mime_content_type($temp_name), $mimeTypes);
            }
        }
        return $validationResult ? $this->returnResult(1, 1) : $this->returnResult(0, 44);
    }

    public function checkFileSizes($filesAndSizes)
    {
        $validationResult = true;
        foreach ($filesAndSizes as $fileName => $filesSize) {
            foreach ((array) $_FILES[$fileName]['size'] as $fs) {
                $validationResult = $validationResult && ($fs !== 0 && $fs <= $filesSize);
            }
        }
        return $validationResult ? $this->returnResult(1, 1) : $this->returnResult(0, 33);
    }

    public function checkFiles($name, $config)
    {
        /*
            array $config 
                    props : maxFiles MaxSize mimeTypes 
        */
        // NOTICE : in case the FILE INPUT NAME has [] , there is no need to cast $_FILES properties to array
        if (isset($_FILES[$name])) {
            $files = $_FILES[$name];
            $names = (array) $files['name'];
            if (sizeof($names) > $config['maxFiles'])
                return $this->returnResult(0, 22); // code 22 , files counts exceeded
            $sizes = (array) $files['size'];
            $errors = (array) $files['error'];
            $temp_names = (array) $files['tmp_name'];
            // name , type
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            foreach ($temp_names as $index => $tmp_name) {
                switch (false) {
                    case ($sizes[$index] <= $config['maxSize']):
                        return $this->returnResult(0, 33); // code 33 , invalid file size
                    case (in_array($finfo->file($tmp_name), $config['mimeTypes'])):
                        return $this->returnResult(0, 44); // code 44 , invalid mime type
                    case ($errors[$index] !== UPLOAD_ERR_NO_FILE && $sizes[$index] !== 0):
                        return $this->returnResult(0, 55); // code 55 , no file sent
                }
            }
            return $this->returnResult(1, 1); // code 1 , all fine
        } else {
            return $this->returnResult(0, 11); // code 11 , file not set
        }
    }

    public function getFileExtention($fileName)
    {
        $ext = explode('.', $fileName);
        return '.' . end($ext);
    }

    public function saveFile($currentLocation, $saveAs, $target = FILES_PATH)
    {
        return move_uploaded_file($currentLocation,  BASE_PATH . '/' . $target . '/' . $saveAs);
    }


    public function getFilesInfo($name)
    {
        $fileArr = $_FILES[$name];
        if (isset($fileArr) && !empty($fileArr)) {
            $infoArray = [];
            $finfo = new \finfo();
            foreach ((array)$fileArr['name'] as $index => $fileName) {
                $nameArr = explode($fileName, '.');
                $temp = ((array)$fileArr['tmp_name'])[$index];
                $mimeType = $finfo->file($temp, FILEINFO_MIME_TYPE);
                [$fileType, $fileExt] =  explode('/', $mimeType);
                $infoArray[$index] = [
                    'name' => end($nameArr),
                    'temp' => $temp,
                    'type' => $fileType,
                    'ext' => $fileExt
                ];
            }
            return $infoArray;
        } else
            return false;
    }
}
