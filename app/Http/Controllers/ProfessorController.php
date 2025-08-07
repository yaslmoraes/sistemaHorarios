<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use Illuminate\Http\Request;

class ProfessorController extends Controller
{
    public function index()
    {
        return Professor::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
        'nome' => 'required|string|max:255',
        'cpf' => 'required|string|max:14|unique:professores,cpf',
        'matricula' => 'string|max:20|unique:professores,matricula' . $professore->id,
        ]);

        return Professor::create($data);
    }


    public function update(Request $request, Professor $professore)
    {
        $data = $request->validate([
        'nome' => 'required|string|max:255',
        'cpf' => 'required|string|max:14|unique:professores,cpf',
        'matricula' => 'string|max:20|unique:professores,matricula' . $professore->id,
        ]);

        $professore->update($data);
        return $professore;
    }

    public function destroy($id)
    {
        $professor = Professor::findOrFail($id);
        $professor->delete();
        return response()->json(null, 204);
    }
}

