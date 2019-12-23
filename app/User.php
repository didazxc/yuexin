<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable,HasRoles,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $len = strlen($value);
        if($len<60 && $len>=8){
            $this->attributes['password']=Hash::make($value);
        }else{
            $this->attributes['password']=$value;
        }
    }

    public function isSuperAdmin()
    {
        return $this->id==1;
    }

    public function createAccessToken($name, array $scopes = [])
    {
        $res=$this->HasApiTokensCreateToken($name,$scopes);
        AccessToken::create(['access_token_id'=>$res->token->id,'access_token'=>$res->accessToken]);
        return $res;
    }

    public function projects(){
        return $this->hasMany('App\Models\Project','user_id','id');
    }

}
