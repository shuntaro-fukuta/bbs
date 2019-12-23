<?php

class Uploader
{
    const UPLOAD_DIR_NAME = 'upload';

    private $upload_dir_path = '';
    private $mimetypes       = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        // ... その他MIMEタイプ
    ];

    public function __construct(string $dir_path = null)
    {
        $this->setUploadDirPath($dir_path);
    }

    public function setUploadDirPath(?string $dir_path, $append = true)
    {
        if (empty($dir_path)) {
            $upload_dir_path = PROJECT_ROOT . DIR_SEP . self::UPLOAD_DIR_NAME;
        } else {
            $upload_dir_path = PROJECT_ROOT . DIR_SEP . ltrim($dir_path, '/');
        }

        if (file_exists($upload_dir_path) && is_file($upload_dir_path)) {
            throw new Exception(__METHOD__ . "() '{$upload_dir_path}' is a file.");
        }

        if (!file_exists($upload_dir_path)) {
            if ($append) {
                if (!mkdir($upload_dir_path, 0777, true)) {
                    throw new Exception(__METHOD__ . "() Failed to create directory '{$upload_dir_path}'.");
                }
            } else {
                throw new Exception(__METHOD__ . "() Directory not found. '{$upload_dir_path}'");
            }
        }

        $this->upload_dir_path = $upload_dir_path;
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

        $upload_path = $this->directory_path . DIR_SEP . $file_name . '.' . $extension;

        if (!move_uploaded_file($tmp_name, $this->root_path . DIR_SEP . $upload_path)) {
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
        $delete_path = $this->root_path . $file_path;

        if (file_exists($delete_path)) {
            if (!unlink($this->root_path . $file_path)) {
                throw new RuntimeException("Failed to delete file '{$delete_path}'.");
            }
        }
    }
}