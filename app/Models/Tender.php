<?php

namespace App\Models;

use App\Enums\TenderItemProcessingState;
use App\Enums\TenderMandatoryContentMethod;
use App\Enums\TenderMandatoryContentUtility;
use App\Enums\TenderProjectFormat;
use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    protected $fillable = [
        'client_id',
        'bidding_id',
        'manage_current',
        'manage_offer',
        'revenue',
        'conditions',
        'invitation_require_check',
        'mode',
        'open_procedure_check',
        'invitation_request_check',
        'invitation_request_date',
        'invitation_request_processing_state',
        'reliance_require_check',
        'reliance_admit_check',
        'reliance_company',
        'reliance_date',
        'reliance_processing_state',
        'reliance_qualification',
        'partnership_require_check',
        'partnership_company',
        'partnership_processing_state',
        'partnership_activities',
        'collection_require_check',
        'collection_request_date',
        'collection_request_processing_state',
        'service_reference_require_check',
        'service_reference_number',
        'service_reference_processing_state',
        'service_reference_1',
        'service_reference_date_1',
        'service_reference_2',
        'service_reference_date_2',
        'bank_reference_require_check',
        'bank_reference_number',
        'bank_reference_processing_state',
        'bank_reference_1',
        'bank_reference_date_1',
        'bank_reference_2',
        'bank_reference_date_2',
        'pass_oe_require_check',
        'pass_oe_require_deadline_date',
        'pass_oe_require_processing_state',
        'inspection_processing_state',
        'deposit_require_check',
        'deposit_require_amount',
        'deposit_require_date',
        'deposit_require_processing_state',
        'authority_tax_require_check',
        'authority_tax_require_amount',
        'authority_tax_payment_date',
        'authority_tax_processing_state',
        'project_require_check',
        'tender_project_format',
        'project_processing_state',
        'project_points',
        'project_max_page',
        'project_format',
        'project_character',
        'project_dimension',
        'project_spacing',
        'project_printed',
        'security_utility',
        'security_method',
        'security_processing_state',
        'staff_utility',
        'staff_method',
        'staff_processing_state',
        'other_utility',
        'other_method',
        'other_processing_state',
        'note',
        'modified_user_id',
        'modified_date',
    ];

    protected $casts = [
        'invitation_request_processing_state' => TenderItemProcessingState::class,
        'reliance_processing_state' => TenderItemProcessingState::class,
        'partnership_processing_state' => TenderItemProcessingState::class,
        'collection_request_processing_state' => TenderItemProcessingState::class,
        'service_reference_processing_state' => TenderItemProcessingState::class,
        'bank_reference_processing_state' => TenderItemProcessingState::class,
        'pass_oe_require_processing_state' => TenderItemProcessingState::class,
        'inspection_processing_state' => TenderItemProcessingState::class,
        'deposit_require_processing_state' => TenderItemProcessingState::class,
        'authority_tax_processing_state' => TenderItemProcessingState::class,
        'project_processing_state' => TenderItemProcessingState::class,
        'security_processing_state' => TenderItemProcessingState::class,
        'staff_processing_state' => TenderItemProcessingState::class,
        'other_processing_state' => TenderItemProcessingState::class,
        'tender_project_format' => TenderProjectFormat::class,
        'security_method' => TenderMandatoryContentMethod::class,
        'staff_method' => TenderMandatoryContentMethod::class,
        'other_method' => TenderMandatoryContentMethod::class,
        'security_utility' => TenderMandatoryContentUtility::class,
        'staff_utility' => TenderMandatoryContentUtility::class,
        'other_utility' => TenderMandatoryContentUtility::class,
        'invitation_require_check' => 'boolean',
        'open_procedure_check' => 'boolean',
        'invitation_request_check' => 'boolean',
        'reliance_require_check' => 'boolean',
        'reliance_admit_check' => 'boolean',
        'partnership_require_check' => 'boolean',
        'collection_require_check' => 'boolean',
        'service_reference_require_check' => 'boolean',
        'bank_reference_require_check' => 'boolean',
        'pass_oe_require_check' => 'boolean',
        'deposit_require_check' => 'boolean',
        'authority_tax_require_check' => 'boolean',
        'project_require_check' => 'boolean',
        'invitation_request_date' => 'date',
        'reliance_date' => 'date',
        'collection_request_date' => 'date',
        'service_reference_date_1' => 'date',
        'service_reference_date_2' => 'date',
        'bank_reference_date_1' => 'date',
        'bank_reference_date_2' => 'date',
        'pass_oe_require_deadline_date' => 'date',
        'deposit_require_date' => 'date',
        'authority_tax_payment_date' => 'date',
        'modified_date' => 'date',
        'revenue' => 'decimal:2',
        'deposit_require_amount' => 'decimal:2',
        'authority_tax_require_amount' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function bidding()
    {
        return $this->belongsTo(Bidding::class);
    }

    public function modifiedUser()
    {
        return $this->belongsTo(User::class, 'modified_user_id');
    }

    public function necessaryDocs()
    {
        return $this->hasMany(TenderNecessaryDoc::class);
    }

    protected static function booted()
    {
        static::creating(function ($tender) {
            //
        });

        static::created(function ($tender) {
            //
        });

        static::updating(function ($tender) {
            //
        });

        static::saved(function ($tender) {
            //
        });

        static::deleting(function ($tender) {
            //
        });

        static::deleted(function ($tender) {
            //
        });
    }
}
