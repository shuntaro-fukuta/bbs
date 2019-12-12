<?php

class ImageUploader
{
    private $mimetypes = [
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
    ];

    private function buildUniquePath($image_path)
    {
        $mime_type = mime_content_type($image_path);

        $extension = array_search($mime_type, $this->mimetypes);

        // TODO: ディレクトリを変更できるようにする？
        $unique_path = './uploads/' . uniqid(mt_rand(), true) . ".{$extension}";

        return $unique_path;
    }

    public function upload($image_path)
    {
        $upload_path = $this->buildUniquePath($image_path);

        if (!move_uploaded_file($image_path, $upload_path)) {
            throw new RuntimeException('Failed to upload image');
        }

        return $upload_path;
    }
}