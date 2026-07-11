<?php

class FileManager
{
    private $fileTable = 'file_manage';
    protected $uploadDir;
    private $conn;

    public function __construct($db, $uploadDir = '')
    {
        $this->uploadDir = $uploadDir;
        $this->conn = $db;

        // Make sure the upload directory exists
        if (!is_dir($this->uploadDir) and $uploadDir != '') {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function uploadFile($file)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            // Handle upload errors if needed
            return false;
        }

        $originalName = $file['name'];
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);

        // Generate a unique name for the file
        $uniqueName = $this->generateUniqueFileName($extension);

        // Move the file to the desired location with the new name
        $destination = $this->uploadDir . '/' . $uniqueName;
        move_uploaded_file($file['tmp_name'], $destination);

        return $uniqueName;
    }

    protected function generateUniqueFileName($extension)
    {
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        return $filename;
    }


    function getFileInfoFromPath($filePath, $baseDownloadUrl = '', $fileTitle = '', $CompanyId = '',$local = 0,$userId = NULL,$creation_date = null)
    {
        $fileInfo = pathinfo($filePath);

        $fileSize = @filesize($filePath);


        $fileExtension = $fileInfo['extension'] ?? null;
        $fileName = $fileInfo['filename'];

        $downloadLink = $baseDownloadUrl . rawurlencode($fileName) . '.' . $fileExtension;

        $formattedSize = $this->formatFileSize($fileSize);
        
        $user = 0;
        if($userId != NULL)
            $user = 1;



        return [
            'fileName' => $fileName,
            'CompanyId' => $CompanyId,
            'fileTitle' => $fileTitle,
            'fileSize' => $formattedSize,
            'fileType' => $fileExtension,
            'downloadLink' => $downloadLink,
            'local' => $local,
            'user' => $user,
            'creation_date' =>$creation_date
        ];
    }

    private function formatFileSize($bytes)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    public function fileDownload($realFilePath)
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($realFilePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($realFilePath));
        ob_clean();
        flush();
        // خواندن و ارسال محتوای فایل
        readfile($realFilePath);
        exit;
    }
    public function getFileManageByPart($part_id, $part_name, $user_id = '', $admin_id = '')
    {
        $sqlQuery = "SELECT * FROM " . $this->fileTable . " WHERE part_id = ? AND part_name = ?";
        $params = array("is", $part_id, $part_name);
        
        // Check if local field is 1, then restrict to current company_id
        if ($_SESSION['company_id']) {
            $sqlQuery .= " AND (local = 0 OR (local = 1 AND company_id = ?))";
            $params[0] .= "i";
            $params[] = $_SESSION['company_id'];
        }
    
        if ($user_id != '') {
            $sqlQuery .= " AND user_id = ?";
            $params[0] .= "i";
            $params[] = $user_id;
        }
    
        if ($admin_id != '') {
            $sqlQuery .= " AND admin_id = ?";
            $params[0] .= "i";
            $params[] = $admin_id;
        }
    
        $sqlQuery .= " ORDER BY id DESC";
    
        $stmt = $this->conn->prepare($sqlQuery);
    
        // Bind parameters dynamically
        $bind_params = array();
        foreach ($params as $key => $value) {
            $bind_params[$key] = &$params[$key];
        }
        call_user_func_array(array($stmt, 'bind_param'), $bind_params);
    
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            return $result;
        } else {
            return null;
        }
    }
    

}




?>