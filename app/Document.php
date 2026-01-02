<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;

class Document extends Model
{
    use HasCompany;

    protected     $fillable = [
        'user_id',
        'name',
        'Folder',
        'type',
        'status',
        'publish_date',
        'expiration_date',
        'description',
        'attachments',
        'comp_id'
    ];
    public static $status = [
        'Active',
        'Draft',
        'Expired',
        'Canceled',
    ];
    public function types()
    {
        return $this->hasOne('App\DocumentType', 'id', 'type');
    }
    public function assign_user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
    public function opportunitys()
    {
        return $this->hasOne('App\Opportunities', 'id', 'opportunities');
    }
    public function accounts()
    {
        return $this->hasOne('App\Account', 'id', 'account');
    }

}
