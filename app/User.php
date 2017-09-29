<?php
namespace App;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Support\Facades\Hash;
class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','email','name', 'login','mobile','password',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       'created_at','updated_at',
    ];
    /**
     * Verify user's credentials.
     *
     * @param  string $email
     * @param  string $password
     * @return int|boolean
     * @see    https://github.com/lucadegasperi/oauth2-server-laravel/blob/master/docs/authorization-server/password.md
     */
    public function verify($login, $password){
        $user = User::where('login', $login)->first();
        if($user && Hash::check($password, $user->password)){
            return $user->id;
        }
        return false;
    }

    public function contacts(){
        return $this->hasMany('App\Contact','parent_id');
    }
}