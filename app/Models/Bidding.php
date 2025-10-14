<?php

namespace App\Models;

use App\Enums\BiddingProcessingState;
use App\Enums\BiddingPriorityType;
use App\Enums\BiddingProcedureType;
use App\Enums\ClientType;
use Illuminate\Database\Eloquent\Model;

class Bidding extends Model
{
    protected $fillable = [
        'description',
        'amount',
        'residents',
        'bidding_state_id',
        'bidding_processing_state',
        'bidding_priority_type',
        'bidding_type_id',
        'bidding_adjudication_type_id',
        'mandatory_inspection',
        'contact',
        'client_type',
        'client_name',
        'client_id',
        'contracting_station',
        'region_id',
        'province',
        'province_id',
        'bidding_procedure_type',
        'procedure_portal',
        'cig',
        'procedure_id',
        'day',
        'month',
        'year',
        'renew',
        'assigned_user_id',
        'modified_user_id',
        'bidding_note',
        'note',
        'deadline_date',
        'deadline_time',
        'send_date',
        'send_time',
        'clarification_request_deadline_date',
        'clarification_request_deadline_time',
        'inspection_deadline_date',
        'inspection_deadline_time',
        'opening_date',
        'opening_time',
        'source1_id',
        'source2_id',
        'source3_id',
    ];

    protected $casts = [
        'bidding_processing_state_id' => BiddingProcessingState::class,
        'priority_id' => BiddingPriorityType::class,
        'procedure_type_id' => BiddingProcedureType::class,
        'client_type_id' => ClientType::class,
    ];

    public function serviceTypes()
    {
        return $this->belongsToMany(ServiceType::class, 'bidding_service_type', 'bidding_id', 'service_type_id');
    }

    public function biddingState()
    {
        return $this->belongsTo(BiddingState::class);
    }

    public function biddingType()
    {
        return $this->belongsTo(BiddingType::class);
    }

    public function biddingAdjudicationType()
    {
        return $this->belongsTo(BiddingAdjudicationType::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // public function contractingStation()
    // {
    //     return $this->belongsTo(ContractingStation::class);
    // }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function modifiedUser()
    {
        return $this->belongsTo(User::class, 'modified_user_id');
    }

    public function source1()
    {
        return $this->belongsTo(BiddingDataSource::class, 'source1_id');
    }

    public function source2()
    {
        return $this->belongsTo(BiddingDataSource::class, 'source2_id');
    }

    public function source3()
    {
        return $this->belongsTo(BiddingDataSource::class, 'source3_id');
    }
}
