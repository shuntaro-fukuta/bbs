<?php

class ImageUploader
{
    private $directory_path;
    private $mimetypes = [
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
    ];

    public function __construct($directory_path)
    {
        $this->setDirectoryPath($directory_path);
    }

    private function setDirectoryPath($directory_path)
    {
        if (!file_exists($directory_path)) {
            mkdir($directory_path, 0774, true);
        }

        $this->directory_path = $directory_path;
    }

    private function buildUniquePath($tmp_name)
    {
        $mime_type = mime_content_type($tmp_name);

        $extension = array_search($mime_type, $this->mimetypes);

        $file_name = uniqid(mt_rand(), true) . '.' . $extension;

        $file_path = $this->directory_path . '/' . $file_name;

        return $file_path;
    }

    public function upload(array $file)
    {
        if ($file === [] || !isset($file['tmp_name']) || $file['tmp_name'] === '') {
            return false;
        }

        $tmp_name = $file['tmp_name'];

        $upload_path = $this->buildUniquePath($tmp_name);

        if (!move_uploaded_file($tmp_name, $upload_path)) {
            throw new RuntimeException('Failed to upload file');
        }

        return $upload_path;
    }
}