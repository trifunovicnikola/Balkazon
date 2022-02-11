<?php


namespace App\Http\Controllers;


use App\Models\razno;
use App\Models\raznopolja;
use App\Models\slika;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function Sodium\add;

class raznoController extends \Illuminate\Routing\Controller
{

    public function getAllTypes(){
        return razno::all();
    }
    public  function  getType($tip){

        $svi= DB::select('select * from raznopolja where javno="1" and razno_vrsta='.$tip);

        for($i=0; $i<sizeof($svi);$i++){
            $svi[$i]->slika = slika::where('slika_razno', $svi[$i]->id)->get();

        }
        return $svi;

    }



    public function getAll()
    {
        $svi = raznopolja::all();
        for ($i = 0; $i < sizeof($svi); $i++) {
            $svi[$i]->slika = slika::where('slika_razno', $svi[$i]->id)->first();
        }
        return $svi;
    }

    public  function getId($id){
        $rezultat1= raznopolja::find($id);
        $rezultat=  DB::select('select url from slika where slika_razno='.$id);
$rezultat1->podaci=$rezultat;
        return $rezultat1;
    }
    public function delAll(){
        DB::select('delete from slika where slika_razno>=1 ');
        DB::select('delete  from raznopolja');
        return raznopolja::all();
    }
    public function delId($id){
        DB::select('delete  from slika where slika_razno='.$id);
        DB::select('delete  from raznopolja where id='.$id);
        return raznopolja::all();
    }
    public function addPost(Request $request)
    {
        $produkt = new raznopolja();
        $produkt->razno_vrsta = $request->razno_vrsta;
        $produkt->naziv = $request->naziv;

        $produkt->opis = $request->opis;
        $produkt->cijena = $request->cijena;
        $produkt->lokacija = $request->lokacija;
        $produkt->kontakt = $request->kontakt;
        $produkt->stanje = $request->stanje;
        $produkt->sirina = $request->sirina;
        $produkt->duzina = $request->duzina;
        $produkt->user_id = $request->user_id;
        $produkt->javno = 1;
        $produkt->index='raznopolja';
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
            $slika->slika_razno=$zadnji;
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
                $slika->slika_razno=$zadnji;
                $slika->url=$compPic;
                $slika->save();

            }
        }

return raznopolja::all();


    }

    public function modPostbyId(Request $request){
        $post= raznopolja::find($request->id);
        $post->razno_vrsta = $request->razno_vrsta;
        $post->naziv = $request->naziv;

        $post->opis = $request->opis;
        $post->cijena = $request->cijena;
        $post->lokacija = $request->lokacija;
        $post->kontakt = $request->kontakt;
        $post->stanje = $request->stanje;
        $post->sirina = $request->sirina;
        $post->duzina = $request->duzina;
        $post->user_id = $request->user_id;

        $post->save();
        return raznopolja::all();
    }

    public function Filter(Request $request){


        $id=$request->id;
        $cijena_min= $request->cijenaMin;
        $cijena_max= $request->cijenaMax;
        $stanje= $request->stanje;



        $sve= raznopolja::select('raznopolja.*')->where('javno',"1");

        if ($id)
            $sve= $sve->where('razno_vrsta',$id);

        if ($cijena_min)
            $sve = $sve->where('cijena','>=',$cijena_min);

        if ($cijena_max)
            $sve = $sve->where('cijena','<',$cijena_max);

        if ($stanje)

            $sve= $sve->where('stanje',$stanje);


        $sve = $sve->get();
        for($i=0; $i<sizeof($sve);$i++){

            $sve[$i]->slika = slika::where('slika_razno', $sve[$i]->id)->get();

        }

        return  $sve;
    }


}
