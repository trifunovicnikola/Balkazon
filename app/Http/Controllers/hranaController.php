<?php


namespace App\Http\Controllers;



use App\Models\hranapolja;
use App\Models\slika;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class hranaController extends \Illuminate\Routing\Controller
{

    public function getAllTypes(){
        return hranapolja::all();
    }
    public  function  getType($tip){

        $svi= DB::select('select * from hranapolja where javno="1" and hrana_vrsta='.$tip);

        for($i=0; $i<sizeof($svi);$i++){
            $svi[$i]->slika = slika::where('slika_hrana', $svi[$i]->id)->get();

        }
        return $svi;

    }




    public function getAll() {
        $svi = hranapolja::all();
        for ($i = 0; $i < sizeof($svi); $i++) {
            $svi[$i]->slika = slika::where('slika_hrana', $svi[$i]->id)->first();
        }
        return $svi;
    }

    public  function getId($id){
        $rezultat1= hranapolja::find($id);
        $rezultat=  DB::select('select url from slika where slika_hrana='.$id);
        $rezultat1->podaci=$rezultat;
        return $rezultat1;
    }
    public function delAll(){
        DB::select('delete from slika where slika_hrana>=1 ');
        DB::select('delete  from hranapolja');
        return hranapolja::all();
    }
    public function delId($id){
        DB::select('delete  from slika where slika_hrana='.$id);
        DB::select('delete  from hranapolja where id='.$id);
        return hranapolja::all();
    }
    public function addPost(Request $request)
    {
        $produkt = new hranapolja();
        $produkt->hrana_vrsta = $request->hrana_vrsta;
        $produkt->kolicina = $request->kolicina;
        $produkt->naziv = $request->naziv;
        $produkt->opis = $request->opis;
        $produkt->domace = $request->domace;
        $produkt->lokacija = $request->lokacija;
        $produkt->kontakt = $request->kontakt;
        $produkt->cijena = $request->cijena;
        $produkt->sirina = $request->sirina;
        $produkt->duzina = $request->duzina;
        $produkt->user_id = $request->user_id;
        $produkt->index='hranapolja';

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
            $slika->slika_hrana=$zadnji;
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
                $slika->slika_hrana=$zadnji;
                $slika->url=$compPic;
                $slika->save();

            }
        }

 return hranapolja::all();


    }
    public function modPostbyId(Request $request){
        $post= hranapolja::find($request->id);

        $post->hrana_vrsta = $request->hrana_vrsta;
        $post->kolicina = $request->kolicina;
        $post->naziv = $request->naziv;
        $post->opis = $request->opis;
        $post->domace = $request->domace;
        $post->lokacija = $request->lokacija;
        $post->kontakt = $request->kontakt;
        $post->cijena = $request->cijena;
        $post->sirina = $request->sirina;
        $post->duzina = $request->duzina;
        $post->user_id = $request->user_id;


        $post->save();
        return hranapolja::all();
    }
    public function Filter(Request $request){


        $id=$request->id;
        $cijena_min= $request->cijenaMin;
        $cijena_max= $request->cijenaMax;
        $domace= $request->domace;



        $sve= hranapolja::select('hranapolja.*')->where('javno',"1");

        if ($id)
            $sve= $sve->where('hrana_vrsta',$id);

        if ($cijena_min)
            $sve = $sve->where('cijena','>=',$cijena_min);

        if ($cijena_max)
            $sve = $sve->where('cijena','<',$cijena_max);

        if ($domace)

            $sve= $sve->where('domace',$domace);


        $sve = $sve->get();
        for($i=0; $i<sizeof($sve);$i++){

            $sve[$i]->slika = slika::where('slika_hrana', $sve[$i]->id)->get();

        }

        return  $sve;
    }
}
