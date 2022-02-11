<?php


namespace App\Http\Controllers;


use App\Models\nekretnine;
use App\Models\slika;
use App\Models\nekretninepolja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class nekretnineController extends Controller
{

    public function getAllTypes(){
        return nekretnine::all();
    }
    public  function  getType($tip){

        $svi= DB::select('select * from nekretninepolja where javno="1" and nekretnine_vrsta='.$tip);

        for($i=0; $i<sizeof($svi);$i++){
            $svi[$i]->slika = slika::where('slika_nekretnine', $svi[$i]->id)->get();

        }
        return $svi;

    }




    public function getAll() {
        $svi = nekretninepolja::all();
        for ($i = 0; $i < sizeof($svi); $i++) {
            $svi[$i]->slika = slika::where('slika_nekretnine', $svi[$i]->id)->first();
        }
        return $svi;
}

    public  function getId($id){
  $rezultat1= nekretninepolja::find($id);
        $rezultat=  DB::select('select url from slika where slika_nekretnine='.$id);
        $rezultat1->podaci=$rezultat;
        return $rezultat1;
}
    public function delAll(){
        DB::select('delete from slika where slika_nekretnine>=1 ');
    DB::select('delete  from nekretninepolja');
    return nekretninepolja::all();
}
    public function delId($id){
    DB::select('delete  from slika where slika_nekretnine='.$id);
    DB::select('delete  from nekretninepolja where id='.$id);
    return nekretninepolja::all();
}
    public function addPost(Request $request)
{
    $produkt = new nekretninepolja();
    $produkt->nekretnine_vrsta = $request->nekretnine_vrsta;
    $produkt->naziv = $request->naziv;
    $produkt->kvadratura = $request->kvadratura;
    $produkt->opis = $request->opis;
    $produkt->tip_vlasnistva = $request->tip_vlasnistva;
    $produkt->lokacija = $request->lokacija;
    $produkt->kontakt = $request->kontakt;
    $produkt->cijena = $request->cijena;
    $produkt->sirina = $request->sirina;
    $produkt->duzina = $request->duzina;
    $produkt->user_id = $request->user_id;
    $produkt->javno = 1;
    $produkt->index='nekretninepolja';
    $produkt->save();
    $zadnji = $produkt->id;


    if($request->hasFile('prva_slika')){
        $name = $request->file('prva_slika')->getClientOriginalName();
        $filenameonly = pathinfo($name,PATHINFO_FILENAME);
        $extension = $request->file('prva_slika')->getClientOriginalExtension();
        $compPic =str_replace(' ','_',$filenameonly).'_'.rand() .'_'.time(). '.'.
            $extension;
        $path = $request->file('prva_slika')->storeAs('public/file',$compPic);
        $slika=new slika();
        $slika->slika_nekretnine=$zadnji;
        $slika->url=$compPic;
        $slika->save();
    }
    else{ echo 'nema';}

    if ($request->hasfile('slike')) {
        foreach ($request->file('slike') as $key => $file) {
            $name = $file->getClientOriginalName();
            $filenameonly = pathinfo($name,PATHINFO_FILENAME);

            $compPic =str_replace(' ','_',$filenameonly).'_'.rand() .'_'.time(). '.'.'jpg';
            $path = $file->storeAs('public/file',$compPic);
            $slika=new slika();
            $slika->slika_nekretnine=$zadnji;
            $slika->url=$compPic;
            $slika->save();

        }
    }

return nekretninepolja::all();


}
    public function modPostbyId(Request $request){
        $post= nekretninepolja::find($request->id);
        $post->nekretnine_vrsta = $request->nekretnine_vrsta;
        $post->naziv = $request->naziv;
        $post->kvadratura = $request->kvadratura;
        $post->opis = $request->opis;
        $post->tip_vlasnistva = $request->tip_vlasnistva;
        $post->lokacija = $request->lokacija;
        $post->kontakt = $request->kontakt;
        $post->cijena = $request->cijena;
        $post->sirina = $request->sirina;
        $post->duzina = $request->duzina;
        $post->user_id = $request->user_id;

        $post->save();

        $post->save();
        return nekretninepolja::all();
    }
    public function Filter(Request $request){


        $id=$request->id;
        $cijena_min= $request->cijenaMin;
        $cijena_max= $request->cijenaMax;
        $tip_vlasnistva= $request->tip_vlasnistva;
        $kvadratura_min= $request->kvadraturaMin;
        $kvadratura_max= $request->kvadraturaMax;

        $sve= nekretninepolja::select('nekretninepolja.*')->where('javno',"1");

        if ($id)
            $sve= $sve->where('nekretnine_vrsta',$id);

        if ($cijena_min)
            $sve = $sve->where('cijena','>=',$cijena_min);

        if ($cijena_max)
            $sve = $sve->where('cijena','<',$cijena_max);
        if ($kvadratura_min)
            $sve = $sve->where('kvadratura','>=',$kvadratura_min);

        if ($kvadratura_max)
            $sve = $sve->where('kvadratura','<',$kvadratura_max);
        if ($tip_vlasnistva)
            $sve= $sve->where('tip_vlasnistva',$tip_vlasnistva);


        $sve = $sve->get();
        for($i=0; $i<sizeof($sve);$i++){

            $sve[$i]->slika = slika::where('slika_nekretnine', $sve[$i]->id)->get();

        }

        return  $sve;
    }



}
