@extends('backend.dashboards.supplier.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('supplier.products.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Products') }}
                    </a>
                </div>
                <h4 class="page-title">{{ __('Product Details') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5 class="mb-3">{{ __('Product Information') }}</h5>

                            <div class="table-responsive">
                                <table class="table table-borderless table-nowrap mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" style="width: 50%;">{{ __('Name') }}:</th>
                                            <td>{{ $product->name }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">{{ __('SKU') }}:</th>
                                            <td>{{ $product->sku }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">{{ __('Price Before') }}:</th>
                                            <td>${{ number_format($product->price_before, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">{{ __('Price After') }}:</th>
                                            <td>${{ number_format($product->price_after, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">{{ __('Discount Value') }}:</th>
                                            <td>${{ number_format($product->discount_value, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">{{ __('Stock') }}:</th>
                                            <td>{{ $product->stock }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">{{ __('Status') }}:</th>
                                            <td>
                                                @if($product->status)
                                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">{{ __('Approval Status') }}:</th>
                                            <td>
                                                @if($product->approved)
                                                    <span class="badge bg-success">{{ __('Approved') }}</span>
                                                @else
                                                    <span class="badge bg-warning">{{ __('Pending') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if(!$product->approved && $product->reason)
                                        <tr>
                                            <th scope="row">{{ __('Rejection Reason') }}:</th>
                                            <td class="text-danger">{{ $product->reason }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <h5 class="mb-3">{{ __('Description') }}</h5>

                            <div class="mb-3">
                                <h6>{{ __('Description') }}:</h6>
                                <p class="text-muted">{{ $product->description ?: __('No description available') }}</p>
                            </div>

                            <div class="mb-3">
                                <h6>{{ __('Categories') }}:</h6>
                                @if($product->categories && $product->categories->count() > 0)
                                    @foreach($product->categories as $category)
                                        <span class="badge bg-primary me-1">{{ $category->name }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">{{ __('No categories assigned') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Product Images') }}</h5>

                    @if($product->attachments && $product->attachments->count() > 0)
                        <div class="row">
                            @foreach($product->attachments as $attachment)
                                <div class="col-6 mb-3">
                                    <div class="position-relative">
                                        <img src="{{ asset('storage/' . $attachment->url) }}"
                                             alt="Product Image"
                                             class="img-fluid rounded"
                                             style="width: 100%; height: 150px; object-fit: cover;"
                                             data-bs-toggle="modal"
                                             data-bs-target="#imageModal{{ $loop->index }}"
                                             style="cursor: pointer;">
                                    </div>
                                </div>

                                <!-- Image Modal -->
                                <div class="modal fade" id="imageModal{{ $loop->index }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('Product Image') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="{{ asset('storage/' . $attachment->path) }}"
                                                     alt="Product Image"
                                                     class="img-fluid">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center">
                            <i class="mdi mdi-image-off display-4 text-muted"></i>
                            <p class="text-muted mt-2">{{ __('No images available') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Timestamps') }}</h5>

                    <div class="table-responsive">
                        <table class="table table-borderless table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row">{{ __('Created At') }}:</th>
                                    <td>{{ $product->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">{{ __('Updated At') }}:</th>
                                    <td>{{ $product->updated_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.img-fluid:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease-in-out;
}
</style>
@endpush
