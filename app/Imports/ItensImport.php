<?php

namespace App\Imports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ItensImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $loteId;

    public function __construct($loteId)
    {
        $this->loteId = $loteId;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $quantidade = $this->limparNumero($row['quantidade'] ?? 0);
        $valorUnitario = $this->limparNumero($row['valor_unitario'] ?? 0);
        
        return new Item([
            'lote_id'        => $this->loteId,
            'descricao'      => $row['descricao'] ?? 'Item Sem Descrição',
            'unidade'        => strtoupper($row['unidade'] ?? 'UN'),
            'quantidade'     => $quantidade,
            'valor_unitario' => $valorUnitario,
            'valor_total'    => $quantidade * $valorUnitario,
        ]);
    }

    public function rules(): array
    {
        return [
            'descricao'      => 'required',
            'quantidade'     => 'required',
            'valor_unitario' => 'required',
        ];
    }

    private function limparNumero($valor)
    {
        if (is_numeric($valor)) return $valor;

        $valor = str_replace(['R$', ' ', '.'], '', $valor);
        $valor = str_replace(',', '.', $valor);

        return (float) $valor;
    }
}