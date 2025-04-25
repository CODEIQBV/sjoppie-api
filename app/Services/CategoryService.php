<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Str;

class CategoryService
{
    public function createCategory(array $data): Category
    {
        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return Category::create($data);
    }

    public function updateCategory(Category $category, array $data): bool
    {
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $category->update($data);
    }

    public function deleteCategory(Category $category): bool
    {
        return $category->delete();
    }

    public function getCategory(int|string $identifier): ?Category
    {
        return Category::where(is_numeric($identifier) ? 'id' : 'slug', $identifier)
            ->with(['parent', 'children', 'products', 'tags'])
            ->first();
    }

    public function getAllCategories()
    {
        return Category::with(['parent', 'children', 'products', 'tags'])
            ->orderBy('order')
            ->get();
    }

    public function getCategoryTree()
    {
        return Category::with(['children'])
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();
    }
} 