@extends('backend.dashboards.admin.layouts.app')
@section('title' , __('Blog Categories'))
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#blogCategoriesModal" onclick="resetForm()">
                        <i class="mdi mdi-plus"></i> {{ __('Add Blog Category') }}
                    </button>
                </div>
                <h4 class="page-title">{{ __('Blog Categories') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="blog-categories-table" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Name English') }}</th>
                                <th>{{ __('Name Arabic') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="blogCategoriesModal" tabindex="-1" role="dialog" aria-labelledby="blogCategoriesModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="blogCategoriesModalLabel">{{ __('Add Blog Category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="blogCategoriesForm" method="POST">
                    @csrf
                    <input type="hidden" id="blogCategoriesId">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="name_ar"
                                class="form-label">{{ __('Name Arabic') }}</label>
                            <input type="text" class="form-control"
                                id="name_ar" name="name_ar">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <label for="name_en"
                                class="form-label">{{ __('Name English') }}</label>
                            <input type="text" class="form-control"
                                id="name_en" name="name_en">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 col-md-6 mb-3">
                            <div class="form-check form-switch mt-4">
                                <input type="hidden" name="status"
                                    id="statusHidden" value="0">
                                <input type="checkbox"
                                    class="form-check-input"
                                    id="statusToggle" value="1">
                                <label class="form-check-label"
                                    for="statusToggle">{{ __('Status') }}</label>

                            </div>
                        </div>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit"
                            class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@include('backend.dashboards.admin.pages.blog-categories.scripts.index-scripts')
