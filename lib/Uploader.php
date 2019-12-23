<?php

class Uploader
{
    const UPLOAD_DIR_NAME = 'uploads';

    private $root_path;
    private $directory_path = '/uploads';
    private $mimetypes      = [
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
        if (!$this->isValidPath($directory_path)) {
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

    private function getExtension(string $file_path)
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
        if (!$this->isValidPath($file_path)) {
            throw new InvalidArgumentException("Path format must be '/directory/file'.");
        }

        $delete_path = $this->root_path . $file_path;

        if (file_exists($delete_path)) {
            if (!unlink($this->root_path . $file_path)) {
                throw new RuntimeException("Failed to delete file '{$delete_path}'.");
            }
        }
    }

    private function isValidPath(string $path)
    {
        if (empty($path)) {
            return false;
        }

        if (substr($path, 0, 1) !== '/') {
            return false;
        }

        return true;
    }
}