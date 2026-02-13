<?php

namespace App\Http\Controllers;

use App\Models\Prefeitura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrefeituraController extends Controller
{
    public function index()
    {
        $empresaId = Auth::user()->empresa_id; 

        $prefeituras = Prefeitura::where('empresa_id', $empresaId)
                                 ->orderBy('nome')
                                 ->paginate(10);

        return view('prefeituras.index', compact('prefeituras'));
    }

    public function create()
    {
        return view('prefeituras.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'required|string|size:18', 
            'endereco' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        // Trava de segurança: Se o usuário não tiver empresa, não deixa criar
        if (!$user->empresa_id) {
            return redirect()->back()
                ->withErrors(['error' => 'Seu usuário não está vinculado a nenhuma empresa. Contate o suporte.'])
                ->withInput();
        }

        $validated['empresa_id'] = Auth::user()->empresa_id;

        $prefeitura = Prefeitura::create($validated);

        return redirect()
            ->route('prefeituras.edit', $prefeitura->id)
            ->with('success', 'Prefeitura cadastrada! Agora você pode adicionar os contratos abaixo.');
    }

    public function edit($id)
    {
        $empresaId = Auth::user()->empresa_id;

        $prefeitura = Prefeitura::where('id', $id)
                                ->where('empresa_id', $empresaId)
                                ->with(['contratos' => function($query) {
                                    $query->orderBy('created_at', 'desc'); 
                                }])
                                ->firstOrFail();

        return view('prefeituras.edit', compact('prefeitura'));
    }

    public function update(Request $request, $id)
    {
        $empresaId = Auth::user()->empresa_id;
        
        $prefeitura = Prefeitura::where('id', $id)
                                ->where('empresa_id', $empresaId)
                                ->firstOrFail();

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'required|string|size:18',
            'endereco' => 'nullable|string|max:255',
        ]);

        $prefeitura->update($validated);

        return redirect()
            ->route('prefeituras.edit', $prefeitura->id)
            ->with('success', 'Dados da prefeitura atualizados.');
    }

    public function destroy($id)
    {
        $empresaId = Auth::user()->empresa_id;
        
        $prefeitura = Prefeitura::where('id', $id)
                                ->where('empresa_id', $empresaId)
                                ->firstOrFail();

        $prefeitura->delete();

        return redirect()
            ->route('prefeituras.index')
            ->with('success', 'Prefeitura removida (arquivada) com sucesso.');
    }
}