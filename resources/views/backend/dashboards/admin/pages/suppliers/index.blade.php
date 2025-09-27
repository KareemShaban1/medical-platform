@extends('backend.dashboards.admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#suppliersModal"
                            onclick="resetForm()">
                            <i class="mdi mdi-plus"></i> {{ __('Add Supplier') }}
                        </button>
                    </div>
                    <h4 class="page-title">{{ __('Suppliers') }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="suppliers-table" class="table dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Is Allowed') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Users') }}</th>
                                    <th>{{ __('Approval') }}</th>
                                    <th>{{ __('Attachments') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="suppliersModal" tabindex="-1" role="dialog" aria-labelledby="suppliersModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="suppliersModalLabel">{{ __('Add Supplier') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="suppliersForm" method="POST">
                        @csrf
                        <input type="hidden" id="supplierId">
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="name" class="form-label">{{ __('Name') }}</label>
                                <input type="text" class="form-control" id="name" name="name">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-12 col-md-6 mb-3">
                                <label for="phone" class="form-label">{{ __('Phone') }}</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="address" class="form-label">{{ __('Address') }}</label>
                                <textarea name="address" id="address" class="form-control"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12 col-md-3 mb-3">
                                <div class="form-check form-switch mt-4">
                                    <input type="hidden" name="is_allowed" id="isAllowedHidden" value="0">
                                    <input type="checkbox" class="form-check-input" id="isAllowedToggle" value="1">
                                    <label class="form-check-label" for="isAllowedToggle">{{ __('Is Allowed') }}</label>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-12 col-md-3 mb-3">
                                <div class="form-check form-switch mt-4">
                                    <input type="hidden" name="status" id="statusHidden" value="0">
                                    <input type="checkbox" class="form-check-input" id="statusToggle" value="1">
                                    <label class="form-check-label" for="statusToggle">{{ __('Status') }}</label>

                                </div>
                            </div>

                            <div class="col-12 col-md-12 mb-3">
                                <label for="images" class="form-label">{{ __('Images') }}</label>
                                <input type="file" class="form-control" id="images" name="images[]" multiple>

                                <div id="imagesPreview" class="mt-2 d-flex flex-wrap gap-2"></div>

                                <div class="invalid-feedback"></div>

                            </div>
                        </div>


                        <div class="modal-footer">
                            <button type="button" class="btn btn-light"
                                data-bs-dismiss="modal">{{ __('Close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>


    <!-- show Modal -->
    <div class="modal fade" id="showSupplierModal" tabindex="-1" role="dialog"
        aria-labelledby="showSupplierModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showSupplierModalLabel">{{ __('Show Supplier') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <span>{{ __('Name') }} :</span> <span id="showName"></span>

                    </div>
                    <div>
                        <span>{{ __('Phone') }} :</span> <span id="showPhone"></span>
                    </div>
                    <div>
                        <span>{{ __('Address') }} :</span> <span id="showAddress"></span>
                    </div>
                    <div>
                        <span>{{ __('Is Allowed') }} :</span> <span id="showIsAllowed"></span>
                    </div>
                    <div>
                        <span>{{ __('Status') }} :</span> <span id="showStatus"></span>
                    </div>
                    <div>
                        <span>{{ __('Users') }} :</span> <span id="showUsers"></span>
                    </div>

                    <!-- Images -->
                    <div>
                        <span>{{ __('Images') }} :</span>
                        <div id="showImages"></div>
                    </div>


                </div>
            </div>
        </div>
    </div>

    @include('backend.dashboards.admin.pages.suppliers.approval-modal')
    </div>
@endsection


@include('backend.dashboards.admin.pages.suppliers.scripts')
