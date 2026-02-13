<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;   

class Item extends Model
{
    use HasFactory;
    
    protected $table = 'itens'; 

    protected $fillable = ['lote_id', 'numero_item', 'descricao', 'unidade', 'quantidade', 'valor_unitario', 'valor_total'];

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function entregas()
    {
        return $this->belongsToMany(Entrega::class, 'entrega_item')
                    ->withPivot('quantidade_entregue')
                    ->withTimestamps();
    }
}
