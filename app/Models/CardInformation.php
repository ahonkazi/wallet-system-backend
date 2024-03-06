<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'card_number',
        'exp_month',
        'exp_year',
        'cvv',
        'card_holder_name'
    ];

}
