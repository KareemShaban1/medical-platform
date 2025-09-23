<?php

namespace App\Observers;

use App\Models\BlogPost;
use Illuminate\Support\Str;
class BlogPostObserver
{
    //
    public function creating(BlogPost $blogPost)
    {
        $blogPost->slug_ar = Str::slug($blogPost->title_ar);
        $blogPost->slug_en = Str::slug($blogPost->title_en);
    }
    public function updating(BlogPost $blogPost)
    {
        $blogPost->slug_ar = Str::slug($blogPost->title_ar);
        $blogPost->slug_en = Str::slug($blogPost->title_en);
    }
}