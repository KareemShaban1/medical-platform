<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DoctorProfile extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'clinic_user_id',
        'name',
        'bio',
        'email',
        'phone',
        'twitter_link',
        'linkedin_link',
        'facebook_link',
        'instagram_link',
        'research_links',
        'years_experience',
        'specialties',
        'services_offered',
        'education',
        'experience',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'is_featured',
        'featured_by',
        'locked_for_edit',
    ];

    protected $casts = [
        'research_links' => 'array',
        'specialties' => 'array',
        'services_offered' => 'array',
        'education' => 'array',
        'experience' => 'array',
        'reviewed_at' => 'datetime',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public static function getStatuses()
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    public function clinicUser()
    {
        return $this->belongsTo(ClinicUser::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    public function featuredBy()
    {
        return $this->belongsTo(Admin::class, 'featured_by');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_photo')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml', 'image/webp']);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10);
    }

    // Accessors
    public function getProfilePhotoUrlAttribute()
    {
        $media = $this->getFirstMedia('profile_photo');
        return $media ? $media->getUrl() : null;
    }

    public function getProfilePhotoThumbAttribute()
    {
        $media = $this->getFirstMedia('profile_photo');
        return $media ? $media->getUrl('thumb') : null;
    }

    public function getStatusBadgeAttribute()
    {
        $statusClasses = [
            self::STATUS_DRAFT => 'bg-secondary',
            self::STATUS_PENDING => 'bg-warning',
            self::STATUS_APPROVED => 'bg-success',
            self::STATUS_REJECTED => 'bg-danger',
        ];

        $class = $statusClasses[$this->status] ?? 'bg-secondary';
        $text = self::getStatuses()[$this->status] ?? 'Unknown';

        return "<span class=\"badge {$class}\">{$text}</span>";
    }

    // Scopes
    public function scopeForClinicUser($query, $clinicUserId)
    {
        return $query->where('clinic_user_id', $clinicUserId);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Helper methods
    public function isDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function canBeEdited()
    {
        // If locked for edit, prevent editing regardless of status
        if ($this->locked_for_edit) {
            return false;
        }

        // Allow editing for draft and rejected profiles
        if (in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED])) {
            return true;
        }

        // For approved profiles, only allow if not locked
        if ($this->status === self::STATUS_APPROVED && !$this->locked_for_edit) {
            return true;
        }

        return false;
    }

    public function submitForReview()
    {
        $this->update([
            'status' => self::STATUS_PENDING,
            'rejection_reason' => null,
        ]);
    }

    public function approve($adminId)
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);
    }

    public function reject($adminId, $reason)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function toggleFeatured($adminId)
    {
        $this->update([
            'is_featured' => !$this->is_featured,
            'featured_by' => !$this->is_featured ? null : $adminId,
        ]);
    }

    public function toggleLockForEdit()
    {
        $this->update([
            'locked_for_edit' => !$this->locked_for_edit,
        ]);
    }

    public function isFeatured()
    {
        return $this->is_featured;
    }
}
