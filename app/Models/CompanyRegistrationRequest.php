<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * "سجّل شركتك" is a request/review workflow, not a self-serve signup:
 * a company submits this, an admin approves/rejects/marks it contacted.
 * Approving now creates (or updates) a real `Company` record - see
 * `company()` below and `CompanyRegistrationRequestResource::approve()` -
 * but the public Companies Directory/profile pages remain deferred to
 * Phase 2, and any payment/collection still happens manually outside this
 * table. See docs/DECISIONS.md for the full rationale.
 */
class CompanyRegistrationRequest extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_CONTACTED = 'contacted';

    /**
     * @var array<string, string>
     */
    public const STATUSES = [
        self::STATUS_PENDING => 'قيد المراجعة',
        self::STATUS_APPROVED => 'مقبول',
        self::STATUS_REJECTED => 'مرفوض',
        self::STATUS_CONTACTED => 'تم التواصل',
    ];

    protected $fillable = [
        'company_name',
        'contact_name',
        'phone',
        'email',
        'city',
        'category',
        'city_id',
        'category_id',
        'website',
        'logo',
        'description',
        'notes',
        'status',
        'reviewed_at',
        'reviewed_by',
        'admin_notes',
        'company_id',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * The real city relationship. Named cityRelation() (rather than city())
     * because the legacy `city` text column is still present on this model
     * for backward compatibility - accessing $model->city always resolves
     * to that raw column, never this relation, if both share the same name
     * (see App\Models\News::categoryRelation() for the identical, already-
     * established precedent in this codebase).
     */
    public function cityRelation(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * The real category relationship - see cityRelation() docblock for why
     * this isn't named category().
     */
    public function categoryRelation(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isContacted(): bool
    {
        return $this->status === self::STATUS_CONTACTED;
    }
}
