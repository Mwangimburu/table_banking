<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 27/10/2018
 * Time: 11:17
 */

namespace App\Models;

class Member extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'members';

    /**
     * Main table primary key
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'nationality',
        'id_image',
        'id_number',
        'passport_number',
        'telephone_number',
        'email',
        'postal_address',
        'residential_address',
        'bank_name',
        'bank_account',
        'bank_branch',
        'members_status',
        'passport_photo'
    ];
}