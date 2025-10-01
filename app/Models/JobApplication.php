<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobApplication extends Model
{
	use HasFactory;

	protected $fillable = [
		'job_id',
		'applicant_data',
		'status',
		'notes'
	];

	protected $casts = [
		'applicant_data' => 'array',
	];

	// Relationships
	public function job()
	{
		return $this->belongsTo(Job::class);
	}

	// Scopes
	public function scopePending($query)
	{
		return $query->where('status', 'pending');
	}

	public function scopeReviewed($query)
	{
		return $query->where('status', 'reviewed');
	}

	public function scopeAccepted($query)
	{
		return $query->where('status', 'accepted');
	}

	public function scopeRejected($query)
	{
		return $query->where('status', 'rejected');
	}

	// Helper methods to access applicant data
	public function getApplicantName()
	{
		return $this->applicant_data['name'] ?? null;
	}

	public function getApplicantEmail()
	{
		return $this->applicant_data['email'] ?? null;
	}

	public function getApplicantPhone()
	{
		return $this->applicant_data['phone'] ?? null;
	}

	public function getCvPath()
	{
		return $this->applicant_data['cv'] ?? null;
	}

	public function getDynamicFieldValue($fieldName)
	{
		return $this->applicant_data[$fieldName] ?? null;
	}
}
