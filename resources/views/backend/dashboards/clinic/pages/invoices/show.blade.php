@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="page-title">{{ __('Invoice') }} #{{ $invoice->id }}</h4>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-outline-primary" onclick="window.print()">
        <i class="mdi mdi-printer"></i> {{ __('Print') }}
      </button>
      <a href="{{ route('clinic.invoices.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
    </div>
  </div>

  <style>
    .print-only { display: none; }
    @media print {
      .leftside-menu, .navbar-custom, .footer, .menu-arrow, .side-nav, .page-title, .no-print, .btn { display: none !important; }
      .print-only { display: block !important; }
      .card, .card-body { border: none !important; box-shadow: none !important; }
      .container-fluid { width: 100% !important; margin: 0 !important; padding: 0 12px !important; }
      table { border-collapse: collapse !important; width: 100% !important; }
      th, td { border: 1px solid #e5e7eb !important; padding: 6px !important; }
    }
  </style>

  <div class="row">
    <div class="col-lg-8">
      <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">{{ __('Items') }}</h5></div>
        <div class="card-body">
          <table class="table table-sm align-middle">
            <thead>
              <tr>
                <th>{{ __('Description') }}</th>
                <th class="text-center" style="width:120px">{{ __('Qty') }}</th>
                <th class="text-end" style="width:160px">{{ __('Unit Price') }}</th>
                <th class="text-end" style="width:160px">{{ __('Total') }}</th>
                <th style="width:100px"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($invoice->items as $item)
                <tr>
                  <form method="POST" action="{{ route('clinic.invoices.items.update', [$invoice->id, $item->id]) }}">
                    @csrf
                    <td>
                      <div class="no-print"><input type="text" class="form-control form-control-sm" name="description" value="{{ $item->description }}" {{ $invoice->status === 'paid' ? 'readonly' : '' }}></div>
                      <div class="print-only">{{ $item->description }}</div>
                    </td>
                    <td class="text-center">
                      <div class="no-print"><input type="number" min="1" class="form-control form-control-sm text-center" name="quantity" value="{{ $item->quantity ?? 1 }}" {{ $invoice->status === 'paid' ? 'readonly' : '' }}></div>
                      <div class="print-only">{{ $item->quantity ?? 1 }}</div>
                    </td>
                    <td class="text-end">
                      <div class="no-print"><input type="number" step="0.01" min="0" class="form-control form-control-sm text-end" name="unit_price" value="{{ $item->unit_price }}" {{ $invoice->status === 'paid' ? 'readonly' : '' }}></div>
                      <div class="print-only">{{ number_format($item->unit_price, 2) }}</div>
                    </td>
                    <td class="text-end">{{ number_format($item->total, 2) }}</td>
                    <td class="text-end">
                      @if($invoice->status !== 'paid')
                        <button class="btn btn-sm btn-primary">{{ __('Save') }}</button>
                        <form method="POST" action="{{ route('clinic.invoices.items.delete', [$invoice->id, $item->id]) }}" style="display:inline-block">
                          @csrf
                          @method('DELETE')
                          <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('Remove item?') }}')">{{ __('Delete') }}</button>
                        </form>
                      @endif
                    </td>
                  </form>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-muted">{{ __('No items yet') }}</td></tr>
              @endforelse
            </tbody>
          </table>

          @if($invoice->status !== 'paid')
          <hr>
          <h6 class="mb-2">{{ __('Add Item') }}</h6>
          <form method="POST" action="{{ route('clinic.invoices.items.add', $invoice->id) }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-6">
              <label class="form-label">{{ __('Description') }}</label>
              <input type="text" name="description" class="form-control" required>
            </div>
            <div class="col-md-2">
              <label class="form-label">{{ __('Qty') }}</label>
              <input type="number" name="quantity" min="1" value="1" class="form-control">
            </div>
            <div class="col-md-2">
              <label class="form-label">{{ __('Unit Price') }}</label>
              <input type="number" name="unit_price" step="0.01" min="0" class="form-control" required>
            </div>
            <div class="col-md-2">
              <button class="btn btn-primary w-100">{{ __('Add') }}</button>
            </div>
          </form>
          @endif
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">{{ __('Summary') }}</h5></div>
        <div class="card-body">
          <div class="mb-2"><strong>{{ __('Patient') }}:</strong> {{ $invoice->patient?->user?->name ?? 'N/A' }}</div>
          <div class="mb-2"><strong>{{ __('Doctor') }}:</strong> {{ $invoice->doctorProfile?->name ?? 'N/A' }}</div>
          <div class="mb-2"><strong>{{ __('Status') }}:</strong> <span class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'unpaid' ? 'warning' : 'secondary') }}">{{ ucfirst($invoice->status) }}</span></div>
          <hr>
          <form method="POST" action="{{ route('clinic.invoices.update-header', $invoice->id) }}">
            @csrf
            <div class="mb-2">
              <label class="form-label">{{ __('Discount') }}</label>
              <input type="number" step="0.01" min="0" name="discount" class="form-control" value="{{ $invoice->discount }}" {{ $invoice->status === 'paid' ? 'readonly' : '' }}>
            </div>
            <div class="mb-2">
              <label class="form-label">{{ __('Tax') }}</label>
              <input type="number" step="0.01" min="0" name="tax" class="form-control" value="{{ $invoice->tax }}" {{ $invoice->status === 'paid' ? 'readonly' : '' }}>
            </div>
            <div class="mb-2">
              <label class="form-label">{{ __('Payment Method') }}</label>
              <select name="payment_method" class="form-select" {{ $invoice->status === 'paid' ? 'disabled' : '' }}>
                <option value="">{{ __('Select') }}</option>
                <option value="cash" {{ $invoice->payment_method === 'cash' ? 'selected' : '' }}>{{ __('Cash') }}</option>
                <option value="card" {{ $invoice->payment_method === 'card' ? 'selected' : '' }}>{{ __('Card') }}</option>
                <option value="insurance" {{ $invoice->payment_method === 'insurance' ? 'selected' : '' }}>{{ __('Insurance') }}</option>
              </select>
            </div>
            @if($invoice->status !== 'paid')
              <button class="btn btn-info text-white w-100 mb-2">{{ __('Update') }}</button>
            @endif
          </form>
          <hr>
          <div class="d-flex justify-content-between"><span>{{ __('Subtotal') }}</span><strong>{{ number_format($invoice->subtotal, 2) }}</strong></div>
          <div class="d-flex justify-content-between"><span>{{ __('Discount') }}</span><strong>-{{ number_format($invoice->discount, 2) }}</strong></div>
          <div class="d-flex justify-content-between"><span>{{ __('Tax') }}</span><strong>+{{ number_format($invoice->tax, 2) }}</strong></div>
          <div class="d-flex justify-content-between fs-5 mt-2"><span>{{ __('Total') }}</span><strong>{{ number_format($invoice->total, 2) }}</strong></div>
          @if($invoice->status !== 'paid')
          <form method="POST" action="{{ route('clinic.invoices.mark-paid', $invoice->id) }}" class="mt-3">
            @csrf
            <button class="btn btn-success w-100" onclick="return confirm('{{ __('Mark invoice as paid?') }}')">{{ __('Mark as Paid') }}</button>
          </form>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
