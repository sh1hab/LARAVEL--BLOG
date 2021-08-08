<?php

namespace App\Transformers;

class UserTransformer extends Transformer
{
    /**
     * Transform customer
     *
     * @param array $item
     * @return array
     */
    public function transform(array $item)
    {
        return [
            'id' => $item['id'],
            'name' => $item['name'],
            'email' => $item['email'],
            'role_id' => $item['role_id'],
            'role' => $item['role'],
        ];
    }
}
