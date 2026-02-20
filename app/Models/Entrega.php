<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;   

class Entrega extends Model
{
    use HasFactory;

    protected $fillable = ['contrato_id', 'user_id', 'data_entrega', 'comprovante_path', 'observacoes'];

    protected $casts = [
        'data_entrega' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    public function itens()
    {
        return $this->belongsToMany(Item::class, 'entrega_item')
                    ->withPivot('quantidade_entregue')
                    ->withTimestamps();
    }
}
