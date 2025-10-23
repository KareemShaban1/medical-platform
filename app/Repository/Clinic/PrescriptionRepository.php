<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\PrescriptionRepositoryInterface;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Traits\HandlesMediaUploads;
use Illuminate\Support\Facades\DB;

class PrescriptionRepository implements PrescriptionRepositoryInterface
{
    use HandlesMediaUploads;
    /** ---------------------- PUBLIC METHODS ---------------------- */

    public function index()
    {
        return [];
    }

    public function data()
    {
        $prescriptions = Prescription::query();

        return datatables()->of($prescriptions)
            ->addColumn('action', fn($item) => $this->prescriptionActions($item))
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store($request)
    {
        return $this->savePrescription(new Prescription(), $request, 'created');
    }

    public function show($id)
    {
            return Prescription::findOrFail($id);
    }


    public function update($request, $id)
    {
        $prescription = Prescription::findOrFail($id);
        return $this->savePrescription($prescription, $request, 'updated');
    }


    public function destroy($id)
    {
        $prescription = Prescription::findOrFail($id);
        $prescription->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('Prescription deleted successfully'),
        ]);
    }

  


    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function savePrescription($prescription, $request, string $action)
    {
        try {
            
            DB::beginTransaction();
            $prescription->fill($request->validated())->save();
    
            // ✅ Handle Media
            if ($request->hasFile('images')) {
                $this->processMedia($prescription, $request, [
                    ['field' => 'images', 'collection' => 'prescription_images', 'multiple' => true],
                ], $action);
            }
    
            // ✅ Handle Prescription Items
            if ($request->has('items')) {
                $items = $request->input('items');
    
                if ($action === 'updated') {
                    $prescription->items()->delete();
                }
    
                foreach ($items as $item) {
                    $prescription->items()->create([
                        'drug_name' => $item['drug_name'] ?? '',
                        'dose'      => $item['dose'] ?? null,
                        'frequency' => $item['frequency'] ?? null,
                        'duration'  => $item['duration'] ?? null,
                        'notes'     => $item['notes'] ?? null,
                    ]);
                }
            }
    
            
            DB::commit();
            
            // ✅ Handle AJAX / Normal response
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => __('Prescription ' . $action . ' successfully'),
                ]);
            }
                
    
            return redirect()
                ->route('clinic.appointments.index')
                ->with('success', __('Prescription ' . $action . ' successfully'));
    
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Prescription save error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
    
            
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ], 500);
            }
    
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
    

            

    private function prescriptionActions($item): string
    {
        $editUrl = route('clinic.prescriptions.edit', $item->id);
        $showUrl = route('clinic.prescriptions.show', $item->id);

        return <<<HTML
        <div class="d-flex gap-2">
           <a href="{$showUrl}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
           <a href="{$editUrl}" class="btn btn-sm btn-warning text-white"><i class="fa fa-edit"></i></a>
           <button onclick="deletePrescription({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }


   



    private function jsonResponse(string $status, string $message)
    {
        if (request()->ajax()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}