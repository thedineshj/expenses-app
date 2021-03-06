<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $mobile
 * @property Bill[] $bills
 * @property UserExpense[] $userExpenses
 */
class User extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'email', 'mobile'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bills()
    {
        return $this->hasMany('App\Models\Bill', 'bill_paid_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userExpenses()
    {
        return $this->hasMany('App\Models\UserExpense');
    }
}
