<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;   

class Lote extends Model
{
    use HasFactory;

    protected $fillable = ['contrato_id', 'nome'];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    public function itens()
    {
        return $this->hasMany(Item::class);
    }
}
