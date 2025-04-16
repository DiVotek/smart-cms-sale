<?php

namespace SmartCms\Sale;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use SmartCms\Core\Resources\BaseResource;
use SmartCms\Store\Models\Category;
use SmartCms\Store\Models\Product;
use SmartCms\Store\Resources\Product\ProductResource;

class SaleResource extends BaseResource
{
    public array $categories = [];

    public function prepareData($request): array
    {
        $seo = $this->resource->getSeo();
        $name = $this->resource->name();
        $products = $this->getProducts();
        $data = [
            'id' => $this->resource->id,
            'name' => $name,
            'heading' => $seo->heading ?? $name,
            'breadcrumbs' => array_map(fn($breadcrumb) => (object) $breadcrumb, $this->resource->getBreadcrumbs()),
            'image' => $this->validateImage($this->resource->image),
            'banner' => $this->validateImage($this->resource->banner),
            'categories' => $this->categories,
            'products' => $products,
            'summary' => $seo->summary ?? '',
            'content' => $seo->content ?? '',
            'parent' => null,
            'title' => $seo->title ?? $name,
            'meta_description' => $seo->description ?? '',
        ];

        return $data;
    }

    public function getProducts(): LengthAwarePaginator
    {
        $categoryFilter = request()->get('categories', []);
        if (!is_array($categoryFilter)) {
            $categoryFilter = [];
        }
        $q = Product::query()->whereHas('promotions', function ($query) {
            $query->where('start_date', '<=', now())
                ->where('end_date', '>=', now());
        });
        $categories = Category::query()->whereIn('id', $q->clone()->pluck('category_id')->unique()->toArray())->get();
        $this->categories = $categories->map(fn(Category $category) => CategoryFilterResource::make($category)->get())->toArray();
        if (!empty($categoryFilter)) {
            $q = $q->whereIn('category_id', $categoryFilter);
        }
        $q = $q->distinct();
        $sort = request()->query('sort', null);
        if ($sort && isset($this->availableSort[$sort])) {
            $sort = [
                'pa' => 'price_asc',
                'pd' => 'price_desc',
                'na' => 'name_asc',
                'nd' => 'name_desc',
            ][$sort];
            $q = $q->orderBy(explode('_', $sort)[0], explode('_', $sort)[1]);
        }
        $perPage = $this->context('per_page', 15);
        $products = $q->paginate($perPage);
        $products->getCollection()->transform(function ($product) {
            return ProductResource::make($product)->get();
        });

        return $products;
    }
}
