<?php

namespace Aryeo\MonitoredJobs\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoredJobException extends Model
{
    use HasFactory;

    public $guarded = ['id'];
}
