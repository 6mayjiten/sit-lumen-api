<?php 
namespace App;
use Illuminate\Database\Eloquent\Model;
class Contact extends Model{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['id','parent_id', 'user_id', 'name', 'phone','usershare_phone','type'];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
	protected $hidden   = ['created_at','updated_at'];
    /**
     * Define a one-to-many relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user(){
        return $this->belongsTo('App\User','id');
    }
}