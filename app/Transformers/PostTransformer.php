<?php

namespace App\Transformers;

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
        return [
            'id' => $item['id'],
            'title' => $item['title'],
            'content' => $item['content'],
            'slug' => $item['slug'],
            'upload' => $item['upload'] ?? null,
            'author' => $item['author'] ?? null,
        ];
    }
}
