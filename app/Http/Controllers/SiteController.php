<?php

namespace App\Http\Controllers;
use App\Models\Submissao;
use App\Models\Evento;
use App\Models\Atividade;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index(){
        return view('Site.index');
    }

    public function site($id){
        $Evento =  Evento::where('id',$id)->first();
        return view('Site.index',[
            "Submissoes" => Submissao::select('Regras','Categoria')->where('IDEvento',$id)->get(),
            "Palestras" => DB::select("SELECT pl.*,pa.Nome,pa.Foto FROM palestras pl INNER JOIN palestrantes pa ON(pl.IDPalestrante = pa.id) INNER JOIN eventos e ON(e.id = pl.IDEvento) WHERE e.id = $id "),
            "Evento" =>$Evento,
            "Atividades" => Atividade::where('id',$id)->get(),
            "Site" => json_decode($Evento->Site,true)
        ]);
    }
}
