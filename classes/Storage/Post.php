<?php

class Storage_Post extends Storage_Base
{
    protected $table_name = 'post';

    protected $validation_rule = [
        'name'  => [
            'required' => false,
            'length'   => [
                'min' => '3',
                'max' => '16',
            ],
        ],
        'title' => [
            'required' => true,
            'length'   => [
                'min' => 10,
                'max' => 32,
            ],
        ],
        'comment' => [
            'required' => true,
            'length'   => [
                'min' => 10,
                'max' => 200,
            ],
        ],
        'image_file' => [
            'required'   => false,
            'mime_types' => [
                'jpeg' => 'image/jpeg',
                'jpg'  => 'image/jpeg',
                'png'  => 'image/png',
                'gif'  => 'image/gif',
            ],
            'file_size' => [
                'max' => 1024 * 1024,
            ],
        ],
        'password' => [
            'required' => false,
            'digit'    => 4,
        ],
    ];

    public function validate(array $inputs)
    {
        $validator = new Validator();
        $validator->setAttributeValidationRules($this->validation_rule);

        return $validator->validate($inputs);
    }

    public function buildWhereToSearch(?array $search_conditions)
    {
        if (is_null($search_conditions)) {
            return null;
        }

        $title_search_string   = $search_conditions['title']   ?? null;
        $comment_search_string = $search_conditions['comment'] ?? null;
        $image_status          = $search_conditions['image']   ?? null;
        $post_status           = $search_conditions['post']    ?? null;

        $conditions = [];
        $values     = [];

        if (!is_null($title_search_string)) {
            $conditions[] = 'title LIKE ?';
            $values[]     = '%' . $title_search_string . '%';
        }

        if (!is_null($comment_search_string)) {
            $conditions[] = 'comment LIKE ?';
            $values[]     = '%' . $comment_search_string . '%';
        }

        if (!is_null($image_status)) {
            switch ($image_status) {
                case 'with':
                    $conditions[] = 'image_path IS NOT NULL';
                    break;
                case 'without':
                    $conditions[] = 'image_path IS NULL';
                    break;
            }
        }

        if (!is_null($post_status)) {
            switch ($post_status) {
                case 'on':
                    $conditions[] = 'is_deleted = ?';
                    $values[]     = 0;
                    break;
                case 'delete':
                    $conditions[] = 'is_deleted = ?';
                    $values[]     = 1;
                    break;
            }
        }

        if (empty($conditions)) {
            $where = null;
        } else {
            $condition = implode(' AND ', $conditions);
            $where     = ['condition' => $condition, 'values' => $values];
        }

        return $where;
    }
}