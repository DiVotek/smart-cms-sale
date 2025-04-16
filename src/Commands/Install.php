<?php

namespace SmartCms\Sale\Commands;

use Illuminate\Console\Command;
use SmartCms\Core\Models\Layout;
use SmartCms\Core\Models\Page;

class Install extends Command
{
    protected $signature = 'sale:install';

    protected $description = 'Install Smart CMS sale module';

    public function handle()
    {
        $this->call('make:layout', [
            'name' => 'sale.index',
        ]);
        $layout = Layout::query()->where('path', 'sale.index')->first();
        if (!$layout) {
            $this->error('Layout not found');
            return;
        }
        $page = Page::query()->updateOrCreate([
            'slug' => 'sale',
        ], [
            'name' => 'Sale',
            'layout_id' => $layout->id,
        ]);
        setting([
            'pages.sale' => $page->id,
        ]);
        $this->call('optimize:clear');
        $this->info('Installed Smart CMS sale module');
    }
}
