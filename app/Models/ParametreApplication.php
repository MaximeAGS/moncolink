<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParametreApplication extends Model
{
    protected $table = 'parametres_application';

    protected $fillable = [
        'nom',
        'description',
        'slogan',
        'logo',
    ];
}
