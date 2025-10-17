@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container mt-4 card">
    <div class="card-header">
        <h4>Create Payslip for: {{ $clinic_user->name }}</h4>
    </div>
    <div class="card-body">

    <form id="payslipForm" method="POST" action="{{ route('clinic.payslips.store') }}">
        @csrf
        <input type="hidden" name="clinic_user_id" value="{{ $clinic_user->id }}">

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="period_start" class="form-label">Period Start</label>
                <input type="date" id="period_start" name="period_start" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label for="period_end" class="form-label">Period End</label>
                <input type="date" id="period_end" name="period_end" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="payslip_status" class="form-select" required>
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="unpaid">Unpaid</option>
                </select>
            </div>
        </div>

        <h5>Payslip Items</h5>
        <table class="table table-bordered" id="payslipItemsTable">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Label</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="payslipItemsBody">
                <!-- Default base salary row -->
                @if($contract)
                <tr>
                    <td>
                        <input type="hidden" name="items[0][type]"
                            value="fixed">
                        <span class="badge bg-secondary">
                            {{ $contract->salary_type === 'fixed' ? 'Fixed Salary' : ucfirst($contract->salary_type) }}
                        </span>
                    </td>
                    <td>
                        <input type="hidden" name="items[0][notes]" value="Base Salary">
                        {{ $contract->salary_type === 'fixed' ? 'Fixed Salary' : ucfirst($contract->salary_type) }}
                    </td>
                    <td>
                        <input type="number" step="0.01" name="items[0][amount]" value="{{ $contract->base_amount }}" class="form-control" readonly>
                    </td>
                    <td>—</td>
                </tr>
                @endif
            </tbody>
        </table>

        <button type="button" class="btn btn-outline-primary" id="addItemBtn">+ Add Item</button>

        <div class="mt-4 text-end">
            <h5>Gross: <span id="grossTotal">0.00</span></h5>
            <h5>Deductions: <span id="deductionTotal">0.00</span></h5>
            <h4>Net Amount: <span id="netTotal">0.00</span></h4>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">Save Payslip</button>
        </div>
    </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let rowIndex = 1;

        function recalcTotals() {
            let gross = 0,
                deduction = 0;
            $('#payslipItemsBody tr').each(function() {
                const type = $(this).find('input[name*="[type]"]').val();
                const amount = parseFloat($(this).find('input[name*="[amount]"]').val()) || 0;
                if (['fixed', 'hours', 'percentage', 'bonus'].includes(type)) gross += amount;
                if (type === 'deduction') deduction += amount;
            });
            $('#grossTotal').text(gross.toFixed(2));
            $('#deductionTotal').text(deduction.toFixed(2));
            $('#netTotal').text((gross - deduction).toFixed(2));
        }

        $('#addItemBtn').on('click', function() {
            const newRow = `
            <tr>
                <td>
                    <select name="items[${rowIndex}][type]" class="form-select item-type">
                        <option value="bonus">Bonus</option>
                        <option value="deduction">Deduction</option>
                    </select>
                </td>
                <td><input type="text" name="items[${rowIndex}][notes]" class="form-control" placeholder="Label"></td>
                <td><input type="number" step="0.01" name="items[${rowIndex}][amount]" class="form-control amount-input" value="0"></td>
                <td><button type="button" class="btn btn-sm btn-danger removeRow">X</button></td>
            </tr>`;
            $('#payslipItemsBody').append(newRow);
            rowIndex++;
        });

        $(document).on('input', '.amount-input', recalcTotals);
        $(document).on('change', '.item-type', recalcTotals);
        $(document).on('click', '.removeRow', function() {
            $(this).closest('tr').remove();
            recalcTotals();
        });

        recalcTotals();
    });
</script>
@endpush