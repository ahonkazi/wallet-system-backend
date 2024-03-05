<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageFeatures extends Model
{
    use HasFactory;
    protected fillable=[
'name',
'package_id',
'description',
]

}
