<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Str;
class CategoryObserver
{
    //
    public function creating(Category $category)
    {
        $category->slug_ar = Str::slug($category->name_ar);
        $category->slug_en = Str::slug($category->name_en);
        $category->admin_id = auth()->guard('admin')->user()->id;
    }
    public function updating(Category $category)
    {
        $category->slug_ar = Str::slug($category->name_ar);
        $category->slug_en = Str::slug($category->name_en);
    }
}