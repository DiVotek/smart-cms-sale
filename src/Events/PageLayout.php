<?php

namespace SmartCms\Sale\Events;

use Illuminate\Database\Eloquent\Builder;
use SmartCms\Core\Models\Layout;
use SmartCms\Core\Models\Page;

class PageLayout
{
    public function __invoke(Builder $query, Page $page)
    {
        $salePage = setting('pages.sale', 0);
        if ($page->id == $salePage) {
            $saleLayouts = Layout::query()->where('path', 'like', 'sale%')->get();
            return $query->whereIn('id', $saleLayouts->pluck('id'));
        }
    }
}
