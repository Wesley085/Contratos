<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    /**
     * Lista as empresas (provavelmente só terá uma).
     */
    public function index()
    {
        $empresas = Empresa::orderBy('created_at', 'desc')->get();
        return view('empresas.index', compact('empresas'));
    }

    /**
     * Mostra o formulário de criação.
     */
    public function create()
    {
        // Se você quiser travar para ter APENAS UMA empresa no sistema:
        // if (Empresa::count() > 0) {
        //     return redirect()->route('empresas.index')->with('error', 'Só é permitido cadastrar uma empresa.');
        // }

        return view('empresas.create');
    }

    /**
     * Salva a nova empresa no banco.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'razao_social' => 'required|string|max:255',
            'cnpj'         => 'required|string|size:18|unique:empresas,cnpj',
            'endereco'     => 'nullable|string|max:255',
            'logo_path'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Max 2MB
        ]);

        // Upload da Logo
        if ($request->hasFile('logo_path')) {
            // Salva em storage/app/public/logos
            $path = $request->file('logo_path')->store('logos', 'public');
            $validated['logo_path'] = $path;
        }

        Empresa::create($validated);

        return redirect()
            ->route('empresas.index')
            ->with('success', 'Empresa cadastrada com sucesso!');
    }

    /**
     * Mostra o formulário de edição.
     */
    public function edit($id)
    {
        $empresa = Empresa::findOrFail($id);
        return view('empresas.edit', compact('empresa'));
    }

    /**
     * Atualiza os dados da empresa.
     */
    public function update(Request $request, $id)
    {
        $empresa = Empresa::findOrFail($id);

        $validated = $request->validate([
            'razao_social' => 'required|string|max:255',
            // O ignore no unique é essencial para não dar erro ao atualizar o próprio registro
            'cnpj'         => 'required|string|size:18|unique:empresas,cnpj,' . $empresa->id,
            'endereco'     => 'nullable|string|max:255',
            'logo_path'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Lógica de Troca de Logo
        if ($request->hasFile('logo_path')) {
            // 1. Apaga a logo antiga se existir
            if ($empresa->logo_path && Storage::disk('public')->exists($empresa->logo_path)) {
                Storage::disk('public')->delete($empresa->logo_path);
            }

            // 2. Sobe a nova
            $path = $request->file('logo_path')->store('logos', 'public');
            $validated['logo_path'] = $path;
        }

        $empresa->update($validated);

        return redirect()
            ->route('empresas.index')
            ->with('success', 'Dados da empresa atualizados com sucesso!');
    }

    /**
     * Remove a empresa (Soft Delete).
     */
    public function destroy($id)
    {
        $empresa = Empresa::findOrFail($id);
        $empresa->delete();

        return redirect()
            ->route('empresas.index')
            ->with('success', 'Empresa removida com sucesso.');
    }
}