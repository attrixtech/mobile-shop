<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairStatusHistory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function repairJob()
    {
        return $this->belongsTo(RepairJob::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
