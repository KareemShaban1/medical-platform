<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    //
    public function index()
    {
        $products = Product::approved()->active()->with('categories')->paginate(20);
        $categories = Category::active()->select('id', 'name_ar', 'name_en')->get();
        return view('frontend.pages.products.index', compact('products', 'categories'));
    }

    public function filter(Request $request)
    {
        try {
            $query = Product::approved()->active()->with('categories');

            // Search filter - search in name and description
            if ($request->filled('search')) {
                $searchTerm = trim($request->search);
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name_en', 'like', '%' . $searchTerm . '%')
                        ->orWhere('name_ar', 'like', '%' . $searchTerm . '%')
                        ->orWhere('description_en', 'like', '%' . $searchTerm . '%')
                        ->orWhere('description_ar', 'like', '%' . $searchTerm . '%');
                });
            }

            // Category filter - handle multiple categories
            if ($request->filled('category')) {
                $categoryId = $request->category;
                $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                });
            }

            // Price filter - handle different price ranges
            if ($request->filled('price')) {
                $priceRange = $request->price;
                switch ($priceRange) {
                    case '0-50':
                        $query->whereBetween('price_after', [0, 50]);
                        break;
                    case '50-100':
                        $query->whereBetween('price_after', [50, 100]);
                        break;
                    case '100-200':
                        $query->whereBetween('price_after', [100, 200]);
                        break;
                    case '200+':
                        $query->where('price_after', '>=', 200);
                        break;
                }
            }

            // Stock filter - only show products with stock
            if ($request->has('in_stock') && $request->in_stock) {
                $query->where('stock', '>', 0);
            }

            // Supplier filter
            if ($request->filled('supplier')) {
                $query->where('supplier_id', $request->supplier);
            }

            // Date range filter
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Sort filter with proper column mapping
            $sortBy = $request->get('sort', 'name');
            switch ($sortBy) {
                case 'name':
                    $query->orderBy('name_en', 'asc');
                    break;
                case 'name-desc':
                    $query->orderBy('name_en', 'desc');
                    break;
                case 'price':
                    $query->orderBy('price_after', 'asc');
                    break;
                case 'price-desc':
                    $query->orderBy('price_after', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'stock':
                    $query->orderBy('stock', 'desc');
                    break;
                case 'discount':
                    $query->orderBy('discount_value', 'desc');
                    break;
                default:
                    $query->orderBy('name_en', 'asc');
            }

            // Get pagination per page
            $perPage = $request->get('per_page', 20);
            $perPage = min($perPage, 100); // Limit max per page to 100

            $products = $query->paginate($perPage);

            // Add additional data for response
            $additionalData = [
                'filters_applied' => $this->getAppliedFilters($request),
                'total_products' => Product::approved()->active()->count(),
                'filtered_count' => $products->total()
            ];

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('frontend.pages.products.partials.products-grid', compact('products'))->render(),
                    'pagination' => view('frontend.pages.products.partials.pagination', compact('products'))->render(),
                    'count' => $products->total(),
                    'data' => $additionalData
                ]);
            }

            return view('frontend.pages.products.index', compact('products', 'additionalData'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error filtering products: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error filtering products. Please try again.');
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
            $category = Category::find($request->category);
            if ($category) {
                $filters['category'] = app()->getLocale() == 'ar' ? $category->name_ar : $category->name_en;
            }
        }

        if ($request->filled('price')) {
            $filters['price'] = $request->price;
        }

        if ($request->filled('sort')) {
            $filters['sort'] = $request->sort;
        }

        return $filters;
    }

    /**
     * Get products by category
     */
    public function category($categoryId)
    {
        $category = Category::active()->findOrFail($categoryId);
        $products = Product::approved()->active()
            ->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            })
            ->with('categories')
            ->paginate(20);

        $categories = Category::active()->select('id', 'name_ar', 'name_en')->get();

        return view('frontend.pages.products.index', compact('products', 'categories', 'category'));
    }

    /**
     * Get products by supplier
     */
    public function supplier($supplierId)
    {
        $products = Product::approved()->active()
            ->where('supplier_id', $supplierId)
            ->with('categories')
            ->paginate(20);

        $categories = Category::active()->select('id', 'name_ar', 'name_en')->get();

        return view('frontend.pages.products.index', compact('products', 'categories'));
    }

    /**
     * Get products on sale/discount
     */
    public function onSale()
    {
        $products = Product::approved()->active()
            ->where('discount_value', '>', 0)
            ->with('categories')
            ->orderBy('discount_value', 'desc')
            ->paginate(20);

        $categories = Category::active()->select('id', 'name_ar', 'name_en')->get();

        return view('frontend.pages.products.index', compact('products', 'categories'));
    }

    /**
     * Get products in stock
     */
    public function inStock()
    {
        $products = Product::approved()->active()
            ->where('stock', '>', 0)
            ->with('categories')
            ->orderBy('stock', 'desc')
            ->paginate(20);

        $categories = Category::active()->select('id', 'name_ar', 'name_en')->get();

        return view('frontend.pages.products.index', compact('products', 'categories'));
    }

    /**
     * Get recently added products
     */
    public function recent()
    {
        $products = Product::approved()->active()
            ->with('categories')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $categories = Category::active()->select('id', 'name_ar', 'name_en')->get();

        return view('frontend.pages.products.index', compact('products', 'categories'));
    }

    /**
     * Show product details
     */
    public function show($id)
    {
        $product = Product::approved()->active()
            ->with(['categories', 'supplier'])
            ->findOrFail($id);

        // Get related products from the same category
        $relatedProducts = Product::approved()->active()
            ->where('id', '!=', $id)
            ->whereHas('categories', function ($query) use ($product) {
                $query->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->with('categories')
            ->limit(8)
            ->get();

        // Get products from the same supplier
        $supplierProducts = Product::approved()->active()
            ->where('id', '!=', $id)
            ->where('supplier_id', $product->supplier_id)
            ->with('categories')
            ->limit(4)
            ->get();

        return view('frontend.pages.products.show', compact('product', 'relatedProducts', 'supplierProducts'));
    }
}
