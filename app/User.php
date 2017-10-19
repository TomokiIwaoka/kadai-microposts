<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
    
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    /* Define Many to Many relationship for follow and unfollow function */
     public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    
    /* Follow and unfollow function */
    public function follow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 自分自身ではないかの確認
        $its_me = $this->id == $userId;
        
        if ($exist || $its_me) {
            // 既にフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    public function unfollow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 自分自身ではないかの確認
        $its_me = $this->id == $userId;
        
        if ($exist && !$its_me) {
            // 既にフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
    /* Check already follow or not and returen true or not */
    public function is_following($userId) {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    /* Get microposts that the user follows */
    public function feed_microposts()
    {
        $follow_user_ids = $this->followings()->lists('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
    
    /* Get microposts that the user likes*/
    public function likes()
    {
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    /* Like the micropost 
     *自分のMicropostのLikeは可能(FacebookとかLineとかもできるので)
     * @param likeしようとしているmicropostのID
     */
    public function like($micropostId)
    {
        // 既にLikeしているかの確認
        $exist = $this->already_liked($micropostId);
        
        if ($exist) {
            // 既にLikeしていれば何もしない
            return false;
        } else {
            // 未LikeであればLikeする
            $this->likes()->attach($micropostId);
            return true;
        }
    }
    
    /* Unlike the micropost 
     * 自分のMicropostのunlikeも出来る
     * @param unlikeしようとしているmicropostのID
     */
    public function unlike($micropostId)
    {
        // 既にLikeしているかの確認
        $exist = $this->already_liked($micropostId);
          
        if ($exist) {
            // 既にLikeしていればフォローを外す
            $this->likes()->detach($micropostId);
            return true;
        } else {
            // 未Likeであれば何もしない
            return false;
        }
    }
    
    /* 当該ユーザーが特定Miropostについて既にLikeしているか確認 
     * @param like or unlikeを確認しようとしているmicropostのID
     */
    public function already_liked($micropostId) {
        return $this->likes()->where('micropost_id', $micropostId)->exists();
    }
}