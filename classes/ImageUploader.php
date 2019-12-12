<?php

class ImageUploader
{
    private $mimetypes = [
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
    ];

    private function buildUniquePath($tmp_name)
    {
        $mime_type = mime_content_type($tmp_name);

        $extension = array_search($mime_type, $this->mimetypes);

        // TODO: ディレクトリを変更できるようにする？
        $unique_path = './uploads/' . uniqid(mt_rand(), true) . ".{$extension}";

        return $unique_path;
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