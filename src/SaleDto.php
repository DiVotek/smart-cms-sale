<?php

namespace SmartCms\Sale;

use Illuminate\Pagination\LengthAwarePaginator;
use SmartCms\Core\Models\Page;
use SmartCms\Core\Models\Seo;
use SmartCms\Core\Repositories\DtoInterface;
use SmartCms\Core\Traits\Dto\AsDto;
use SmartCms\Store\Models\Category;
use SmartCms\Store\Models\Product;
use SmartCms\Store\Repositories\Product\ProductDto;

class SaleDto implements DtoInterface
{
    use AsDto;

    public function __construct(public int $id, public string $name, public array $breadcrumbs, public LengthAwarePaginator $products, public array $categories = [], public ?string $image, public ?string $heading, public ?string $short_description, public ?string $content = '', public ?string $banner = '') {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'heading' => $this->heading ?? $this->name,
            'breadcrumbs' => array_map(fn($breadcrumb) => (object) $breadcrumb, $this->breadcrumbs),
            'image' => $this->validateImage($this->image ?? no_image()),
            'banner' => $this->validateImage($this->banner ?? no_image()),
            'products' => $this->products,
            'categories' => $this->categories,
            'summary' => $this->short_description ?? '',
            'content' => $this->content ?? '',
            ...$this->extra,
        ];
    }

    public static function make(): self
    {
        $categoryFilter = request()->get('categories', []);
        if (!is_array($categoryFilter)) {
            $categoryFilter = [];
        }
        $page = Page::query()->where('id', setting('pages.sale', 0))->first() ?? new Page(['name' => 'Sale', 'slug' => 'sale']);
        $seo = $page->seo()->where('language_id', current_lang_id())->first() ?? new Seo();
        $products = Product::query()->whereHas('promotions', function ($query) {
            $query->where('start_date', '<=', now())
                ->where('end_date', '>=', now());
        });
        $categories = $products->pluck('category_id')->unique()->toArray();
        $categories = Category::query()->whereIn('id', $categories)->pluck('name', 'id')->toArray();
        if (!empty($categoryFilter)) {
            $products = $products->whereIn('category_id', $categoryFilter);
        }
        $products = $products->paginate(15);
        $products->getCollection()->transform(function ($product) {
            return ProductDto::fromModel($product);
        });

        return new self(
            id: $page->id,
            name: $page->name(),
            breadcrumbs: $page->getBreadcrumbs(),
            products: $products,
            categories: $categories,
            image: null,
            heading: $seo->heading ?? $page->name(),
            short_description: $seo->short_description ?? '',
            content: $seo->content ?? '',
            banner: $seo->banner ?? null,
        );
    }
}
