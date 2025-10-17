@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container mt-4 card">
    <div class="card-header">
        <h4>Edit Payslip for: {{ $payslip->clinicUser->name }}</h4>
    </div>
    <div class="card-body">
    <form id="payslipForm" method="POST" action="{{ route('clinic.payslips.update', $payslip->id) }}">
        @csrf
        @method('PUT')

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="period_start" class="form-label">Period Start</label>
                <input type="date" id="period_start" name="period_start" class="form-control"
                    value="{{ $payslip->period_start }}" required>
            </div>
            <div class="col-md-4">
                <label for="period_end" class="form-label">Period End</label>
                <input type="date" id="period_end" name="period_end" class="form-control"
                    value="{{ $payslip->period_end }}" required>
            </div>
            <div class="col-md-4">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="payslip_status" class="form-select" required>
                    <option value="pending" {{ $payslip->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ $payslip->status === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="unpaid" {{ $payslip->status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                </select>
            </div>
        </div>

        <h5>Payslip Items</h5>
        <table class="table table-bordered" id="payslipItemsTable">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Notes</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="payslipItemsBody">
                @foreach($payslip->items as $index => $item)
                    <tr>
                        <td>
                            @if($item->type === 'fixed')
                                <input type="hidden" name="items[{{ $index }}][type]" value="fixed">
                                <span class="badge bg-secondary">{{ ucfirst($item->type) }}</span>
                            @else
                                <select name="items[{{ $index }}][type]" class="form-select item-type">
                                    <option value="bonus" {{ $item->type === 'bonus' ? 'selected' : '' }}>Bonus</option>
                                    <option value="deduction" {{ $item->type === 'deduction' ? 'selected' : '' }}>Deduction</option>
                                </select>
                            @endif
                        </td>
                        <td>
                            <input type="text" name="items[{{ $index }}][notes]" class="form-control"
                                   value="{{ $item->notes }}" {{ $item->type === 'fixed' ? 'readonly' : '' }}>
                        </td>
                        <td>
                            <input type="number" step="0.01" name="items[{{ $index }}][amount]" class="form-control amount-input"
                                   value="{{ $item->amount }}" {{ $item->type === 'fixed' ? 'readonly' : '' }}>
                        </td>
                        <td>
                            @if($item->type !== 'fixed')
                                <button type="button" class="btn btn-sm btn-danger removeRow">X</button>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="button" class="btn btn-outline-primary" id="addItemBtn">+ Add Item</button>

        <div class="mt-4 text-end">
            <h5>Gross: <span id="grossTotal">{{ $payslip->gross_amount }}</span></h5>
            <h5>Deductions: <span id="deductionTotal">{{ $payslip->deductions }}</span></h5>
            <h4>Net Amount: <span id="netTotal">{{ $payslip->net_amount }}</span></h4>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">Update Payslip</button>
        </div>
    </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    let rowIndex = {{ $payslip->items->count() }};

    function recalcTotals() {
        let gross = 0, deduction = 0;
        $('#payslipItemsBody tr').each(function () {
            const type = $(this).find('input[name*="[type]"], select[name*="[type]"]').val();
            const amount = parseFloat($(this).find('input[name*="[amount]"]').val()) || 0;
            if (['fixed', 'hours', 'percentage', 'bonus'].includes(type)) gross += amount;
            if (type === 'deduction') deduction += amount;
        });
        $('#grossTotal').text(gross.toFixed(2));
        $('#deductionTotal').text(deduction.toFixed(2));
        $('#netTotal').text((gross - deduction).toFixed(2));
    }

    $('#addItemBtn').on('click', function () {
        const newRow = `
            <tr>
                <td>
                    <select name="items[${rowIndex}][type]" class="form-select item-type">
                        <option value="bonus">Bonus</option>
                        <option value="deduction">Deduction</option>
                    </select>
                </td>
                <td><input type="text" name="items[${rowIndex}][notes]" class="form-control" placeholder="Notes"></td>
                <td><input type="number" step="0.01" name="items[${rowIndex}][amount]" class="form-control amount-input" value="0"></td>
                <td><button type="button" class="btn btn-sm btn-danger removeRow">X</button></td>
            </tr>`;
        $('#payslipItemsBody').append(newRow);
        rowIndex++;
    });

    $(document).on('input', '.amount-input', recalcTotals);
    $(document).on('change', '.item-type', recalcTotals);
    $(document).on('click', '.removeRow', function () {
        $(this).closest('tr').remove();
        recalcTotals();
    });

    recalcTotals();
});
</script>
@endpush
