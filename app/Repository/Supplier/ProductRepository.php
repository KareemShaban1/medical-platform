<?php

namespace App\Repository\Supplier;

use App\Interfaces\Supplier\ProductRepositoryInterface;
use App\Models\Attachment;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductRepository implements ProductRepositoryInterface
{

    public function index()
    {
        return [];
    }

    public function data()
    {
        $products = Product::with(['categories'])->mine();

        return datatables()->of($products)
            ->addColumn('images', fn($item) => $this->productImage($item))
            ->addColumn('name', fn($item) => $item->name)
            ->editColumn('status', fn($item) => $this->productStatus($item))
            ->editColumn('approved', fn($item) => $this->productApproved($item))
            ->addColumn('categories', fn($item) => $this->productCategories($item))
            ->addColumn('action', fn($item) => $this->productActions($item))
            ->rawColumns(['images', 'status', 'approved', 'categories', 'action'])
            ->make(true);
    }

    public function store($request)
    {
        DB::transaction(function () use ($request) {
            $data = $request;
            $data['supplier_id'] = auth('supplier')->id();

            $product = Product::create($data);
            $product->categories()->sync($data['categories']);

            if (!empty($data['attachment'])) {
                foreach ($data['attachment'] as $attachment) {
                    $product->addMedia($attachment)->toMediaCollection('product_images');
                }
            }

            return $product;
        });
    }

    public function show($id)
    {
        return Product::with(['categories','supplier'])->findOrFail($id);
    }

    public function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $product = Product::findOrFail($id);
            $data = $request;
            $product->update($data);
            $product->categories()->sync($data['categories']);

            if (isset($data['removed_images']) && !empty($data['removed_images'])) {
                foreach ($data['removed_images'] as $attachmentId) {
                    $product->deleteMedia($attachmentId);
                }
            }

            if (!empty($data['attachment'])) {
                $productAttachments = $product->getMedia('product_images')->pluck('id')->toArray();
                $newAttachments = array_diff($data['attachment'], $productAttachments);
                foreach ($newAttachments as $attachment) {
                    $product->addMedia($attachment)->toMediaCollection('product_images');
                }
            }

            return $product;
        });
    }


    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $product = Product::findOrFail($id);
            $product->delete();

            return $product;
        });
    }

    public function trash()
    {
        return [];
    }

    public function trashData()
    {
        $products = Product::onlyTrashed()->with(['categories'])->mine();

        return datatables()->of($products)
            ->addColumn('image', fn($item) => $this->productImage($item))
            ->addColumn('name', fn($item) => $item->name)
            ->addColumn('status', fn() => '<span class="badge bg-secondary">Trashed</span>')
            ->editColumn('deleted_at', fn($item) => $item->deleted_at->format('Y-m-d H:i:s'))
            ->addColumn('action', fn($item) => $this->trashActions($item))
            ->rawColumns(['image', 'status', 'action'])
            ->make(true);
    }

    public function restore($id)
    {
        $category = Product::onlyTrashed()->findOrFail($id);
        $category->restore();
    }

    public function forceDelete($id)
    {
        $product = Product::onlyTrashed()->mine()->findOrFail($id);

        return DB::transaction(function () use ($product) {
            $product->categories()->detach();
            $product->detachAllFiles();
            return $product->forceDelete();
        });
    }

    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function productImage($item): string
    {
        $firstImage = $item->first_image;
        if ($firstImage) {
            $imageUrl = $firstImage;
            return '<img src="' . $imageUrl . '" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">';
        }
        return '<span class="badge bg-secondary">No Image</span>';
    }

    private function productStatus($item): string
    {
        $checked = $item->status ? 'checked' : '';
        $statusText = $item->status ? 'Active' : 'Inactive';
        $statusClass = $item->status ? 'success' : 'danger';

        return <<<HTML
            <div class="form-check form-switch mt-2">
                <input type="checkbox" class="form-check-input toggle-status"
                       data-id="{$item->id}" id="status-{$item->id}"
                       {$checked}>
                <label class="form-check-label" for="status-{$item->id}">
                    <span class="badge bg-{$statusClass}">{$statusText}</span>
                </label>
            </div>
        HTML;
    }

    private function productApproved($item): string
    {
        if ($item->approved) {
            return '<span class="badge bg-success">Approved</span>';
        } else {
            return '<span class="badge bg-warning">Pending</span>';
        }
    }

    private function productCategories($item): string
    {
        if ($item->categories && $item->categories->count() > 0) {
            $badges = $item->categories->map(function ($cat) {
                return '<span class="badge bg-primary me-1 mb-1">' . e($cat->name) . '</span>';
            })->implode(' ');

            return '<div class="d-flex flex-wrap">' . $badges . '</div>';
        }

        return '<span class="badge bg-secondary">No Categories</span>';
    }

    private function productActions($item): string
    {
        $showUrl = route('supplier.products.show', $item->id);
        return <<<HTML
        <div class="d-flex gap-2">
            <a href="{$showUrl}" class="btn btn-sm btn-success" title="View"><i class="fa fa-eye"></i></a>
            <button onclick="editProduct({$item->id})" class="btn btn-sm btn-info" title="Edit"><i class="fa fa-edit"></i></button>
            <button onclick="deleteProduct({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }

    private function trashActions($item): string
    {
        return <<<HTML
        <button class="btn btn-sm btn-success" onclick="restoreProduct({$item->id})">
            <i class="mdi mdi-restore"></i> Restore
        </button>
        <button class="btn btn-sm btn-danger" onclick="forceDeleteProduct({$item->id})">
            <i class="mdi mdi-delete-forever"></i> Delete
        </button>
        HTML;
    }
}
