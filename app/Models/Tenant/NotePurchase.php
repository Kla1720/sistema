<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Catalogs\NoteCreditType;
use App\Models\Tenant\Catalogs\NoteDebitType;

class NotePurchase extends ModelTenant
{
    protected $with = ['affected_purchase', 'note_credit_type', 'note_debit_type'];
    public $timestamps = false;

    protected $fillable = [
        'purchase_id',
        'note_type',
        'note_credit_type_id',
        'note_debit_type_id',
        'note_description',
        'affected_purchase_id',
        'data_affected_document',

    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function affected_purchase()
    {
        return $this->belongsTo(Purchase::class, 'affected_purchase_id');
    }

    public function note_credit_type()
    {
        return $this->belongsTo(NoteCreditType::class, 'note_credit_type_id');
    }

    public function note_debit_type()
    {
        return $this->belongsTo(NoteDebitType::class, 'note_debit_type_id');
    }

    public function getDataAffectedDocumentAttribute($value)
    {
        return (is_null($value))?null:(object) json_decode($value);
    }

    public function setDataAffectedDocumentAttribute($value)
    {
        $this->attributes['data_affected_document'] = (is_null($value))?null:json_encode($value);
    }

}
