<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Message
 * @package App
 */
class Message extends Model
{
    protected $table = 'messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['from_user_id', 'to_user_id', 'message'];

    public function fromUser()
    {
        return $this->belongsTo('App\User', 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo('App\User', 'to_user_id');
    }
} 