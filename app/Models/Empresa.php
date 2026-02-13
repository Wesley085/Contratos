<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;   
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['razao_social', 'cnpj', 'logo_path', 'endereco'];

    public function prefeituras()
    {
        return $this->hasMany(Prefeitura::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
    
}