<?php


namespace App\Http\Controllers;


use App\Models\automoto;
use App\Models\automotopolja;
use App\Models\slika;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class automotoController extends \Illuminate\Routing\Controller
{

    public  function setVisible(Request $req){
        $post= automotopolja::find( $req->id);
        $post->javno=1;

    }




    public function getAllTypes(){
        return automoto::all();
    }
public  function  getType($tip){

     $svi= DB::select('select * from automotopolja where javno="1" and automoto_vrsta='.$tip);

    for($i=0; $i<sizeof($svi);$i++){
        $svi[$i]->slika = slika::where('slika_automoto', $svi[$i]->id)->get();

    }
return $svi;

}


    public function getAll() {
        $svi= automotopolja::all();
        for($i=0; $i<sizeof($svi);$i++){
            $svi[$i]->slika = slika::where('slika_automoto', $svi[$i]->id)->first();

        }
        return $svi;

    }

    public  function getId($id){
        $rezultat1= automotopolja::find($id);
        $rezultat=  DB::select('select url from slika where slika_automoto='.$id);
        $rezultat1->podaci=$rezultat;
        return $rezultat1;
    }
    public function delAll(){
        DB::select('delete from slika where slika_automoto>=1 ');
        DB::select('delete  from automotopolja');
        return automotopolja::all();
    }
    public function delId($id){
        DB::select('delete  from slika where slika_automoto='.$id);
        DB::select('delete  from automotopolja where id='.$id);
        return automotopolja::all();
    }
    public function addPost(Request $request)
    {
        $produkt = new automotopolja();
        $produkt->automoto_vrsta = $request->automoto_vrsta;
        $produkt->naziv = $request->naziv;
        $produkt->marka = $request->marka;
        $produkt->model = $request->model;
        $produkt->godina_proizvodnje = $request->godina_proizvodnje;
        $produkt->kubikaza = $request->kubikaza;
        $produkt->kilometraza = $request->kilometraza;
        $produkt->boja = $request->boja;
        $produkt->registrovan = $request->registrovan;
        $produkt->datum_isteka = $request->datum_isteka;
        $produkt->opis = $request->opis;
        $produkt->stanje = $request->stanje;
        $produkt->lokacija = $request->lokacija;
        $produkt->kontakt = $request->kontakt;
        $produkt->cijena = $request->cijena;
        $produkt->sirina = $request->sirina;
        $produkt->duzina = $request->duzina;
        $produkt->user_id = $request->user_id;
        $produkt->javno = 1;
        $produkt->index='automotopolja';
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
            $slika->slika_automoto=$zadnji;
            $slika->url=$compPic;
            $slika->save();
        }


        if ($request->hasfile('slike')) {
            foreach ($request->file('slike') as $key => $file) {
                $name = $file->getClientOriginalName();
                $filenameonly = pathinfo($name,PATHINFO_FILENAME);

                $compPic =str_replace(' ','_',$filenameonly).'_'.rand() .'_'.time(). '.'.'jpg';
                $path = $file->storeAs('public/file',$compPic);



                $slika=new slika();
                $slika->slika_automoto=$zadnji;
                $slika->url=$compPic;
                $slika->save();

            }
        }

    return automotopolja::all();


    }
    public function modPostbyId(Request $request){
        $post= automotopolja::find($request->id);
        $post->automoto_vrsta = $request->automoto_vrsta;
        $post->naziv = $request->naziv;
        $post->marka = $request->marka;
        $post->model = $request->model;
        $post->godina_proizvodnje = $request->godina_proizvodnje;
        $post->kubikaza = $request->kubikaza;
        $post->kilometraza = $request->kilometraza;
        $post->boja = $request->boja;
        $post->registrovan = $request->registrovan;
        $post->datum_isteka = $request->datum_isteka;
        $post->opis = $request->opis;
        $post->stanje = $request->stanje;
        $post->lokacija = $request->lokacija;
        $post->kontakt = $request->kontakt;
        $post->cijena = $request->cijena;
        $post->sirina = $request->sirina;
        $post->duzina = $request->duzina;
        $post->user_id = $request->user_id;

        $post->save();



        return automotopolja::all();
    }


    public function Filter(Request $request){


        $id=$request->id;
        $cijena_min= $request->cijenaMin;
        $cijena_max= $request->cijenaMax;
        $marka=$request->marka;
        $model=$request->model;
        $godiste_min=$request->godisteMin;
        $godiste_max=$request->godisteMax;
        $kubikaza_min=$request->kubikazaMin;
        $kubikaza_max=$request->kubikazaMax;

        $sve= automotopolja::select('automotopolja.*')->where('javno',"1");

        if ($id)
            $sve= $sve->where('automoto_vrsta',$id);

        if ($cijena_min)
            $sve = $sve->where('cijena','>=',$cijena_min);

        if ($cijena_max)
            $sve = $sve->where('cijena','<',$cijena_max);

        if ($marka)

            $sve= $sve->where('marka',$marka);

        if ($model)

            $sve= $sve->where('model',$model);


        if ($godiste_min)

            $sve= $sve->where('godina_proizvodnje','>=',$godiste_min);
        if ($godiste_max)

            $sve= $sve->where('godina_proizvodnje','<',$godiste_max);

        if ($kubikaza_min)

            $sve= $sve->where('kubikaza','>=',$kubikaza_min);

        if ($kubikaza_max)

            $sve= $sve->where('kubikaza','<',$kubikaza_max);

            $sve = $sve->get();
        for($i=0; $i<sizeof($sve);$i++){

            $sve[$i]->slika = slika::where('slika_automoto', $sve[$i]->id)->get();

        }

        return  $sve;
    }



}
