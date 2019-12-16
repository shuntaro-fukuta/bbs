<?php

class Uploader
{
    protected $root_path;
    protected $directory_path = '/uploads';
    protected $mimetypes      = [
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
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

    public function upload(array $file)
    {
        $tmp_name = $file['tmp_name'] ?? null;
        if (empty($tmp_name)) {
            throw new Exception('Invalid file.');
        }

        $upload_path = $this->directory_path . '/' . $this->createUniqueFilename($tmp_name);

        if (!move_uploaded_file($tmp_name, $this->root_path . '/' . $upload_path)) {
            throw new RuntimeException('Failed to upload file.');
        }

        return $upload_path;
    }

    protected function createUniqueFilename(string $tmp_name)
    {
        $mime_type = mime_content_type($tmp_name);

        $extension = array_search($mime_type, $this->mimetypes);

        return uniqid(mt_rand(), true) . '.' . $extension;
    }
}