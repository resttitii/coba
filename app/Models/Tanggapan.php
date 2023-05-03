<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tanggapan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'tanggapan'; //di table tanggapan
    protected $primaryKey = 'tanggapan_id';

    //user id => nama, role || id keluhan => keluhan, comment => tanggapan
    protected $fillable = [
        'user_id',
        'keluhan_id',
        'comment'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function keluhan()
    {
        return $this->belongsTo(Keluhan::class, 'keluhan_id');
    }
}
