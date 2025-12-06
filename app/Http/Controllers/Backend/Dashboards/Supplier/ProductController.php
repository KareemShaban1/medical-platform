<?php

namespace App\Http\Controllers\Backend\Dashboards\Supplier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\Product\ProductStoreRequest;
use App\Http\Requests\Supplier\Product\ProductUpdateRequest;
use App\Interfaces\Supplier\ProductRepositoryInterface;
use App\Traits\HandlesFeatureLimits;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    use HandlesFeatureLimits;

    protected $productRepo;

    public function __construct(ProductRepositoryInterface $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view products'), 403, __('You are not authorized to view products'));

        return view('backend.dashboards.supplier.pages.products.index');
    }

    public function data()
    {
        return $this->productRepo->data();
    }

    public function store(ProductStoreRequest $request)
    {
        // apply permissions
        abort_if(!hasPermission('create product'), 403, __('You are not authorized to create products'));

        $supplier = Auth::guard('supplier')->user()->supplier;

        return $this->checkFeatureLimit(
            $supplier,
            'max_products',
            function() use ($request) {
                $product = $this->productRepo->store($request->validated());
                return $this->jsonResponse('success', message: __('Product created successfully'));
            }
        );
    }


    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view product'), 403, __('You are not authorized to view product'));

        $product = $this->productRepo->show($id);
        return request()->ajax()
        ? response()->json($product->load('categories'))
        : view('backend.dashboards.supplier.pages.products.show', compact('product'));
    }


    public function update(ProductUpdateRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update product'), 403, __('You are not authorized to update product'));

        $this->productRepo->update($request->validated(), $id);
        return $this->jsonResponse('success', __('Product updated successfully'));
    }


    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete product'), 403, __('You are not authorized to delete product'));

            $this->productRepo->destroy($id);
            return $this->jsonResponse('success', __('Product deleted successfully'));
    }

    public function toggleStatus($id)
    {
        $product = Product::mine()->findOrFail($id);
        $product->update(['status' => !$product->status]);

        return $this->jsonResponse('success', __('Product status updated successfully'));
    }

    public function trash()
    {
         // apply permissions
         abort_if(!hasPermission('view trash products'), 403, __('You are not authorized to view trash products'));

        
        return view('backend.dashboards.supplier.pages.products.trash');
    }

    public function trashData()
    {
        // apply permissions
        abort_if(!hasPermission('view trash products'), 403, __('You are not authorized to view trash products'));

        return $this->productRepo->trashData();
    }

    public function restore($id)
    {
        // apply permissions
        abort_if(!hasPermission('restore product'), 403, __('You are not authorized to restore product'));

        $this->productRepo->restore($id);
        return $this->jsonResponse('success', __('Product restored successfully'));
    }

    public function forceDelete($id)
    {
        // apply permissions
        abort_if(!hasPermission('force delete product'), 403, __('You are not authorized to force delete product'));

        $this->productRepo->forceDelete($id);
        return $this->jsonResponse('success', __('Product permanently deleted successfully'));
    }

    public function categories()
    {
        $categories = Category::select('id', 'name_en', 'name_ar')->active()->get();
        return response()->json($categories);
    }

    private function jsonResponse(string $status, string $message)
    {
        if (request()->ajax()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }


}
