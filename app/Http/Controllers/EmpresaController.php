<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresas = Empresa::orderBy('created_at', 'desc')->get();
        return view('empresas.index', compact('empresas'));
    }

    public function create()
    {
        // Ninguém cria empresa no sistema
        // return redirect()->route('empresas.index')->with('error', 'Só é permitido cadastrar uma empresa.');
        

        return view('empresas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'razao_social' => 'required|string|max:255',
            'cnpj'         => 'required|string|size:18|unique:empresas,cnpj',
            'endereco'     => 'nullable|string|max:255',
            'logo_path'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Max 2MB
        ]);

        if ($request->hasFile('logo_path')) {
            $path = $request->file('logo_path')->store('logos', 'public');
            $validated['logo_path'] = $path;
        }

        Empresa::create($validated);

        return redirect()
            ->route('empresas.index')
            ->with('success', 'Empresa cadastrada com sucesso!');
    }

    public function edit($id)
    {
        $empresa = Empresa::findOrFail($id);
        return view('empresas.edit', compact('empresa'));
    }

    public function update(Request $request, $id)
    {
        $empresa = Empresa::findOrFail($id);

        $validated = $request->validate([
            'razao_social' => 'required|string|max:255',
            'cnpj'         => 'required|string|size:18|unique:empresas,cnpj,' . $empresa->id,
            'endereco'     => 'nullable|string|max:255',
            'logo_path'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('logo_path')) {
            if ($empresa->logo_path && Storage::disk('public')->exists($empresa->logo_path)) {
                Storage::disk('public')->delete($empresa->logo_path);
            }

            $path = $request->file('logo_path')->store('logos', 'public');
            $validated['logo_path'] = $path;
        }

        $empresa->update($validated);

        return redirect()
            ->route('empresas.index')
            ->with('success', 'Dados da empresa atualizados com sucesso!');
    }

    public function destroy($id)
    {
        $empresa = Empresa::findOrFail($id);
        $empresa->delete();

        return redirect()
            ->route('empresas.index')
            ->with('success', 'Empresa removida com sucesso.');
    }
}