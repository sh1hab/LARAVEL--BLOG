<?php

namespace App\Transformers;

use App\Transformers\Transformer;

class PostTransformer extends Transformer
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
            'title' => $item['title'],
            'content' => $item['content'],
            'slug' => $item['slug'],
            'upload' => $item['upload']
        ];

        return $transformedItem;
    }
}
