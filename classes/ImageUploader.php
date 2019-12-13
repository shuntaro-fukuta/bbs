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

    private function setUploadPath($path)
    {
        if (!file_exists($path)) {
            // hamaco: ディレクトリの権限が4(Read)だと中身見れないよ
            //         1(Exec)がないとディレクトリの中身見れないかな
            mkdir($path, 0774, true);
        }

        $this->upload_path = $path;
    }

    private function createUniqueFilename($tmp_name)
    {
        $mime_type = mime_content_type($tmp_name);

        $extension = array_search($mime_type, $this->mimetypes);

        return uniqid(mt_rand(), true) . '.' . $extension;
    }

    private function buildUploadPath($tmp_name)
    {
        $file_name = $this->createUniqueFilename($tmp_name);

        $file_path = $this->upload_path . '/' . $file_name;

        return $file_path;
    }

    public function upload(array $file)
    {
        if ($file === [] || !isset($file['tmp_name']) || $file['tmp_name'] === '') {
            return false;
        }

        $tmp_name = $file['tmp_name'];

        $upload_path = $this->buildUploadPath($tmp_name);

        if (!move_uploaded_file($tmp_name, $upload_path)) {
            throw new RuntimeException('Failed to upload file');
        }

        return $upload_path;
    }
}