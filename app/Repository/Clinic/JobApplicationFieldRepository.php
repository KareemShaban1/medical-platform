<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\JobApplicationFieldRepositoryInterface;
use App\Models\JobApplicationField;

class JobApplicationFieldRepository implements JobApplicationFieldRepositoryInterface
{


    public function show($id)
    {
        return JobApplicationField::findOrFail($id);
    }

    public function store($request)
    {
        try {
            $data = $request->all();
            $jobId = $data['job_id'];
            $fields = $data['fields'];
            $createdFields = [];



            foreach ($fields as $fieldData) {
                // Handle options for select, checkbox, radio fields
                if (in_array($fieldData['field_type'], ['select', 'checkbox', 'radio']) && isset($fieldData['options'])) {
                    // Filter out null values and empty strings
                    $fieldData['options'] = array_filter($fieldData['options'], function ($option) {
                        return !is_null($option) && $option !== '';
                    });

                    // If no valid options, set to null
                    if (empty($fieldData['options'])) {
                        $fieldData['options'] = null;
                    }
                } else {
                    $fieldData['options'] = null;
                }

                // Set default order if not provided
                if (!isset($fieldData['order']) || $fieldData['order'] === '') {
                    $fieldData['order'] = 0;
                }

                // Convert required to boolean
                $fieldData['required'] = isset($fieldData['required']) && $fieldData['required'] === '1';

                // Add job_id to field data
                $fieldData['job_id'] = $jobId;

                $jobApplicationField = JobApplicationField::create($fieldData);
                $createdFields[] = $jobApplicationField;
            }

            \Log::info('Job Application Fields Created Successfully:', [
                'created_count' => count($createdFields),
                'created_fields' => $createdFields
            ]);

            return response()->json([
                'success' => true,
                'message' => count($createdFields) . ' job application field(s) created successfully.',
                'data' => $createdFields
            ]);
        } catch (\Exception $e) {
            \Log::error('Job Application Field Creation Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the field.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update($request, $id)
    {
        try {
            $data = $request->all();
            $jobId = $data['job_id'];
            $fields = $data['fields'] ?? [];
            $updatedFields = [];

            \Log::info('Job Application Field Update Data:', [
                'job_id' => $jobId,
                'fields_count' => count($fields),
                'fields' => $fields
            ]);

            // Delete existing fields for this job
            JobApplicationField::where('job_id', $jobId)->delete();

            // Create new fields
            foreach ($fields as $fieldData) {
                // Handle options for select, checkbox, radio fields
                if (in_array($fieldData['field_type'], ['select', 'checkbox', 'radio']) && isset($fieldData['options'])) {
                    // Filter out null values and empty strings
                    $fieldData['options'] = array_filter($fieldData['options'], function ($option) {
                        return !is_null($option) && $option !== '';
                    });

                    // If no valid options, set to null
                    if (empty($fieldData['options'])) {
                        $fieldData['options'] = null;
                    }
                } else {
                    $fieldData['options'] = null;
                }

                // Set default order if not provided
                if (!isset($fieldData['order']) || $fieldData['order'] === '') {
                    $fieldData['order'] = 0;
                }

                // Convert required to boolean
                $fieldData['required'] = isset($fieldData['required']) && $fieldData['required'] === '1';

                // Add job_id to field data
                $fieldData['job_id'] = $jobId;

                $jobApplicationField = JobApplicationField::create($fieldData);
                $updatedFields[] = $jobApplicationField;
            }

            \Log::info('Job Application Fields Updated Successfully:', [
                'job_id' => $jobId,
                'updated_count' => count($updatedFields),
                'updated_fields' => $updatedFields
            ]);

            return response()->json([
                'success' => true,
                'message' => count($updatedFields) . ' job application field(s) updated successfully.',
                'data' => $updatedFields
            ]);
        } catch (\Exception $e) {
            \Log::error('Job Application Field Update Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the fields.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus($request)
    {
        $jobApplicationField = JobApplicationField::findOrFail($request->id);
        $jobApplicationField->updateStatus($request->all());
        return $jobApplicationField;
    }
}
