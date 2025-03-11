<?php

namespace SmartCms\Sale\Events;

use SmartCms\Core\Components\Pages\PageComponent;
use SmartCms\Sale\SaleDto;

class PageView
{
    public function __invoke(PageComponent $component)
    {
        $layout = $component->layout;
        if (! $layout) {
            return;
        }
        if ($layout->path == 'sale/sale') {
            $component->dto = SaleDto::make();
            dd($component->dto);
        }
    }
}
