<?php

class ImageUploader
{
    private $upload_path;
    private $mimetypes = [
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
    ];

    public function __construct($upload_path)
    {
        $this->setUploadPath($upload_path);
    }

    private function setUploadPath($upload_path)
    {
        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0774, true);
        }

        $this->upload_path = $upload_path;
    }

    private function buildUniquePath($tmp_name)
    {
        $mime_type = mime_content_type($tmp_name);

        $extension = array_search($mime_type, $this->mimetypes);

        $file_name = uniqid(mt_rand(), true) . '.' . $extension;

        $file_path = $this->upload_path . '/' . $file_name;

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