# Banners Module Implementation Guide

## 📋 Table of Contents
1. [Overview](#overview)
2. [Database Schema](#database-schema)
3. [Model Implementation](#model-implementation)
4. [Migration Files](#migration-files)
5. [Repository Pattern](#repository-pattern)
6. [Controller Implementation](#controller-implementation)
7. [Form Request Validation](#form-request-validation)
8. [Routes Configuration](#routes-configuration)
9. [Views Implementation](#views-implementation)
10. [Frontend Display](#frontend-display)
11. [Permissions Setup](#permissions-setup)
12. [Service Provider Registration](#service-provider-registration)
13. [Additional Features](#additional-features)

---

## 🎯 Overview

The Banners Module allows administrators to manage website banners/advertisements from the admin dashboard. Banners can contain:
- **Image**: Banner image/visual content (managed via Spatie Media Library)
- **Text**: Title, description, or call-to-action text
- **Link**: URL to redirect when banner is clicked
- **Content**: Rich HTML content for complex banners
- **Position**: Dynamic position identifier (any string value, e.g., 'header', 'sidebar', 'footer', 'homepage_banner', etc.)
- **Display Settings**: Start/end dates, status, priority, target pages
- **Analytics**: Click tracking, view counts

**Key Features:**
- Uses Spatie Media Library for image management (no image fields in database)
- Dynamic position system (no enum restrictions)
- Supports desktop and mobile-specific images

---

## 🗄️ Database Schema

### Main Table: `banners`

```php
Schema::create('banners', function (Blueprint $table) {
    $table->id();
    
    // Basic Information
    $table->string('title')->nullable(); // Banner title/name
    $table->text('description')->nullable(); // Short description
    $table->text('content')->nullable(); // Rich HTML content
    
    // Link & CTA
    $table->string('link_url')->nullable(); // Click destination URL
    $table->string('link_text')->nullable(); // Call-to-action button text
    $table->boolean('open_in_new_tab')->default(false); // Open link in new tab
    
    // Display Settings
    $table->string('position')->default('homepage_banner'); // Dynamic position (e.g., 'header', 'sidebar', 'footer', 'homepage_hero', etc.)
    
    // Targeting & Scheduling
    $table->timestamp('start_at')->nullable(); // Start date/time
    $table->timestamp('end_at')->nullable(); // End date/time
    $table->boolean('status')->default(true); // Active/Inactive
    $table->integer('priority')->default(0); // Display order (higher = first)
    
    // Targeting Options
    $table->json('target_pages')->nullable(); // Specific pages to show on
    $table->json('target_categories')->nullable(); // Show on specific categories
    $table->json('target_products')->nullable(); // Show on specific products
    $table->boolean('show_on_all_pages')->default(false); // Show everywhere
    
    // Analytics
    $table->integer('views_count')->default(0); // Total views
    $table->integer('clicks_count')->default(0); // Total clicks
    
    // Metadata
    $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
    $table->softDeletes(); // Soft delete support
    $table->timestamps();
    
    // Indexes
    $table->index('position');
    $table->index('status');
    $table->index('priority');
    $table->index(['start_at', 'end_at']);
});
```

**Note:** Images are handled using Spatie Media Library. The `media` table (created by Spatie) will store all banner images.

### Analytics Table: `banner_clicks` (Optional - for detailed click tracking)

```php
Schema::create('banner_clicks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('banner_id')->constrained('banners')->cascadeOnDelete();
    $table->string('ip_address')->nullable();
    $table->string('user_agent')->nullable();
    $table->string('referrer')->nullable();
    $table->morphs('clickable'); // Track who clicked (user, guest, etc.)
    $table->timestamp('clicked_at');
    
    $table->index('banner_id');
    $table->index('clicked_at');
});
```

---

## 📦 Model Implementation

### File: `app/Models/Banner.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Banner extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'title',
        'description',
        'content',
        'link_url',
        'link_text',
        'open_in_new_tab',
        'position',
        'start_at',
        'end_at',
        'status',
        'priority',
        'target_pages',
        'target_categories',
        'target_products',
        'show_on_all_pages',
        'views_count',
        'clicks_count',
        'created_by',
    ];

    protected $appends = ['image', 'mobile_image'];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'status' => 'boolean',
        'open_in_new_tab' => 'boolean',
        'show_on_all_pages' => 'boolean',
        'target_pages' => 'array',
        'target_categories' => 'array',
        'target_products' => 'array',
        'priority' => 'integer',
        'views_count' => 'integer',
        'clicks_count' => 'integer',
    ];

    /**
     * Get the admin who created this banner
     */
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Get banner clicks
     */
    public function clicks()
    {
        return $this->hasMany(BannerClick::class);
    }

    /**
     * Get banner image (desktop) from Spatie Media
     */
    public function getImageAttribute()
    {
        $media = $this->getMedia('banner_image')->first();
        return $media ? $media->getUrl() : null;
    }

    /**
     * Get banner mobile image from Spatie Media
     */
    public function getMobileImageAttribute()
    {
        $media = $this->getMedia('banner_mobile_image')->first();
        return $media ? $media->getUrl() : null;
    }

    /**
     * Get all banner images
     */
    public function getImagesAttribute()
    {
        return $this->getMedia('banner_image')->map(function ($media) {
            return $media->getUrl();
        })->toArray();
    }

    /**
     * Scope: Get active banners
     */
    public function scopeActive($query)
    {
        $now = now();
        return $query->where('status', true)
            ->where(function($q) use ($now) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
            });
    }

    /**
     * Scope: Get banners by position
     */
    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Scope: Order by priority
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('priority', 'desc')->orderBy('created_at', 'desc');
    }

    /**
     * Check if banner is currently active
     */
    public function isActive(): bool
    {
        if (!$this->status) {
            return false;
        }

        $now = now();
        
        if ($this->start_at && $this->start_at->isFuture()) {
            return false;
        }

        if ($this->end_at && $this->end_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Increment views count
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Increment clicks count
     */
    public function incrementClicks(): void
    {
        $this->increment('clicks_count');
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('banner_image')
            ->singleFile();

        $this->addMediaCollection('banner_mobile_image')
            ->singleFile();
    }
}
```

### File: `app/Models/BannerClick.php` (Optional)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerClick extends Model
{
    use HasFactory;

    protected $fillable = [
        'banner_id',
        'ip_address',
        'user_agent',
        'referrer',
        'clickable_type',
        'clickable_id',
        'clicked_at',
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
    ];

    public function banner()
    {
        return $this->belongsTo(Banner::class);
    }

    public function clickable()
    {
        return $this->morphTo();
    }

    /**
     * Scope: Get clicks from authenticated users only
     */
    public function scopeFromUsers($query)
    {
        return $query->whereNotNull('clickable_type')
                    ->whereNotNull('clickable_id');
    }

    /**
     * Scope: Get clicks from guests only
     */
    public function scopeFromGuests($query)
    {
        return $query->where(function($q) {
            $q->whereNull('clickable_type')
              ->orWhere('clickable_type', 'guest');
        })->whereNull('clickable_id');
    }
}
```

---

## 🔄 Migration Files

### File: `database/migrations/YYYY_MM_DD_HHMMSS_create_banners_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->string('link_url')->nullable();
            $table->string('link_text')->nullable();
            $table->boolean('open_in_new_tab')->default(false);
            $table->string('position')->default('homepage_banner');
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('priority')->default(0);
            $table->json('target_pages')->nullable();
            $table->json('target_categories')->nullable();
            $table->json('target_products')->nullable();
            $table->boolean('show_on_all_pages')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('clicks_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index('position');
            $table->index('status');
            $table->index('priority');
            $table->index(['start_at', 'end_at']);
        });

        Schema::create('banner_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('banner_id')->constrained('banners')->cascadeOnDelete();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->morphs('clickable');
            $table->timestamp('clicked_at');
            
            $table->index('banner_id');
            $table->index('clicked_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banner_clicks');
        Schema::dropIfExists('banners');
    }
};
```

---

## 🔌 Repository Pattern

### Interface: `app/Interfaces/Admin/BannerRepositoryInterface.php`

```php
<?php

namespace App\Interfaces\Admin;

interface BannerRepositoryInterface
{
    public function data();
    public function store($request);
    public function show($id);
    public function update($request, $id);
    public function destroy($id);
    public function trashData();
    public function restore($id);
    public function forceDelete($id);
    public function toggleStatus($id);
    public function incrementViews($id);
    public function incrementClicks($id);
}
```

### Implementation: `app/Repository/Admin/BannerRepository.php`

```php
<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\BannerRepositoryInterface;
use App\Models\Banner;
use App\Traits\HandlesMediaUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BannerRepository implements BannerRepositoryInterface
{
    use HandlesMediaUploads;
    public function data()
    {
        $banners = Banner::query();

        return datatables()->of($banners)
            ->addColumn('image_preview', fn($item) => $this->imagePreview($item))
            ->editColumn('position', fn($item) => $this->formatPosition($item))
            ->editColumn('status', fn($item) => $this->statusBadge($item))
            ->editColumn('start_at', fn($item) => $item->start_at ? $item->start_at->format('Y-m-d H:i') : '-')
            ->editColumn('end_at', fn($item) => $item->end_at ? $item->end_at->format('Y-m-d H:i') : '-')
            ->addColumn('stats', fn($item) => $this->statsBadge($item))
            ->addColumn('action', fn($item) => $this->actions($item))
            ->rawColumns(['image_preview', 'status', 'stats', 'action'])
            ->make(true);
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['created_by'] = Auth::guard('admin')->id();

            $banner = Banner::create($data);

            // Handle image uploads using Spatie Media
            $this->processMedia($banner, $request, [
                ['field' => 'image', 'collection' => 'banner_image', 'multiple' => false],
                ['field' => 'mobile_image', 'collection' => 'banner_mobile_image', 'multiple' => false],
            ], 'created');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('Banner created successfully'),
                'redirect' => route('admin.banners.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('Error creating banner: ') . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        return Banner::with('creator')->findOrFail($id);
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $banner = Banner::findOrFail($id);
            $data = $request->validated();

            $banner->update($data);

            // Handle image uploads using Spatie Media
            $this->processMedia($banner, $request, [
                ['field' => 'image', 'collection' => 'banner_image', 'multiple' => false],
                ['field' => 'mobile_image', 'collection' => 'banner_mobile_image', 'multiple' => false],
            ], 'updated');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('Banner updated successfully'),
                'redirect' => route('admin.banners.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('Error updating banner: ') . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $banner = Banner::findOrFail($id);
            $banner->delete();

            return response()->json([
                'success' => true,
                'message' => __('Banner deleted successfully')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error deleting banner: ') . $e->getMessage()
            ], 500);
        }
    }

    public function trashData()
    {
        $banners = Banner::onlyTrashed();

        return datatables()->of($banners)
            ->addColumn('image_preview', fn($item) => $this->imagePreview($item))
            ->editColumn('deleted_at', fn($item) => $item->deleted_at->format('Y-m-d H:i'))
            ->addColumn('action', fn($item) => $this->trashActions($item))
            ->rawColumns(['image_preview', 'action'])
            ->make(true);
    }

    public function restore($id)
    {
        try {
            $banner = Banner::withTrashed()->findOrFail($id);
            $banner->restore();

            return response()->json([
                'success' => true,
                'message' => __('Banner restored successfully')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error restoring banner: ') . $e->getMessage()
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $banner = Banner::withTrashed()->findOrFail($id);

            // Clear all media collections (Spatie Media handles deletion automatically)
            $banner->clearMediaCollection('banner_image');
            $banner->clearMediaCollection('banner_mobile_image');

            $banner->forceDelete();

            return response()->json([
                'success' => true,
                'message' => __('Banner permanently deleted')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error deleting banner: ') . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $banner = Banner::findOrFail($id);
            $banner->status = !$banner->status;
            $banner->save();

            return response()->json([
                'success' => true,
                'message' => __('Banner status updated'),
                'status' => $banner->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating status: ') . $e->getMessage()
            ], 500);
        }
    }

    public function incrementViews($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->incrementViews();
        return response()->json(['success' => true]);
    }

    public function incrementClicks($id)
    {
        try {
            DB::beginTransaction();

            $banner = Banner::findOrFail($id);
            $banner->incrementClicks();

            // Optional: Store detailed click information
            // Handle authenticated users and guests
            $clickableType = null;
            $clickableId = null;

            if (Auth::check()) {
                // Authenticated user - store the user model class and ID
                $clickableType = get_class(Auth::user());
                $clickableId = Auth::id();
            } else {
                // Guest user - you can either:
                // Option 1: Store null for both (recommended)
                $clickableType = null;
                $clickableId = null;
                
                // Option 2: Use a string identifier for guests (alternative)
                // $clickableType = 'guest';
                // $clickableId = null; // or use a hash of IP for tracking: md5(request()->ip())
            }

            $banner->clicks()->create([
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'referrer' => request()->header('referer'),
                'clickable_type' => $clickableType,
                'clickable_id' => $clickableId,
                'clicked_at' => now(),
            ]);

            DB::commit();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false], 500);
        }
    }

    // Helper methods for DataTables
    private function imagePreview($item)
    {
        $image = $item->image;
        if ($image) {
            return '<img src="' . $image . '" alt="' . ($item->title ?? '') . '" class="img-thumbnail" style="max-width: 100px; max-height: 60px;">';
        }
        return '<span class="text-muted">' . __('No image') . '</span>';
    }

    private function formatPosition($item)
    {
        // Position is dynamic, so just display it as-is with a badge
        $position = ucfirst(str_replace('_', ' ', $item->position));
        return '<span class="badge bg-info">' . $position . '</span>';
    }

    private function statusBadge($item)
    {
        $badge = $item->status ? 'success' : 'danger';
        $text = $item->status ? __('Active') : __('Inactive');
        return '<span class="badge bg-' . $badge . '">' . $text . '</span>';
    }

    private function statsBadge($item)
    {
        return '<small class="text-muted">' . 
               __('Views') . ': ' . $item->views_count . ' | ' . 
               __('Clicks') . ': ' . $item->clicks_count . 
               '</small>';
    }

    private function actions($item)
    {
        $html = '<div class="btn-group" role="group">';
        
        if (hasPermission('update banner')) {
            $html .= '<a href="' . route('admin.banners.edit', $item->id) . '" class="btn btn-sm btn-primary" title="' . __('Edit') . '">
                        <i class="fas fa-edit"></i>
                      </a>';
        }

        if (hasPermission('delete banner')) {
            $html .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $item->id . '" title="' . __('Delete') . '">
                        <i class="fas fa-trash"></i>
                      </button>';
        }

        if (hasPermission('update banner')) {
            $statusClass = $item->status ? 'btn-warning' : 'btn-success';
            $statusIcon = $item->status ? 'fa-eye-slash' : 'fa-eye';
            $statusTitle = $item->status ? __('Deactivate') : __('Activate');
            
            $html .= '<button type="button" class="btn btn-sm ' . $statusClass . ' toggle-status-btn" data-id="' . $item->id . '" title="' . $statusTitle . '">
                        <i class="fas ' . $statusIcon . '"></i>
                      </button>';
        }

        $html .= '</div>';
        return $html;
    }

    private function trashActions($item)
    {
        $html = '<div class="btn-group" role="group">';
        
        $html .= '<button type="button" class="btn btn-sm btn-success restore-btn" data-id="' . $item->id . '" title="' . __('Restore') . '">
                    <i class="fas fa-undo"></i>
                  </button>';
        
        $html .= '<button type="button" class="btn btn-sm btn-danger force-delete-btn" data-id="' . $item->id . '" title="' . __('Permanently Delete') . '">
                    <i class="fas fa-trash-alt"></i>
                  </button>';
        
        $html .= '</div>';
        return $html;
    }
}
```

---

## 🎮 Controller Implementation

### File: `app/Http/Controllers/Backend/Dashboards/Admin/BannerController.php`

```php
<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Banner\StoreBannerRequest;
use App\Http\Requests\Admin\Banner\UpdateBannerRequest;
use App\Interfaces\Admin\BannerRepositoryInterface;
use App\Models\Banner;

class BannerController extends Controller
{
    protected $repo;

    public function __construct(BannerRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        abort_if(!hasPermission('view banners'), 403, __('You are not authorized to view banners'));
        return view('backend.dashboards.admin.pages.banners.index');
    }

    public function data()
    {
        abort_if(!hasPermission('view banners'), 403, __('You are not authorized to view banners'));
        return $this->repo->data();
    }

    public function create()
    {
        abort_if(!hasPermission('create banner'), 403, __('You are not authorized to create banner'));
        return view('backend.dashboards.admin.pages.banners.create');
    }

    public function store(StoreBannerRequest $request)
    {
        abort_if(!hasPermission('create banner'), 403, __('You are not authorized to create banner'));
        return $this->repo->store($request);
    }

    public function edit($id)
    {
        abort_if(!hasPermission('update banner'), 403, __('You are not authorized to view banner'));
        $banner = $this->repo->show($id);
        return view('backend.dashboards.admin.pages.banners.edit', compact('banner'));
    }

    public function update(UpdateBannerRequest $request, $id)
    {
        abort_if(!hasPermission('update banner'), 403, __('You are not authorized to update banner'));
        return $this->repo->update($request, $id);
    }

    public function destroy($id)
    {
        abort_if(!hasPermission('delete banner'), 403, __('You are not authorized to delete banner'));
        return $this->repo->destroy($id);
    }

    public function trash()
    {
        abort_if(!hasPermission('view banners'), 403, __('You are not authorized to view banners'));
        return view('backend.dashboards.admin.pages.banners.trash');
    }

    public function trashData()
    {
        abort_if(!hasPermission('view banners'), 403, __('You are not authorized to view banners'));
        return $this->repo->trashData();
    }

    public function restore($id)
    {
        abort_if(!hasPermission('delete banner'), 403, __('You are not authorized to restore banner'));
        return $this->repo->restore($id);
    }

    public function forceDelete($id)
    {
        abort_if(!hasPermission('delete banner'), 403, __('You are not authorized to delete banner'));
        return $this->repo->forceDelete($id);
    }

    public function toggleStatus($id)
    {
        abort_if(!hasPermission('update banner'), 403, __('You are not authorized to update banner'));
        return $this->repo->toggleStatus($id);
    }

    // Frontend API endpoints
    public function getBanners($position = null)
    {
        $banners = Banner::active()
            ->when($position, fn($q) => $q->byPosition($position))
            ->ordered()
            ->get();

        return response()->json($banners);
    }

    public function trackView($id)
    {
        return $this->repo->incrementViews($id);
    }

    public function trackClick($id)
    {
        return $this->repo->incrementClicks($id);
    }
}
```

---

## ✅ Form Request Validation

### File: `app/Http/Requests/Admin/Banner/StoreBannerRequest.php`

```php
<?php

namespace App\Http\Requests\Admin\Banner;

use Illuminate\Foundation\Http\FormRequest;

class StoreBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'link_url' => 'nullable|url|max:500',
            'link_text' => 'nullable|string|max:100',
            'open_in_new_tab' => 'nullable|boolean',
            'position' => 'required|string|max:100',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'status' => 'required|boolean',
            'priority' => 'nullable|integer|min:0|max:999',
            'target_pages' => 'nullable|array',
            'target_pages.*' => 'nullable|string',
            'target_categories' => 'nullable|array',
            'target_categories.*' => 'nullable|integer|exists:categories,id',
            'target_products' => 'nullable|array',
            'target_products.*' => 'nullable|integer',
            'show_on_all_pages' => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'open_in_new_tab' => $this->has('open_in_new_tab'),
            'show_on_all_pages' => $this->has('show_on_all_pages'),
            'status' => $this->has('status'),
        ]);
    }
}
```

### File: `app/Http/Requests/Admin/Banner/UpdateBannerRequest.php`

```php
<?php

namespace App\Http\Requests\Admin\Banner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'link_url' => 'nullable|url|max:500',
            'link_text' => 'nullable|string|max:100',
            'open_in_new_tab' => 'nullable|boolean',
            'position' => 'required|string|max:100',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'status' => 'required|boolean',
            'priority' => 'nullable|integer|min:0|max:999',
            'target_pages' => 'nullable|array',
            'target_pages.*' => 'nullable|string',
            'target_categories' => 'nullable|array',
            'target_categories.*' => 'nullable|integer|exists:categories,id',
            'target_products' => 'nullable|array',
            'target_products.*' => 'nullable|integer',
            'show_on_all_pages' => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'open_in_new_tab' => $this->has('open_in_new_tab'),
            'show_on_all_pages' => $this->has('show_on_all_pages'),
            'status' => $this->has('status'),
        ]);
    }
}
```

---

## 🛣️ Routes Configuration

### Add to `routes/admin.php`

```php
use App\Http\Controllers\Backend\Dashboards\Admin\BannerController;

// Banners Management Routes
Route::prefix('banners')->name('banners.')->group(function () {
    Route::get('/', [BannerController::class, 'index'])->name('index');
    Route::get('/data', [BannerController::class, 'data'])->name('data');
    Route::get('/create', [BannerController::class, 'create'])->name('create');
    Route::post('/store', [BannerController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [BannerController::class, 'edit'])->name('edit');
    Route::put('/{id}/update', [BannerController::class, 'update'])->name('update');
    Route::delete('/{id}/destroy', [BannerController::class, 'destroy'])->name('destroy');
    Route::get('/trash', [BannerController::class, 'trash'])->name('trash');
    Route::get('/trash/data', [BannerController::class, 'trashData'])->name('trash.data');
    Route::post('/{id}/restore', [BannerController::class, 'restore'])->name('restore');
    Route::delete('/{id}/force-delete', [BannerController::class, 'forceDelete'])->name('force-delete');
    Route::post('/{id}/toggle-status', [BannerController::class, 'toggleStatus'])->name('toggle-status');
});
```

### Add to `routes/web.php` (for frontend API)

```php
use App\Http\Controllers\Backend\Dashboards\Admin\BannerController;

// Public banner endpoints
Route::prefix('api/banners')->name('api.banners.')->group(function () {
    Route::get('/{position?}', [BannerController::class, 'getBanners'])->name('get');
    Route::post('/{id}/track-view', [BannerController::class, 'trackView'])->name('track-view');
    Route::post('/{id}/track-click', [BannerController::class, 'trackClick'])->name('track-click');
});
```

---

## 🎨 Views Implementation

### Index View: `resources/views/backend/dashboards/admin/pages/banners/index.blade.php`

```blade
@extends('backend.dashboards.admin.layouts.master')

@section('title', __('Banners Management'))

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h4 class="card-title mb-0">{{ __('Banners Management') }}</h4>
            </div>
            <div class="col-md-6 text-end">
                @hasPermission('create banner')
                <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('Add New Banner') }}
                </a>
                @endhasPermission
                <a href="{{ route('admin.banners.trash') }}" class="btn btn-secondary">
                    <i class="fas fa-trash"></i> {{ __('Trash') }}
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="banners-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>{{ __('Image') }}</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Position') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Start Date') }}</th>
                        <th>{{ __('End Date') }}</th>
                        <th>{{ __('Stats') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#banners-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.banners.data') }}",
            columns: [
                { data: 'image_preview', name: 'image_preview', orderable: false, searchable: false },
                { data: 'title', name: 'title' },
                { data: 'position', name: 'position' },
                { data: 'status', name: 'status' },
                { data: 'start_at', name: 'start_at' },
                { data: 'end_at', name: 'end_at' },
                { data: 'stats', name: 'stats', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[6, 'desc']] // Order by priority
        });

        // Delete banner
        $(document).on('click', '.delete-btn', function() {
            let id = $(this).data('id');
            if (confirm('{{ __("Are you sure you want to delete this banner?") }}')) {
                $.ajax({
                    url: "{{ url('admin/banners') }}/" + id + "/destroy",
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            $('#banners-table').DataTable().ajax.reload();
                            showNotification('success', response.message);
                        }
                    }
                });
            }
        });

        // Toggle status
        $(document).on('click', '.toggle-status-btn', function() {
            let id = $(this).data('id');
            $.ajax({
                url: "{{ url('admin/banners') }}/" + id + "/toggle-status",
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        $('#banners-table').DataTable().ajax.reload();
                        showNotification('success', response.message);
                    }
                }
            });
        });
    });
