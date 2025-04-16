<?php

namespace SmartCms\Sale;

use SmartCms\Core\Resources\BaseResource;

class CategoryFilterResource extends BaseResource
{
    public function prepareData($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'link' => $this->resource->route(),
            'image' => $this->validateImage($this->resource->image),
            'selected' => $this->context('selected', false),
        ];
    }
}
