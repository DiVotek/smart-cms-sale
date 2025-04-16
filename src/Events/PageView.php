<?php

namespace SmartCms\Sale\Events;

use SmartCms\Core\Models\Page;
use SmartCms\Core\Services\Frontend\LayoutService;
use SmartCms\Sale\SaleResource;

class PageView
{
    public function __invoke(mixed &$resource, Page $page)
    {
        $layout = $page?->layout;
        if (! $layout) {
            return;
        }
        $service = new LayoutService();
        $meta = $service->getSectionMetadata($layout->path);
        $perPage = 15;
        if ($meta && isset($meta['per_page'])) {
            $perPage = (int) $meta['per_page'];
        }
        if (str_contains($layout->path, 'sale')) {
            $resource = SaleResource::make($page, ['per_page' => $perPage])->get();
        }
    }
}
