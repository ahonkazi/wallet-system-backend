<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAcount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_number',
        'bank_ifsc',
        'account_type',
        'account_name'
    ];

}
