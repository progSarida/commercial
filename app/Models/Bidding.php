<?php

namespace App\Models;

use App\Enums\BiddingProcessingState;
use App\Enums\BiddingPriorityType;
use App\Enums\BiddingProcedureType;
use App\Enums\ClientType;
use App\Enums\FeasibilityType;
use App\Enums\InterestExpressionType;
use App\Enums\SendModeType;
use App\Enums\YesNo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Bidding extends Model
{
    protected $fillable = [
        'description',
        'amount',
        'residents',
        'feasibility_type',
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
        'send_mode',
        'clarification_request_deadline_date',
        'clarification_request_deadline_time',
        'inspection_deadline_date',
        'inspection_deadline_time',
        'inspection_date',
        // 'inspection_time',
        'opening_date',
        'opening_time',
        'source1_id',
        'source2_id',
        'source3_id',
        'attachment_path',
        'awarded',
        'closure_date',
        'interest_expression_type',
        'interest_deadline_date',
        'interest_deadline_time',
        'interest_send_date',
        'interest_send_time',
        'interest_send_mode_type',
        'send_mode_type',
    ];

    protected $casts = [
        'interest_expression_type' => InterestExpressionType::class,
        'interest_send_mode_type' => SendModeType::class,
        'send_mode_type' => SendModeType::class,
        'feasibility_type' => FeasibilityType::class,
        'bidding_processing_state' => BiddingProcessingState::class,
        'bidding_priority_type' => BiddingPriorityType::class,
        'bidding_procedure_type' => BiddingProcedureType::class,
        'client_type' => ClientType::class,
        'awarded' => YesNo::class,
        'deadline_date' => 'date',
        'deadline_time' => 'date',
        'send_date' => 'date',
        'clarification_request_deadline_date' => 'date',
        'inspection_deadline_date' => 'date',
        'interest_deadline_date' => 'date',
        'interest_send_date' => 'date',
        'opening_date' => 'date',
        'closure_date' => 'date',
    ];

    public function tender()
    {
        return $this->hasOne(Tender::class);
    }

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

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
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

    // public function scopeUpcoming(Builder $query): void
    // {
    //     $query->whereDate('deadline_date', '>=', today()->toDateString())
    //       ->orWhereNull('deadline_date');
    // }

    // public function scopeUpcoming(Builder $query): void
    // {
    //     $today = today()->toDateString();

    //     $query->where(function (Builder $subQuery) use ($today) {
    //         // $subQuery->whereRaw('COALESCE(deadline_date, interest_deadline_date) >= ?', [$today]);      // nasconde SOLO se deadline_date scaduta => filtri
    //                 // ->orWhere(function ($q) {
    //                 //     $q->whereNull('deadline_date')
    //                 //     ->whereNull('interest_deadline_date');
    //                 // });
    //         $subQuery->whereDate('deadline_date', '>=', today())
    //              ->orWhereNull('deadline_date');
    //     });
    // }

    public function scopeUpcoming(Builder $query): void
    {
        $query->where(function (Builder $subQuery) {
            // 1. Record con deadline futura o odierna
            $subQuery->whereDate('deadline_date', '>=', today())
                // 2. OPPURE record senza deadline, ma solo se fattibili
                ->orWhere(function (Builder $q) {
                    $q->whereNull('deadline_date')
                    ->where('feasibility_type', '!=', FeasibilityType::NOT_FEASIBLE);
                });
        });
    }

    protected static function booted()
    {
        static::creating(function ($bidding) {
            // $bidding->modified_user_id = Auth::user()->id;
        });

        static::created(function ($bidding) {
            if(!$bidding->attachment_path){
                $bidding->attachment_path = "biddings_attach/{$bidding->id}";
                $bidding->save();
            }
        });

        static::updating(function ($bidding) {
            // $bidding->modified_user_id = Auth::user()->id;
        });

        static::saving(function ($bidding) {
            $bidding->modified_user_id = Auth::user()->id;
        });

        static::saved(function ($bidding) {
            $notPending = $bidding->bidding_processing_state && $bidding->bidding_processing_state != BiddingProcessingState::PENDING;
            if ($notPending) {
                Tender::firstOrCreate(
                    ['bidding_id' => $bidding->id],                         // controllo su bidding_id
                    ['client_id' => $bidding->client_id]                    // se lo crea aggiunge anche client_id
                );
            } else {
                Tender::where('bidding_id', $bidding->id)->delete();        // se cambio lo stato lavorazione a vuoto o 'Non iniziata' elimino il dettagli della gara
            }
        });

        static::deleting(function ($bidding) {
            //
        });

        static::deleted(function ($bidding) {
            if ($bidding->attachment_path) {
                Storage::disk('public')->deleteDirectory($bidding->attachment_path);
            }
            Tender::where('bidding_id', $bidding->id)->delete();
        });
    }
}
