<?php

namespace App\Http\Controllers;

use App\Models\Carro;
use App\Repositories\CarroReposytory;
use Illuminate\Http\Request;

class CarroController extends Controller
{
    public $carro;
    public function __construct(Carro $carro)
    {
        $this->carro = $carro;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $carroRepository = new CarroReposytory($this->carro);

        if($request->has('atributos_modelo')){
            $atributosModelo = $request->atributos_modelo;
            
            $carroRepository->selectAtributosregistrosRelacionados('modelo:id,'.$atributosModelo);
        }else{
            $carroRepository->selectAtributosregistrosRelacionados('modelo');
        }

        if($request->has('filtro')){
            $carroRepository->filtro($request->filtro);
        }

        if($request->has('atributos')){
            $carroRepository->selectAtributos($request->atributos);
        }

        return response()->json($carroRepository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCarroRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->carro->rules());
        $carro = $this->carro->create([
            'modelo_id' => $request->get('modelo_id'),
            'placa' => $request->get('placa'),
            'disponivel' => $request->get('disponivel'),
            'km' => $request->get('km'),
        ]);

        return response()->json($carro, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $carro = $this->carro->with('modelo')->find($id);

        if($carro === null){
            return response()->json(['erro' => 'Recurso pesquisado não existe'], 404);
        }
        return response()->json($carro, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCarroRequest  $request
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $carro = $this->carro->find($id);

        if($carro === null){
            return response()->json(['erro' => 'Impossivel realizar a atualização, o recurso solicitado não existe'], 404);
        }

        if($request->method() === 'PATCH'){
            $regrasDinamicas = [];

            foreach($carro->rules() as $input => $regra){
                if(array_key_exists($input, $request->all())){
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas);
        }else{
            $request->validate($carro->rules());
        }
        
        $carro->fill($request->all());

        $carro->save();

        return response()->json($carro, 200);   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $carro = $this->carro->find($id);
        if($carro === null){
            return response()->json(['erro' => 'Impossivel realizar a exclusão. O recurso solicitado não existe'], 404);
        }
        
        $carro->delete();
        
        return response()->json(['msg' => 'O carro foi removido com sucesso'], 200);
    }
}
