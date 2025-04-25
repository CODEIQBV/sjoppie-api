<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Str;

class TagService
{
    public function createTag(array $data): Tag
    {
        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return Tag::create($data);
    }

    public function updateTag(Tag $tag, array $data): bool
    {
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $tag->update($data);
    }

    public function deleteTag(Tag $tag): bool
    {
        return $tag->delete();
    }

    public function getTag(int|string $identifier): ?Tag
    {
        return Tag::where(is_numeric($identifier) ? 'id' : 'slug', $identifier)
            ->with(['products', 'categories'])
            ->first();
    }

    public function getAllTags()
    {
        return Tag::with(['products', 'categories'])->get();
    }
} 