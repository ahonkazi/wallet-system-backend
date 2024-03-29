<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;
protected $fillable =[
        'name',
        'price',
        'discounted_price',
        'description',
];

public function features()
{
   return $this->hasMany(PackageFeatures::class);
}

}
