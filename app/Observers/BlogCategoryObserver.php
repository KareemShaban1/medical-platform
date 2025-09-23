<?php

namespace App\Observers;

use App\Models\BlogCategory;
use Illuminate\Support\Str;
class BlogCategoryObserver
{
    //
    public function creating(BlogCategory $blogCategory)
    {
        $blogCategory->slug_ar = Str::slug($blogCategory->name_ar);
        $blogCategory->slug_en = Str::slug($blogCategory->name_en);
    }
    public function updating(BlogCategory $blogCategory)
    {
        $blogCategory->slug_ar = Str::slug($blogCategory->name_ar);
        $blogCategory->slug_en = Str::slug($blogCategory->name_en);
    }
}