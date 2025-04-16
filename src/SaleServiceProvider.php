<?php

namespace SmartCms\Sale;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use SmartCms\Core\Admin\Resources\StaticPageResource;
use SmartCms\Sale\Events\PageLayout;
use SmartCms\Sale\Events\PageView;

class SaleServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            Commands\Install::class,
        ]);
    }

    public function boot()
    {
        Event::listen('cms.page.construct', PageView::class);
        StaticPageResource::registerHook('page.layout', PageLayout::class);
    }
}
