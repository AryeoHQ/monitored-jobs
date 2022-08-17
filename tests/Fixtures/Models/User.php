<?php

namespace Aryeo\MonitoredJobs\Tests\Fixtures\Models;

use Aryeo\MonitoredJobs\Tests\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    protected static function newFactory()
    {
        return new UserFactory();
    }
}
