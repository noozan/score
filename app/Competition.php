<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{

    protected $appends = ['joined'];
    public function getJoinedAttribute()
    {
        //if($this->has)
        return $this->users()->exists();
    }

    public function users()
    {
        return $this->belongsToMany(USer::class);
    }
}
