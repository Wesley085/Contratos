<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;   
use Illuminate\Database\Eloquent\SoftDeletes;

class Prefeitura extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['empresa_id', 'nome', 'cnpj', 'endereco'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function contratos()
    {
        return $this->hasMany(Contrato::class);
    }
}