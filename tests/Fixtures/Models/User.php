<?php

namespace Aryeo\MonitoredJobs\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Aryeo\MonitoredJobs\Tests\Factories\UserFactory;

class User extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    protected static function newFactory()
    {
        return new UserFactory();
    }
}
