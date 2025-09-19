<?php

namespace App\Http\Controllers\Backend\Dashboards\Supplier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\Product\ProductStoreRequest;
use App\Http\Requests\Supplier\Product\ProductUpdateRequest;
use App\Interfaces\Supplier\ProductRepositoryInterface;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    protected $productRepo;

    public function __construct(ProductRepositoryInterface $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    public function index()
    {
        return view('backend.dashboards.supplier.pages.products.index');
    }

    public function data()
    {
        return $this->productRepo->data();
    }

    public function store(ProductStoreRequest $request)
    {
        $this->productRepo->store($request->validated());
        return $this->jsonResponse('success', __('Product created successfully'));
    }


    public function show($id)
    {
        $product = $this->productRepo->show($id);
        return request()->ajax()
        ? response()->json($product->load('categories'))
        : view('backend.dashboards.supplier.pages.products.show', compact('product'));
    }


    public function update(ProductUpdateRequest $request, $id)
    {
        $this->productRepo->update($request->validated(), $id);
        return $this->jsonResponse('success', __('Product updated successfully'));
    }


    public function destroy($id)
    {
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
        return view('backend.dashboards.supplier.pages.products.trash');
    }

    public function trashData()
    {
        return $this->productRepo->trashData();
    }

    public function restore($id)
    {
        $this->productRepo->restore($id);
        return $this->jsonResponse('success', __('Product restored successfully'));
    }

    public function forceDelete($id)
    {
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
