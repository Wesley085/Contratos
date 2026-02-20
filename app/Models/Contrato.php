<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;   
use Illuminate\Database\Eloquent\SoftDeletes;

class Contrato extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['prefeitura_id', 'numero_contrato', 'objeto', 'data_inicio', 'data_fim', 'ativo'];

    public function prefeitura()
    {
        return $this->belongsTo(Prefeitura::class);
    }

    // REMOVIDO: public function empresa() { return $this->hasMany(Empresa::class); }
    // Motivo: A empresa é acessada através da prefeitura ($this->prefeitura->empresa)

    public function lotes()
    {
        return $this->hasMany(Lote::class);
    }

    public function itens()
    {
        return $this->hasManyThrough(Item::class, Lote::class);
    }

    public function entregas()
    {
        return $this->hasMany(Entrega::class);
    }
}