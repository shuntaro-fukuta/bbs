<?php

require_once(__DIR__ . '/../functions/general.php');

class Uploader
{
    protected $root_path;
    protected $directory_path = '/uploads';
    protected $mimetypes      = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        // ... その他MIMEタイプ
    ];

    public function __construct()
    {
        $this->root_path = $_SERVER['DOCUMENT_ROOT'];
    }

    public function setDirectoryPath(string $directory_path)
    {
        if (empty($directory_path)) {
            throw new InvalidArgumentException('Invalid path.');
        }

        if (substr($directory_path, 0, 1) !== '/') {
            throw new InvalidArgumentException("Path format must be '/dir1/dir2'.");
        }

        if (!file_exists($this->root_path . $directory_path)) {
            mkdir($this->root_path . $directory_path, 0777, true);
        }

        $this->directory_path = $directory_path;
    }

    public function upload(array $file, string $file_name = null)
    {
        $tmp_name = $file['tmp_name'] ?? null;
        if (empty($tmp_name)) {
            throw new RuntimeException('Invalid file passed.');
        }

        $extension = $this->getExtension($tmp_name);
        if ($extension === false) {
            throw new RuntimeException('Failed to get extension.');
        }

        if (empty($file_name)) {
            $file_name = create_random_string(20);
        }

        $upload_path = $this->directory_path . '/' . $file_name . '.' . $extension;

        if (!move_uploaded_file($tmp_name, $this->root_path . '/' . $upload_path)) {
            throw new RuntimeException('Failed to upload file.');
        }

        return $upload_path;
    }

    protected function getExtension(string $file_path)
    {
        if (!($mime_type = mime_content_type($file_path))) {
            return false;
        }

        if (!($extension = array_search($mime_type, $this->mimetypes))) {
            return false;
        }

        return $extension;
    }

    public function delete(string $file_path)
    {
        if (substr($file_path, 0, 1) !== '/') {
            throw new InvalidArgumentException("Path format must be '/dir1/dir2'.");
        }

        $delete_path = $this->root_path . $file_path;
        if (!file_exists($delete_path)) {
            throw new Exception("{$delete_path} doesn't exist.");
        }

        unlink($this->root_path . $file_path);
    }
}