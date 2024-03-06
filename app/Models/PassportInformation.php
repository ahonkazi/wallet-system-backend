<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PassportInformation extends Model
{
    use HasFactory;

    protected $fillable =[
        'user_id',
        'passport_number',
        'exp_date',
        'country'
    ];

}
