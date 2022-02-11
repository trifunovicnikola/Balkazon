<?php


namespace App\Console;


use App\Models\tehnikapolja;
use Illuminate\Support\Facades\DB;

class tehnikaController extends Controller
{

    public function createPost(Request $request){
$post =new tehnikapolja();
$post->tehnika_vrsta=$request->tehnika_vrsta;
$post->naziv=$request->naziv;
$post->opis= $request->opis;
$post->stanje=$request->stanje;
$post->lokacija=$request->lokacija;
$post->cijena=$request->cijena;
$post->kontakt=$request->kontakt;
$post->slika=$request->slika;
$post->sirina=$request->sirina;
$post->duzina=$request->duzina;
$post->user=$request->user;
$post->karakteristika=$request->karakteristika;
$post->godina_proizvodnje=$request->godina_proizvodnje;

    }
    public function getPost(){

return DB::all();

    }
    public function delPost(){



    }

}
