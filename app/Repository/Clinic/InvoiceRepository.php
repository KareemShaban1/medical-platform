<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\InvoiceRepositoryInterface;
use App\Models\DoctorProfile;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function data(array $filters = [])
    {
        $clinicId = auth('clinic')->user()->clinic_id;

        $query = Invoice::with(['patient.user', 'doctorProfile.clinicUser'])
            ->where('clinic_id', $clinicId)
            ->latest();

        if (!empty($filters['doctor_profile_id'])) {
            $query->where('doctor_profile_id', $filters['doctor_profile_id']);
        }

        if (!empty($filters['patient_id'])) {
            $belongs = Patient::forClinic($clinicId)
                ->where('patients.id', $filters['patient_id'])
                ->exists();
            if ($belongs) {
                $query->where('patient_id', $filters['patient_id']);
            }
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return datatables()->of($query)
            ->addColumn('patient_name', fn($i) => $i->patient?->user?->name ?? 'N/A')
            ->addColumn('doctor_name', fn($i) => $i->doctorProfile?->name ?? 'N/A')
            ->addColumn('status_badge', function ($i) {
                $map = [
                    'unpaid' => 'warning',
                    'paid' => 'success',
                    'cancelled' => 'secondary',
                ];
                $cls = $map[$i->status] ?? 'secondary';
                return '<span class="badge bg-' . $cls . '">' . ucfirst($i->status) . '</span>';
            })
            ->addColumn('created_at', fn($i) => $i->created_at?->format('Y-m-d H:i'))
            ->addColumn('total_fmt', fn($i) => number_format($i->total, 2))
            ->addColumn('action', function($i) {
                $html = '<div class="d-flex gap-2">';
                if (hasPermission('view invoices')) {
                    $html .= '<a href="' . route('clinic.invoices.show', $i->id) . '" class="btn btn-sm btn-info text-white"><i class="fa fa-eye"></i></a>';
                }
                $html .= '</div>';
                return $html;
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function show(int $id)
    {
        $clinicId = auth('clinic')->user()->clinic_id;
        return Invoice::with(['items', 'patient.user', 'doctorProfile'])
            ->where('clinic_id', $clinicId)
            ->findOrFail($id);
    }

    public function updateHeader(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $invoice = $this->show($id);
            $invoice->update([
                'discount' => $data['discount'] ?? $invoice->discount,
                'tax' => $data['tax'] ?? $invoice->tax,
                'payment_method' => $data['payment_method'] ?? $invoice->payment_method,
            ]);
            $invoice->recalcTotals();
            return $invoice->fresh(['items']);
        });
    }

    public function addItem(int $invoiceId, array $data)
    {
        return DB::transaction(function () use ($invoiceId, $data) {
            $invoice = $this->show($invoiceId);
            $item = $invoice->items()->create([
                'description' => $data['description'],
                'quantity' => $data['quantity'] ?? 1,
                'unit_price' => $data['unit_price'] ?? 0,
            ]);
            $invoice->refresh();
            return $invoice;
        });
    }

    public function updateItem(int $invoiceId, int $itemId, array $data)
    {
        return DB::transaction(function () use ($invoiceId, $itemId, $data) {
            $invoice = $this->show($invoiceId);
            $item = $invoice->items()->where('id', $itemId)->firstOrFail();
            $item->update([
                'description' => $data['description'] ?? $item->description,
                'quantity' => $data['quantity'] ?? $item->quantity,
                'unit_price' => $data['unit_price'] ?? $item->unit_price,
            ]);
            $invoice->refresh();
            return $invoice;
        });
    }

    public function deleteItem(int $invoiceId, int $itemId)
    {
        return DB::transaction(function () use ($invoiceId, $itemId) {
            $invoice = $this->show($invoiceId);
            $invoice->items()->where('id', $itemId)->delete();
            $invoice->refresh();
            return $invoice;
        });
    }

    public function markPaid(int $id, ?string $method = null)
    {
        return DB::transaction(function () use ($id, $method) {
            $invoice = $this->show($id);
            $invoice->update([
                'status' => 'paid',
                'payment_method' => $method ?? $invoice->payment_method,
                'paid_at' => now(),
            ]);
            return $invoice->fresh();
        });
    }
}