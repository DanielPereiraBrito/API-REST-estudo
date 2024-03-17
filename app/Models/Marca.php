<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Modelo;
class Marca extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'imagem'];

    public function rules()
    {
        return  [
            'nome' => "required|unique:marcas,nome,{$this->id}",
            'imagem' => 'required|file|mimes:png'
        ];
    }

    public function feedback()
    {
        return [
            'required' => 'O campo :attribute é obrigatório',
            'nome.unique' => 'O nome da marca ja existe',
            'imagem.file' => 'O campo imagem deve ser um arquivo de imagem',
            'imagem.mimes' => 'O arquivo deve ser uma imagem do tipo PNG'
        ];
    }

    public function modelos()
    {
        return $this->hasMany(Modelo::class);
    }
}
