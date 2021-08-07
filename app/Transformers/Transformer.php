<?php

namespace App\Transformers;

abstract class Transformer
{
    /**
     * Transform collection
     *
     * @param array $items
     * @return array
     */
    public function transformCollection(array $items)
    {
        return array_map([$this, 'transform'], $items);
    }

    /**
     * Transform
     * @param array $item
     */
    public abstract function transform(array $item);
}
