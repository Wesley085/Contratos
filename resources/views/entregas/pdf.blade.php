<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Recibo de Entrega #{{ str_pad($entrega->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page { margin: 0cm; }
        
        body {
            margin: 0cm;
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
            background: transparent;
        }

        /* Imagem de Fundo */
        #watermark {
            position: absolute;
            top: 0;
            left: 0;
            width: 21cm;
            height: 29.7cm;
            z-index: -1000;
        }

        /* Conteúdo da Página */
        .page-content {
            margin-top: 4.5cm; 
            margin-left: 2cm;
            margin-right: 2cm;
            margin-bottom: 2cm;
        }

        /* Títulos */
        .doc-header {
            text-align: right;
            margin-bottom: 30px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        .doc-title { font-size: 18px; font-weight: bold; text-transform: uppercase; color: #062F43; }
        .doc-number { font-size: 14px; font-weight: bold; color: #555; }
        .doc-date { font-size: 11px; color: #777; margin-top: 5px; }

        .declaration-text {
            margin-bottom: 25px;
            font-size: 12px;
            text-align: justify;
            line-height: 1.6;
        }

        /* Tabela de Itens */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th {
            background-color: #062F43;
            color: #fff;
            padding: 6px 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        td {
            padding: 6px 8px;
            border: 1px solid #ddd;
            font-size: 10px;
        }
        tr:nth-child(even) { background-color: #f9f9f9; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        /* Totais */
        .total-row td {
            background-color: #eee;
            font-weight: bold;
            color: #000;
        }

        /* Observações */
        .obs-box {
            background-color: #f8f8f8;
            border: 1px solid #eee;
            padding: 10px;
            font-size: 10px;
            color: #555;
            margin-bottom: 40px;
        }

        /* Assinaturas */
        .signatures-table { border: none; margin-top: 50px; }
        .signatures-table td { border: none; padding: 20px; vertical-align: bottom; }
        .sign-line {
            border-top: 1px solid #333;
            width: 80%;
            margin: 0 auto 5px auto;
        }
        .sign-role { font-size: 10px; color: #666; }

    </style>
</head>
<body>

    {{-- TIMBRE --}}
    <div id="watermark">
        <img src="{{ public_path('img/timbre.png') }}" width="100%" height="100%">
    </div>

    @php
        $prefeitura = $entrega->contrato->prefeitura;
        $contrato = $entrega->contrato;
    @endphp

    {{-- CONTEÚDO --}}
    <div class="page-content">
        
        {{-- Título do Documento --}}
        <div class="doc-header">
            <div class="doc-title">Recibo de Entrega</div>
            <div class="doc-number">Nº {{ str_pad($entrega->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>

        {{-- Texto de Declaração --}}
        <div class="declaration-text">
            Declaramos para os devidos fins, que a empresa <strong>ÁGIL DISTRIBUIDORA E COMÉRCIO DE PRODUTOS DIVERSOS</strong>, inscrita sobre o 
            CNPJ n° 28.570.856/0001-11, realizou a entrega dos produtos descritos abaixo, para a <strong>{{ strtoupper($prefeitura->nome) }}</strong>, 
            no dia <strong>{{ $entrega->data_entrega->format('d/m/Y') }}</strong>.
        </div>
        <table>
            <thead>
                <tr>
                    <th width="5%" class="text-center">#</th>
                    <th width="50%">Descrição</th>
                    <th width="10%" class="text-center">Und</th>
                    <th width="10%" class="text-right">Qtd</th>
                    <th width="12%" class="text-right">V. Unit</th>
                    <th width="13%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $totalGeral = 0; @endphp
                @foreach($entrega->itens as $index => $item)
                    @php
                        $qtd = $item->pivot->quantidade_entregue;
                        $totalItem = $qtd * $item->valor_unitario;
                        $totalGeral += $totalItem;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            {{ $item->descricao }}
                        </td>
                        <td class="text-center">{{ $item->unidade }}</td>
                        <td class="text-right">{{ number_format($qtd, 2, ',', '.') }}</td>
                        <td class="text-right">R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                        <td class="text-right font-bold">R$ {{ number_format($totalItem, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                
                <tr class="total-row">
                    <td colspan="5" class="text-right uppercase">Total da Entrega</td>
                    <td class="text-right">R$ {{ number_format($totalGeral, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Observações --}}
        @if($entrega->observacoes)
            <div class="obs-box">
                <strong>Observações:</strong><br>
                {{ $entrega->observacoes }}
            </div>
        @endif

        {{-- Assinaturas --}}
        <table class="signatures-table">
            <tr>
                <td width="50%" class="text-center">
                    <div class="sign-line"></div>
                    <strong>Assinatura do Recebedor</strong><br>
                </td>
                {{-- <td width="50%" class="text-center">
                    <div class="sign-line"></div>
                    <strong>Recebido por</strong><br>
                    <span class="sign-role">{{ $prefeitura->nome }}</span>
                </td> --}}
            </tr>
        </table>

    </div>

</body>
</html>