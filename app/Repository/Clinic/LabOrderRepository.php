<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\LabOrderRepositoryInterface;
use App\Models\LabOrder;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;

class LabOrderRepository implements LabOrderRepositoryInterface
{
    public function index()
    {
        return [];
    }

    public function data()
    {
        $clinicId = auth('clinic')->user()->clinic_id;

        $query = LabOrder::with(['patient', 'creator'])
            ->forClinic($clinicId)
            ->latest();

        // Filters
        $status = request('status');
        if ($status) {
            $query->where('status', $status);
        }

        if ($patientId = request('patient_id')) {
            $query->where('patient_id', $patientId);
        }

        if ($dateFrom = request('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = request('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return datatables()->of($query)
            ->addColumn('patient', fn($item) => $item->patient?->name ?: 'N/A')
            ->addColumn('doctor', fn($item) => $item->creator?->name ?: 'N/A')
            ->addColumn('status_badge', fn($item) => $this->statusBadge($item->status))
            ->addColumn('created_at', fn($item) => $item->created_at?->format('Y-m-d H:i'))
            ->addColumn('action', fn($item) => $this->actions($item))
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            $clinicUser = auth('clinic')->user();

            $data = [
                'clinic_id' => $clinicUser->clinic_id,
                'patient_id' => $request['patient_id'],
                'clinic_user_id' => $clinicUser->id,
                'doctor_profile_id' => $request['doctor_profile_id'] ?? null,
                'test_name' => $request['test_name'],
                'lab_name' => $request['lab_name'] ?? null,
                'status' => 'pending',
                'cost_amount' => $request['cost_amount'] ?? null,
                'notes' => $request['notes'] ?? null,
                'sent_at' => $request['sent_at'] ?? now(),
            ];

            $order = LabOrder::create($data);

            return $order;
        });
    }

    public function show($id)
    {
        $clinicId = auth('clinic')->user()->clinic_id;
        return LabOrder::with(['patient', 'creator', 'doctorProfile'])
            ->forClinic($clinicId)
            ->findOrFail($id);
    }

    public function uploadResults($id, $files = null, ?string $comment = null, bool $replace = false)
    {
        return DB::transaction(function () use ($id, $files, $comment, $replace) {
            $order = $this->show($id);

            // Replace existing files if requested
            if ($replace) {
                $order->clearMediaCollection('lab_results');
            }

            if ($files) {
                foreach ((array)$files as $file) {
                    if ($file) {
                        $order->addMedia($file)->toMediaCollection('lab_results');
                    }
                }
            }

            // Update result comment if provided
            if (!is_null($comment)) {
                $order->result_comment = $comment;
            }

            if ($order->status === 'pending') {
                $order->status = 'received';
                $order->received_at = now();
            }

            $order->save();
            return $order->fresh();
        });
    }

    public function complete($id)
    {
        return DB::transaction(function () use ($id) {
            $order = $this->show($id);
            $order->status = 'completed';
            $order->reviewed_at = now();
            $order->save();
            return $order;
        });
    }

    private function statusBadge(string $status): string
    {
        $map = [
            'pending' => 'warning',
            'received' => 'info',
            'completed' => 'success',
        ];
        $cls = $map[$status] ?? 'secondary';
        return '<span class="badge bg-' . $cls . '">' . ucfirst($status) . '</span>';
    }

    private function actions(LabOrder $item): string
    {
        $showUrl = route('clinic.lab-orders.show', $item->id);
        $buttons = '<div class="d-flex gap-2">';
        $buttons .= '<a href="' . $showUrl . '" class="btn btn-sm btn-success" title="View"><i class="fa fa-eye"></i></a>';
        if (in_array($item->status, ['pending', 'received'])) {
            $buttons .= '<button onclick="openUpload(' . $item->id . ')" class="btn btn-sm btn-info" title="Upload"><i class="fa fa-upload"></i></button>';
        }
        if ($item->status !== 'completed') {
            $buttons .= '<button onclick="markCompleted(' . $item->id . ')" class="btn btn-sm btn-primary" title="Complete"><i class="fa fa-check"></i></button>';
        }
        $buttons .= '</div>';
        return $buttons;
    }
}
