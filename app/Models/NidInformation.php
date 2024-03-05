<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NidInformation extends Model
{
    use HasFactory;
    protected fillable=[
'user_id',
'name',
'nid_number',
'father_name',
'mother_name',
'date_of_birth'
]

}
