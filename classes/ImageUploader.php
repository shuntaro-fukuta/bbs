<?php

class ImageUploader
{
    private $path_to_image_directory;
    private $mimetypes = [
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
    ];

    public function __construct($path_to_image_directory)
    {
        $this->path_to_image_directory = $path_to_image_directory;
    }

    private function getUniqueFilename($tmp_name)
    {
        $mime_type = mime_content_type($tmp_name);

        $extension = array_search($mime_type, $this->mimetypes);

        return uniqid(mt_rand(), true) . '.' . $extension;
    }

    private function buildFilePath($tmp_name)
    {
        $file_name = $this->getUniqueFilename($tmp_name);
        // TODO: ディレクトリを変更できるようにする？
        $file_path = $this->path_to_image_directory . '/' . $file_name;

        return $file_path;
    }

    public function upload(array $file)
    {
        if ($file === [] || !isset($file['tmp_name']) || $file['tmp_name'] === '') {
            return false;
        }

        $tmp_name = $file['tmp_name'];

        $upload_path = $this->buildFilePath($tmp_name);

        if (!move_uploaded_file($tmp_name, $upload_path)) {
            throw new RuntimeException('Failed to upload file');
        }

        return $upload_path;
    }
}