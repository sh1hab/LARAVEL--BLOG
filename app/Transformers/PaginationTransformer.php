<?php

namespace App\Transformers;

class PaginationTransformer extends Transformer
{
    /**
     * Transform pagination data
     *
     * @param array $item
     * @return array
     */
    public function transform(array $item)
    {
        return [
            'first_page_url' => $item['first_page_url'],
            'from' => $item['from'],
            'last_page' => $item['last_page'],
            'last_page_url' => $item['last_page_url'],
            'next_page_url' => $item['next_page_url'],
            'path' => $item['path'],
            'per_page' => $item['per_page'],
            'prev_page_url' => $item['prev_page_url'],
            'to' => $item['to'],
            'total' => $item['total']
        ];
    }
}
