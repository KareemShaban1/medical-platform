<?php

namespace App\Repository\Supplier;

use App\Interfaces\Supplier\ProductRepositoryInterface;
use App\Models\Admin;
use App\Models\Product;
use App\Models\ModuleApprovement;
use App\Notifications\Admin\NewProductSubmittedNotification;
use App\Notifications\Admin\ProductUpdatedForReviewNotification;
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
            ->editColumn('price_before', fn($item) => __('EGP') . ' ' . number_format($item->price_before, 2))
            ->editColumn('price_after', fn($item) => __('EGP') . ' ' . number_format($item->price_after, 2))
            ->addColumn('final_price', fn($item) => __('EGP') . ' ' . number_format($item->final_price ?? $item->price_after, 2))
            ->editColumn('status', fn($item) => $this->productStatus($item))
            // ->editColumn('approved', fn($item) => $this->productApproved($item))
            ->addColumn('approval_status', fn($item) => $this->productApprovalStatus($item))
            ->addColumn('categories', fn($item) => $this->productCategories($item))
            ->addColumn('action', fn($item) => $this->productActions($item))
            ->rawColumns(['images', 'status', 'approval_status', 'categories', 'action'])
            ->make(true);
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request;
            $data['supplier_id'] = auth('supplier')->user()->supplier_id;
            $data['final_price'] = Product::calculateFinalPrice((float) $data['price_after']);

            $product = Product::create($data);
            $product->categories()->sync($data['categories']);

            if (!empty($data['attachment'])) {
                foreach ($data['attachment'] as $attachment) {
                    $product->addMedia($attachment)->toMediaCollection('product_images');
                }
            }

            $adminId = Admin::query()->value('id');
            if ($adminId) {
                ModuleApprovement::create([
                    'module_type' => Product::class,
                    'module_id' => $product->id,
                    'action' => 'under_review',
                    'action_by' => $adminId,
                    'notes' => 'New product submitted for review'
                ]);
            }

            $adminUsers = Admin::all();
            foreach ($adminUsers as $admin) {
                $admin->notify(new NewProductSubmittedNotification($product));
            }

            return $product;
        });
    }

    public function show($id)
    {
        return Product::with(['categories', 'supplier'])->mine()->findOrFail($id);
    }

    public function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $product = Product::findOrFail($id);
            $data = $request;
            $data['final_price'] = Product::calculateFinalPrice((float) $data['price_after']);
            $product->update($data);
            $product->categories()->sync($data['categories']);

            if (isset($data['removed_images']) && !empty($data['removed_images'])) {
                foreach ($data['removed_images'] as $attachmentId) {
                    $product->deleteMedia($attachmentId);
                }
            }
            if ($product->approvement) {
                $product->approvement->update([
                    'action' => 'under_review',
                    'action_by' => Admin::query()->value('id'),
                    'notes' => 'Product updated for review'
                ]);
                $adminUsers = Admin::all();
                foreach ($adminUsers as $admin) {
                    $admin->notify(new ProductUpdatedForReviewNotification($product));
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
            $product = Product::mine()->findOrFail($id);
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
            ->editColumn('price_before', fn($item) => __('EGP') . ' ' . number_format($item->price_before, 2))
            ->editColumn('price_after', fn($item) => __('EGP') . ' ' . number_format($item->price_after, 2))
            ->addColumn('final_price', fn($item) => __('EGP') . ' ' . number_format($item->final_price ?? $item->price_after, 2))
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


    private function productApprovalStatus($item): string
    {
        $approval = $item->approvement;
        if (!$approval) {
            return '<span class="badge bg-secondary">No Record</span>';
        }

        $badges = [
            'pending' => 'warning',
            'under_review' => 'info',
            'approved' => 'success',
            'rejected' => 'danger'
        ];

        $class = $badges[$approval->action] ?? 'secondary';
        $notes = $approval->notes ? '<br><i class="fa fa-info-circle"></i> ' . $approval->notes : '';
        return '<span class="badge bg-' . $class . '">' . ucfirst(str_replace('_', ' ', $approval->action)) . '</span> ' . $notes;
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
        $html = '<div class="d-flex gap-2">';

        if (hasPermission('view products')) {
            $showUrl = route('supplier.products.show', $item->id);
            $html .= '<a href="' . $showUrl . '" class="btn btn-sm btn-success" title="View"><i class="fa fa-eye"></i></a>';
        }

        if (hasPermission('update product')) {
            $html .= '<button onclick="editProduct(' . $item->id . ')" class="btn btn-sm btn-info" title="Edit"><i class="fa fa-edit"></i></button>';
        }

        if (hasPermission('delete product')) {
            $html .= '<button onclick="deleteProduct(' . $item->id . ')" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>';
        }

        $html .= '</div>';

        return $html;
    }

    private function trashActions($item): string
    {
        $html = '<div class="d-flex gap-2">';

        if (hasPermission('restore product')) {
            $html .= '<button class="btn btn-sm btn-success" onclick="restoreProduct(' . $item->id . ')">';
            $html .= '<i class="mdi mdi-restore"></i> Restore';
            $html .= '</button>';
        }

        if (hasPermission('force delete product')) {
            $html .= '<button class="btn btn-sm btn-danger" onclick="forceDeleteProduct(' . $item->id . ')">';
            $html .= '<i class="mdi mdi-delete-forever"></i> Delete';
            $html .= '</button>';
        }

        $html .= '</div>';

        return $html;
    }
}
