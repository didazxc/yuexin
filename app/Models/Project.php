<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table="yuexin_projects";
    protected $guarded=[];

    protected $casts = [
        'args' => 'array',
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }

}