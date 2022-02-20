<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $bill_paid_by
 * @property string $about_bill
 * @property string $billed_date
 * @property boolean $expense_type
 * @property float $expense
 * @property User $user
 * @property UserExpense[] $userExpenses
 */
class Bill extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['bill_paid_by', 'about_bill', 'billed_date', 'expense_type', 'expense'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'bill_paid_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userExpenses()
    {
        return $this->hasMany('App\Models\UserExpense');
    }
}
