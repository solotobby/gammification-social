<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = ['name', 'email', 'phone', 'identification', 'country', 'code', 'validation', 'status'];


}
