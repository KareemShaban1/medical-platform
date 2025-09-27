<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogPost;
use App\Models\BlogCategory;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $blogPosts = BlogPost::with('blogCategory')
            ->active()
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Get filter options for the form
        $categories = BlogCategory::active()->select('id', 'name_ar', 'name_en')->get();
        $authors = ['dr-smith', 'dr-johnson', 'dr-williams', 'medical-team'];
        $dateRanges = ['week', 'month', 'year'];
        $tags = ['covid', 'vaccine', 'heart', 'mental', 'cancer'];

        return view('frontend.pages.blogs', compact(
            'blogPosts',
            'categories',
            'authors',
            'dateRanges',
            'tags'
        ));
    }

    public function filter(Request $request)
    {
        try {
            $query = BlogPost::with('blogCategory')
                ->active();

            // Search filter
            if ($request->filled('search')) {
                $searchTerm = trim($request->search);
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title_en', 'like', '%' . $searchTerm . '%')
                        ->orWhere('title_ar', 'like', '%' . $searchTerm . '%')
                        ->orWhere('content_en', 'like', '%' . $searchTerm . '%')
                        ->orWhere('content_ar', 'like', '%' . $searchTerm . '%');
                });
            }

            // Category filter
            if ($request->filled('category')) {
                $categoryId = $request->category;
                $query->where('blog_category_id', $categoryId);
            }

            // Author filter (we'll add author field to the model later)
            if ($request->filled('author')) {
                $query->where('author', $request->get('author'));
            }

            // Date filter
            if ($request->filled('date')) {
                $dateRange = $request->get('date');
                switch ($dateRange) {
                    case 'week':
                        $query->where('created_at', '>=', now()->subWeek());
                        break;
                    case 'month':
                        $query->where('created_at', '>=', now()->subMonth());
                        break;
                    case 'year':
                        $query->where('created_at', '>=', now()->subYear());
                        break;
                }
            }

            // Tags filter (we'll add tags field to the model later)
            if ($request->filled('tags')) {
                $tags = is_array($request->tags) ? $request->tags : [$request->tags];
                $query->where(function ($q) use ($tags) {
                    foreach ($tags as $tag) {
                        $q->orWhere('tags', 'like', '%' . $tag . '%');
                    }
                });
            }

            // Sort by
            $sortBy = $request->get('sort', 'newest');
            switch ($sortBy) {
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'popular':
                    $query->orderBy('views', 'desc'); // We'll add views field later
                    break;
                case 'title':
                    $query->orderBy('title_en', 'asc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            // Pagination
            $blogPosts = $query->paginate(12);

            // Add additional data for response
            $additionalData = [
                'filters_applied' => $this->getAppliedFilters($request),
                'total_posts' => BlogPost::active()->count(),
                'filtered_count' => $blogPosts->total()
            ];

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('frontend.partials.blog-grid', compact('blogPosts'))->render(),
                    'pagination' => $blogPosts->links()->toHtml(),
                    'count' => $blogPosts->total(),
                    'data' => $additionalData
                ]);
            }

            return view('frontend.pages.blogs', compact('blogPosts', 'additionalData'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error filtering blog posts: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error filtering blog posts. Please try again.');
        }
    }

    /**
     * Get the list of applied filters for display
     */
    private function getAppliedFilters(Request $request)
    {
        $filters = [];

        if ($request->filled('search')) {
            $filters['search'] = $request->search;
        }

        if ($request->filled('category')) {
            $category = BlogCategory::find($request->category);
            if ($category) {
                $filters['category'] = app()->getLocale() == 'ar' ? $category->name_ar : $category->name_en;
            }
        }

        if ($request->filled('author')) {
            $filters['author'] = ucfirst(str_replace('-', ' ', $request->author));
        }

        if ($request->filled('date')) {
            $dateLabels = [
                'week' => 'This Week',
                'month' => 'This Month',
                'year' => 'This Year'
            ];
            $filters['date'] = $dateLabels[$request->date] ?? $request->date;
        }

        if ($request->filled('tags')) {
            $filters['tags'] = is_array($request->tags) ? $request->tags : [$request->tags];
        }

        if ($request->filled('sort')) {
            $sortLabels = [
                'newest' => 'Newest First',
                'oldest' => 'Oldest First',
                'popular' => 'Most Popular',
                'title' => 'Title A-Z'
            ];
            $filters['sort'] = $sortLabels[$request->sort] ?? $request->sort;
        }

        return $filters;
    }
}
