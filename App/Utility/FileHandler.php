<?php

namespace App\Utility;

use App\Types\FilesValidationResult;

class FileHandler
{
    protected function returnResult($status = 0, $response = '', $returnedValue = null)
    {
        $result = new FilesValidationResult();
        $result->status = $status;
        $result->response = $response;
        $result->returnedValue = $returnedValue;
        return $result;
    }

    /**
     * 22 : files count exceeded
     */
    public function checkFileCounts($name, $min = 0, $max = null): FilesValidationResult
    {
        $fileCounts = sizeof((array) $_FILES[$name]['name']);
        $max = $max === null ? (int) ini_get("max_file_uploads") : $max;
        return $fileCounts >= $min && $fileCounts <= $max ? $this->returnResult(1, 1) : $this->returnResult(0, 22);
    }

    /**
     * @param array $filesAndMimeTypes - assoc array which keys are fileName and their values are the specified mimeTypes . 
     * values must be array and in case of multiple allowed mimeTypes , they have more than 1 element
     * @return object returns an object , fails with code 44 , or successes with code 1                           
     */
    public function validateFilesMimeTypes($filesAndMimeTypes): FilesValidationResult
    {
        foreach ($filesAndMimeTypes as $fileName => $mimeTypes)
            foreach ((array) $_FILES[$fileName]['tmp_name'] as $temp_name)
                if (!in_array(mime_content_type($temp_name), $mimeTypes))
                    return $this->returnResult(0, 44);
        return $this->returnResult(1, 1);
    }

    /**
     * @param array $filesAndSizes - an assoc array including , fileNames as index and sizes as value 
     * @return object returns an object , fails with code 33 , or successes with code 1     
     */
    public function checkFileSizes($filesAndSizes): FilesValidationResult
    {
        foreach ($filesAndSizes as $fileName => $filesSize)
            foreach ((array) $_FILES[$fileName]['size'] as $fs)
                if (!($fs !== 0 && $fs <= $filesSize))
                    return $this->returnResult(0, 33);
        return  $this->returnResult(1, 1);
    }

    /**
     * @param string $name - parameter name for the file 
     * @param array $config - an assoc array containing 4 indexes : maxFiles , maxSize , mimeTypes & allowEmpty
     * * (optional) maxFiles is a number , indicating maximum number of files that can be uploaded  . default to `ini.max_file_uploads`                                  
     * * maxSize is a number , indicating the maximum size of the uploaded files              
     * * mimeTypes is an array , including the allowed mimeTypes for the uploaded files                                  
     * * (optional) allowEmpty is a boolean , indicating wether the given file parameter can be empty (omitted) or not | default to false                                                                                               
     * @return FilesValidationResult $result - an object containing `status` , `response` & `returnedValue`                                
     * status : 0 | 1                                                                    
     * response : codes =>                             
     * * 1 -> all fine
     * * 11 -> file missing , or corrupted
     * * 22 -> files count exceeded
     * * 33 -> invalid file size
     * * 44 -> invalid mime type
     * * 55 -> file has error
     */
    public function checkFiles($name, $config): FilesValidationResult
    {
        if (isset($_FILES[$name])) {
            $files = $_FILES[$name];
            $names = (array) $files['name'];
            if (sizeof($names) === 0 && !($config['allowEmpty'] ?? false))
                return $this->returnResult(0, 11);
            if (sizeof($names) > ($config['maxFiles'] ?? ini_get("max_file_uploads")))
                return $this->returnResult(0, 22);
            $sizes = (array) $files['size'];
            if (in_array(0, $sizes)) // size zero means file has not been successfully uploaded or isn't received
                return $this->returnResult(0, 11);
            $errors = (array) $files['error'];
            $temp_names = (array) $files['tmp_name'];
            // name , type
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            foreach ($temp_names as $index => $tmp_name)
                switch (false) {
                    case ($errors[$index] !== UPLOAD_ERR_NO_FILE):
                        return $this->returnResult(0, 55, $errors[$index]);
                    case (in_array($finfo->file($tmp_name), $config['mimeTypes'])):
                        return $this->returnResult(0, 44);
                    case ($sizes[$index] <= $config['maxSize']):
                        return $this->returnResult(0, 33);
                }
            return $this->returnResult(1, 1);
        } else
            return $this->returnResult(0, 11);
    }

    public function getFileExtension($fileName)
    {
        return '.' . end(explode('.', $fileName));
    }

    public function saveFile($currentLocation, $saveAs, $target = IMAGES_PATH): bool
    {
        return move_uploaded_file($currentLocation,  BASE_PATH  . $target . $saveAs);
    }

    public function deleteFile($fileName, $targetDir = IMAGES_PATH)
    {
        $filePath = BASE_PATH . $targetDir . $fileName;
        if (file_exists($filePath))
            return unlink(BASE_PATH . $targetDir . $fileName);
        return false;
    }

    public function getFilesInfo($name)
    {
        $fileArr = $_FILES[$name];
        if (isset($fileArr) && !empty($fileArr)) {
            $infoArray = [];
            $finfo = new \finfo();
            foreach ((array)$fileArr['name'] as $index => $fileName) {
                $nameArr = explode(".", $fileName);

                $temp = ((array)$fileArr['tmp_name'])[$index];
                $mimeType = $finfo->file($temp, FILEINFO_MIME_TYPE);
                [$fileType, $fileExt] =  explode('/', $mimeType);
                $infoArray[$index] = [
                    'name' => $nameArr[0] ?? '',
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
