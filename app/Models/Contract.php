<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Contract extends Model
{
    use HasFactory;

    protected $guarded =[];

    public function added_by()
    {
        return $this->belongsTo(User::class,'user_id');
    }

 
}
