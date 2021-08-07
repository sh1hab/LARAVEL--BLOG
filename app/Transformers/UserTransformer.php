<?php

namespace App\Transformers;

use App\Transformers\Transformer;

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
        $transformedItem = [
            'id' => $item['id'],
            'name' => $item['name'],
            'email' => $item['email'],
            'role_id' => $item['role_id'],
            'role' => $item['role'],
        ];

        return $transformedItem;
    }
}
