<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckMonthlyNotification extends Model
{
    use HasFactory;
    public $table = 'check_monthly_notification';
   

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'created_at',
        'updated_at',
        'date',
        'contest_id',
    ];

}
