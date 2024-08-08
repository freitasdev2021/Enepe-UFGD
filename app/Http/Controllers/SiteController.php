<?php

namespace App\Http\Controllers;
use App\Models\Submissao;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index(){
        return view('Site.index');
    }

    public function site(){
        return view('Site.Enepe.index',[
            "Submissoes" => Submissao::all(),
            "Palestras" => DB::select("SELECT pl.*,pa.Nome,e.Titulo as Evento,pa.Foto FROM palestras pl INNER JOIN palestrantes pa ON(pl.IDPalestrante = pa.id) INNER JOIN eventos e ON(e.id = pl.IDEvento)")
        ]);
    }
}
