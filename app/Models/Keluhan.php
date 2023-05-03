<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keluhan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'keluhan';
    protected $primaryKey = 'keluhan_id';

    //user id => nama, role, dll => cek user.php || question = keluhan
    protected $fillable = [
        'user_id',
        'question'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tanggapan()
    {
        return $this->hasMany(Tanggapan::class, 'keluhan_id');
    }
}
