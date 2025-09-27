<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\SupplierProductRepositoryInterface;
use App\Models\Product;
use App\Models\Supplier;
use App\Notifications\Supplier\ProductApprovalStatusNotification;
use Illuminate\Support\Facades\DB;

class SupplierProductRepository implements SupplierProductRepositoryInterface
{
    public function data()
    {
        $products = Product::with(['supplier', 'categories', 'approvement']);

        return datatables($products)
            ->addColumn('supplier_name', function ($product) {
                return $product->supplier ? $product->supplier->name : 'N/A';
            })
            ->addColumn('product_name', function ($product) {
                return $product->name;
            })
            ->addColumn('image', function ($product) {
                $firstImage = $product->first_image;
                if ($firstImage) {
                    return '<img src="' . $firstImage . '" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">';
                }
                return '<span class="badge bg-secondary">No Image</span>';
            })
            ->addColumn('categories', function ($product) {
                if ($product->categories && $product->categories->count() > 0) {
                    $badges = $product->categories->take(2)->map(function ($cat) {
                        return '<span class="badge bg-primary me-1">' . e($cat->name) . '</span>';
                    })->implode(' ');

                    if ($product->categories->count() > 2) {
                        $badges .= '<span class="badge bg-info">+' . ($product->categories->count() - 2) . '</span>';
                    }

                    return $badges;
                }
                return '<span class="badge bg-secondary">No Categories</span>';
            })
            ->addColumn('approval_status', function ($product) {
                $approval = $product->approvement;
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
                return '<span class="badge bg-' . $class . '">' . ucfirst(str_replace('_', ' ', $approval->action)) . '</span>';
            })
            ->addColumn('price', function ($product) {
                return '$' . number_format($product->price_after ?? $product->price_before, 2);
            })
            ->addColumn('stock', function ($product) {
                $class = $product->stock > 10 ? 'success' : ($product->stock > 0 ? 'warning' : 'danger');
                return '<span class="badge bg-' . $class . '">' . $product->stock . '</span>';
            })
            ->addColumn('actions', function ($product) {
                return '
                    <div class="d-flex gap-2">
                        <a href="' . route('admin.supplier-products.show', $product->id) . '" class="btn btn-sm btn-info">
                            <i class="fa fa-eye"></i> View
                        </a>
                        <button onclick="updateApprovalStatus(' . $product->id . ')" class="btn btn-sm btn-primary">
                            <i class="fa fa-check"></i> Review
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['image', 'categories', 'approval_status', 'stock', 'actions'])
            ->make(true);

    }

    public function show($id)
    {
        return Product::with(['supplier', 'categories', 'approvement'])->findOrFail($id);
    }

    public function updateApprovalStatus(array $data, $id)
    {
        try {
            DB::beginTransaction();

            $product = Product::with(['approvement', 'supplier.supplierUsers'])->findOrFail($id);
            $approval = $product->approvement;

            if (!$approval) {
                return response()->json([
                    'success' => false,
                    'message' => 'No approval record found.'
                ], 404);
            }

            // Update approval
            $approval->update([
                'action' => $data['action'],
                'notes' => $data['notes'],
                'action_by' => auth()->id()
            ]);

            // Send notification to supplier users
            if ($product->supplier && $product->supplier->supplierUsers) {
                foreach ($product->supplier->supplierUsers as $user) {
                    $user->notify(new ProductApprovalStatusNotification(
                        $product,
                        $data['action'],
                        $data['notes']
                    ));
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product approval status updated successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status. Please try again.'
            ], 500);
        }
    }

    public function supplierProducts($supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $products = Product::with(['categories', 'approvement'])
            ->where('supplier_id', $supplierId)
            ->get();
        return compact('supplier', 'products');
    }



}
