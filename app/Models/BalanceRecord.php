<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $lender_id
 * @property int $borrower_id
 * @property float $balance
 * @property User $borrower
 * @property User $lender
 */
class BalanceRecord extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['lender_id', 'borrower_id', 'balance'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function borrower()
    {
        return $this->belongsTo('App\Models\User', 'borrower_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lender()
    {
        return $this->belongsTo('App\Models\User', 'lender_id');
    }
}
