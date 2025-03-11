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
        $layout = $this->createLayout('sale');
        $page = $this->createPage('Sale', 'sale', $layout);
        setting([
            'pages.sale' => $page,
        ]);
        $this->call('optimize:clear');
        $this->info('Installed Smart CMS sale module');
    }

    public function createLayout(string $name): int
    {
        $this->call('make:layout', [
            'name' => $name,
        ]);
        $layout = Layout::query()->updateOrCreate(
            [
                'path' => $name . '/' . $name,
            ],
            [
                'name' => ucfirst($name),
                'template' => template(),
                'status' => 1,
                'schema' => [],
                'value' => [],
            ]
        );

        return $layout->id;
    }

    public function createPage(string $name, string $slug, int $layout): int
    {
        $page = Page::query()->updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'layout' => $layout,
            ]
        );

        return $page->id;
    }
}
