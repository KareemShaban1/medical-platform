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
                                        <h4 class="page-title">{{ __('Trashed Products') }}</h4>
                              </div>
                    </div>
          </div>

          <div class="row">
                    <div class="col-12">
                              <div class="card">
                                        <div class="card-body">
                                                  <table id="products-trash-table" class="table dt-responsive nowrap w-100">
                                                            <thead>
                                                                      <tr>
                                                                                <th>{{ __('ID') }}</th>
                                                                                <th>{{ __('Images') }}</th>
                                                                                <th>{{ __('Name') }}</th>
                                                                                <th>{{ __('SKU') }}</th>
                                                                                <th>{{ __('Price Before') }}</th>
                                                                                <th>{{ __('Price After') }}</th>
                                                                                <th>{{ __('Stock') }}</th>
                                                                                <th>{{ __('Status') }}</th>
                                                                                <th>{{ __('Deleted At') }}</th>
                                                                                <th>{{ __('Actions') }}</th>
                                                                      </tr>
                                                            </thead>
                                                  </table>
                                        </div>
                              </div>
                    </div>
          </div>
</div>
@endsection

@push('scripts')
<script>
let trashTable = $('#products-trash-table').DataTable({
          ajax: '{{ route("supplier.products.trash.data") }}',
          columns: [{
                              data: 'id',
                              name: 'id'
                    },
                    {
                              data: 'image',
                              name: 'image',
                              orderable: false,
                              searchable: false
                    },
                    {
                              data: 'name',
                              name: 'name'
                    },
                    {
                              data: 'sku',
                              name: 'sku'
                    },
                    {
                              data: 'price_before',
                              name: 'price_before'
                    },
                    {
                              data: 'price_after',
                              name: 'price_after'
                    },
                    {
                              data: 'stock',
                              name: 'stock'
                    },
                    {
                              data: 'status',
                              name: 'status'
                    },
                    {
                              data: 'deleted_at',
                              name: 'deleted_at'
                    },
                    {
                              data: 'action',
                              name: 'action',
                              orderable: false,
                              searchable: false
                    },

          ],
          order: [
                    [0, 'desc']
          ],
          dom: '<"d-flex justify-content-between align-items-center mb-3"lfB>rtip',
          pageLength: 10,
          responsive: true,
          language: languages[language],
          buttons: [{
                              extend: 'print',
                              exportOptions: {
                                        columns: [0, 2, 3, 4, 5, 6, 7]
                              }
                    },
                    {
                              extend: 'excel',
                              text: 'Excel',
                              title: 'Trashed Products Data',
                              exportOptions: {
                                        columns: [0, 2, 3, 4, 5, 6, 7]
                              }
                    },
                    {
                              extend: 'copy',
                              exportOptions: {
                                        columns: [0, 2, 3, 4, 5, 6, 7]
                              }
                    },
          ],
          drawCallback: function() {
                    $('.dataTables_paginate > .pagination').addClass(
                              'pagination-rounded');
          }
});

// Restore Product
function restoreProduct(id) {
          Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to restore this product?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, restore it!'
          }).then((result) => {
                    if (result.isConfirmed) {
                              $.ajax({
                                        url: '{{ route("supplier.products.restore", ":id") }}'.replace(':id', id),
                                        method: 'POST',
                                        headers: {
                                                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        success: function(response) {
                                                  trashTable.ajax.reload();
                                                  Swal.fire('Restored!', response.message, 'success');
                                        }
                              });
                    }
          });
}

// Force Delete Product
function forceDeleteProduct(id) {
          Swal.fire({
                    title: 'Are you sure?',
                    text: "This will permanently delete the product. You won't be able to revert this!",
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete permanently!'
          }).then((result) => {
                    if (result.isConfirmed) {
                              $.ajax({
                                        url: '{{ route("supplier.products.force.delete", ":id") }}'.replace(':id', id),
                                        method: 'DELETE',
                                        headers: {
                                                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        success: function(response) {
                                                  trashTable.ajax.reload();
                                                  Swal.fire('Deleted!', response.message, 'success');
                                        }
                              });
                    }
          });
}
</script>
@endpush