</script>
@endpush
```

### Create/Edit View: `resources/views/backend/dashboards/admin/pages/banners/create.blade.php`

```blade
@extends('backend.dashboards.admin.layouts.master')

@section('title', __('Create Banner'))

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">{{ __('Create New Banner') }}</h4>
    </div>
    <div class="card-body">
        <form id="banner-form" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <!-- Basic Information -->
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>{{ __('Basic Information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="title" class="form-label">{{ __('Title') }}</label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">{{ __('Description') }}</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">{{ __('Content') }} (HTML)</label>
                                <textarea class="form-control" id="content" name="content" rows="5">{{ old('content') }}</textarea>
                                <small class="text-muted">{{ __('You can use HTML tags for rich content') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Media -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>{{ __('Media') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="image" class="form-label">{{ __('Banner Image') }} <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <small class="text-muted">{{ __('Recommended size: 1920x600px. Max size: 5MB') }}</small>
                                <div id="image-preview" class="mt-2"></div>
                            </div>

                            <div class="mb-3">
                                <label for="mobile_image" class="form-label">{{ __('Mobile Image') }} ({{ __('Optional') }})</label>
                                <input type="file" class="form-control" id="mobile_image" name="mobile_image" accept="image/*">
                                <small class="text-muted">{{ __('Recommended size: 768x400px. Max size: 5MB') }}</small>
                                <div id="mobile-image-preview" class="mt-2"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Link Settings -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>{{ __('Link Settings') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="link_url" class="form-label">{{ __('Link URL') }}</label>
                                <input type="url" class="form-control" id="link_url" name="link_url" value="{{ old('link_url') }}" placeholder="https://example.com">
                            </div>

                            <div class="mb-3">
                                <label for="link_text" class="form-label">{{ __('Link Text') }}</label>
                                <input type="text" class="form-control" id="link_text" name="link_text" value="{{ old('link_text') }}" placeholder="{{ __('Click Here') }}">
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="open_in_new_tab" name="open_in_new_tab" value="1">
                                <label class="form-check-label" for="open_in_new_tab">
                                    {{ __('Open link in new tab') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Settings -->
                <div class="col-md-4">
                    <!-- Display Settings -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>{{ __('Display Settings') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="position" class="form-label">{{ __('Position') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="position" name="position" value="{{ old('position', 'homepage_banner') }}" required placeholder="e.g., homepage_banner, header, sidebar, footer">
                                <small class="text-muted">{{ __('Enter any position identifier (e.g., homepage_banner, header, sidebar, footer, custom_position_1)') }}</small>
                            </div>

                            <div class="mb-3">
                                <label for="priority" class="form-label">{{ __('Priority') }}</label>
                                <input type="number" class="form-control" id="priority" name="priority" value="{{ old('priority', 0) }}" min="0" max="999">
                                <small class="text-muted">{{ __('Higher number = displayed first') }}</small>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="status" name="status" value="1" checked>
                                <label class="form-check-label" for="status">
                                    {{ __('Active') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Scheduling -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>{{ __('Scheduling') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="start_at" class="form-label">{{ __('Start Date/Time') }}</label>
                                <input type="datetime-local" class="form-control" id="start_at" name="start_at" value="{{ old('start_at') }}">
                            </div>

                            <div class="mb-3">
                                <label for="end_at" class="form-label">{{ __('End Date/Time') }}</label>
                                <input type="datetime-local" class="form-control" id="end_at" name="end_at" value="{{ old('end_at') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Targeting -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>{{ __('Targeting') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="show_on_all_pages" name="show_on_all_pages" value="1">
                                <label class="form-check-label" for="show_on_all_pages">
                                    {{ __('Show on all pages') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">{{ __('Create Banner') }}</button>
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Image preview
        $('#image').on('change', function(e) {
            let file = e.target.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#image-preview').html('<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 300px;">');
                };
                reader.readAsDataURL(file);
            }
        });

        // Mobile image preview
        $('#mobile_image').on('change', function(e) {
            let file = e.target.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#mobile-image-preview').html('<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 300px;">');
                };
                reader.readAsDataURL(file);
            }
        });

        // Form submission
        $('#banner-form').on('submit', function(e) {
            e.preventDefault();
            
            let formData = new FormData(this);
            
            $.ajax({
                url: "{{ route('admin.banners.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        showNotification('success', response.message);
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        showValidationErrors(errors);
                    } else {
                        showNotification('error', xhr.responseJSON.message || '{{ __("An error occurred") }}');
                    }
                }
            });
        });
    });
</script>
@endpush
```

### Edit View: `resources/views/backend/dashboards/admin/pages/banners/edit.blade.php`

Similar to create view, but with the following differences:
- Populate form fields with existing banner data: `{{ $banner->title }}`, `{{ $banner->position }}`, etc.
- Show existing images if available:
  ```blade
  @if($banner->image)
      <div class="mb-2">
          <img src="{{ $banner->image }}" class="img-thumbnail" style="max-width: 200px;">
          <small class="text-muted">{{ __('Current image') }}</small>
      </div>
  @endif
  ```
- Use update route: `{{ route('admin.banners.update', $banner->id) }}`
- Change form method to PUT: `<input type="hidden" name="_method" value="PUT">`
- Remove `required` attribute from image field (since it's optional on update)

---

## 🌐 Frontend Display

### Blade Component: `resources/views/components/banner.blade.php`

```blade
@props(['position' => 'homepage_banner', 'limit' => null])

@php
    $banners = \App\Models\Banner::active()
        ->byPosition($position)
        ->ordered()
        ->when($limit, fn($q) => $q->limit($limit))
        ->get();
@endphp

@if($banners->count() > 0)
    <div class="banners-container banners-{{ $position }}" data-position="{{ $position }}">
        @foreach($banners as $banner)
            <div class="banner-item" data-banner-id="{{ $banner->id }}">
                @if($banner->link_url)
                    <a href="{{ $banner->link_url }}" 
                       class="banner-link" 
                       data-banner-id="{{ $banner->id }}"
                       @if($banner->open_in_new_tab) target="_blank" rel="noopener" @endif>
                @endif

                @if($banner->image)
                    <picture>
                        @if($banner->mobile_image)
                            <source media="(max-width: 768px)" srcset="{{ $banner->mobile_image }}">
                        @endif
                        <img src="{{ $banner->image }}" 
                             alt="{{ $banner->title ?? '' }}" 
                             class="banner-image img-fluid"
                             loading="lazy">
                    </picture>
                @endif

                @if($banner->title || $banner->description || $banner->content)
                    <div class="banner-content">
                        @if($banner->title)
                            <h3 class="banner-title">{{ $banner->title }}</h3>
                        @endif
                        @if($banner->description)
                            <p class="banner-description">{{ $banner->description }}</p>
                        @endif
                        @if($banner->content)
                            <div class="banner-html-content">{!! $banner->content !!}</div>
                        @endif
                        @if($banner->link_text && $banner->link_url)
                            <span class="banner-cta">{{ $banner->link_text }}</span>
                        @endif
                    </div>
                @endif

                @if($banner->link_url)
                    </a>
                @endif
            </div>
        @endforeach
    </div>

    @push('scripts')
    <script>
        // Track banner views
        document.querySelectorAll('.banner-item').forEach(function(item) {
            let bannerId = item.dataset.bannerId;
            if (bannerId) {
                fetch("{{ url('api/banners') }}/" + bannerId + "/track-view", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });
            }
        });

        // Track banner clicks
        document.querySelectorAll('.banner-link').forEach(function(link) {
            link.addEventListener('click', function() {
                let bannerId = this.dataset.bannerId;
                if (bannerId) {
                    fetch("{{ url('api/banners') }}/" + bannerId + "/track-click", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    });
                }
            });
        });
    </script>
    @endpush
@endif
```

### Usage in Frontend Views

```blade
{{-- In homepage --}}
<x-banner position="homepage_hero" />

{{-- In sidebar --}}
<x-banner position="sidebar" limit="3" />

{{-- In footer --}}
<x-banner position="footer" />
```

---

## 🔐 Permissions Setup

### Add to Seeder: `database/seeders/Guards/AdminRolePermissionSeeder.php`

```php
// Add these permissions
'view banners',
'create banner',
'update banner',
'delete banner',
```

### Example Permission Assignment

```php
$adminRole->givePermissionTo([
    'view banners',
    'create banner',
    'update banner',
    'delete banner',
]);
```

---

## 🔧 Service Provider Registration

### Add to `app/Providers/RepositoryServiceProvider.php`

```php
$this->app->bind('App\Interfaces\Admin\BannerRepositoryInterface', 'App\Repository\Admin\BannerRepository');
```

---

## ✨ Additional Features

### 1. Banner Slider/Carousel
- Use a carousel library (Swiper, Slick, etc.) for multiple banners
- Auto-play, navigation arrows, pagination dots

### 2. A/B Testing
- Add `variant` field to test different banner versions
- Track performance per variant

### 3. Banner Templates
- Pre-defined templates for common banner types
- Template selection in create/edit form

### 4. Analytics Dashboard
- View detailed analytics per banner
- Export reports (CSV/Excel)
- Charts for views/clicks over time

### 5. Banner Scheduling
- Calendar view for scheduled banners
- Email notifications for upcoming expirations

### 6. Multi-language Support
- Add `title_en`, `title_ar`, `description_en`, `description_ar` fields
- Use Laravel Localization package

### 7. Banner Zones
- Define custom zones in admin
- Assign banners to specific zones
- Zone-based display logic

### 8. Responsive Image Sizes
- Generate multiple image sizes (thumbnail, medium, large)
- Use Laravel Intervention Image or similar

### 9. Banner Rotation
- Random rotation of banners in same position
- Weighted rotation based on priority

### 10. Integration with Analytics
- Google Analytics event tracking
- Facebook Pixel tracking
- Custom analytics endpoints

---

## 📝 Notes

- **Spatie Media Library**: Images are stored using Spatie Media Library. The `media` table (created by Spatie) handles all image storage.
- **Dynamic Positions**: Position field accepts any string value, allowing flexible positioning without enum restrictions.
- **Image Collections**: 
  - `banner_image` - Desktop/main banner image
  - `banner_mobile_image` - Mobile-specific banner image
- **Guest Click Tracking**: 
  - For guests, `clickable_type` and `clickable_id` are stored as `null` (Laravel supports null in polymorphic relationships)
  - Alternative: Use `'guest'` as `clickable_type` and `null` as `clickable_id` if you want to explicitly identify guests
  - Use scopes `fromUsers()` and `fromGuests()` to filter clicks by user type
- Consider image optimization for better performance
- Implement caching for frequently accessed banners
- Add rate limiting for tracking endpoints
- Consider using queues for analytics processing
- Add validation for image dimensions if specific sizes are required
- Implement soft delete cleanup job for old banners

### Guest Click Tracking Examples

```php
// Get all clicks from authenticated users
$userClicks = BannerClick::fromUsers()->get();

// Get all clicks from guests
$guestClicks = BannerClick::fromGuests()->get();

// Get clicks for a specific banner
$banner = Banner::find(1);
$allClicks = $banner->clicks;
$userClicks = $banner->clicks()->fromUsers()->get();
$guestClicks = $banner->clicks()->fromGuests()->get();

// Count clicks by type
$totalClicks = $banner->clicks()->count();
$userClicksCount = $banner->clicks()->fromUsers()->count();
$guestClicksCount = $banner->clicks()->fromGuests()->count();
```

---

## 🚀 Implementation Checklist

- [ ] Create migration file
- [ ] Create Banner model
- [ ] Create BannerClick model (optional)
- [ ] Create repository interface
- [ ] Create repository implementation
- [ ] Create form request validators
- [ ] Create controller
- [ ] Add routes
- [ ] Create admin views (index, create, edit, trash)
- [ ] Create frontend banner component
- [ ] Register repository in service provider
- [ ] Add permissions to seeder
- [ ] Add sidebar menu item
- [ ] Test CRUD operations
- [ ] Test image uploads
- [ ] Test scheduling
- [ ] Test frontend display
- [ ] Test analytics tracking
- [ ] Add translations (if multi-language)
- [ ] Write tests (optional)

---

**End of Implementation Guide**

