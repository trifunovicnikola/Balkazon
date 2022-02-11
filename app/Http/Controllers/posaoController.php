<?php


namespace App\Http\Controllers;


use App\Models\posao;
use App\Models\posaopolja;
use App\Models\slika;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class posaoController extends \Illuminate\Routing\Controller
{


    public function getAllTypes(){
        return posao::all();
    }
    public  function  getType($tip){

        $svi= DB::select('select * from posaopolja where javno="1" and posao_vrsta='.$tip);

        for($i=0; $i<sizeof($svi);$i++){
            $svi[$i]->slika = slika::where('slika_posao', $svi[$i]->id)->get();

        }
        return $svi;

    }








    public function getAll() {
        $svi = posaopolja::all();
        for ($i = 0; $i < sizeof($svi); $i++) {
            $svi[$i]->slika = slika::where('slika_posao', $svi[$i]->id)->first();
        }
        return $svi;
    }

    public  function getId($id){
        $rezultat1= posaopolja::find($id);
        $rezultat=  DB::select('select url from slika where slika_posao='.$id);
        $rezultat1->podaci=$rezultat;
        return $rezultat1;
    }
    public function delAll(){
        DB::select('delete from slika where slika_posao>=1 ');
        DB::select('delete  from posaopolja');
        return posaopolja::all();
    }
    public function delId($id){
        DB::select('delete  from slika where slika_posao='.$id);
        DB::select('delete  from posaopolja where id='.$id);
        return posaopolja::all();
    }
    public function addPost(Request $request)
    {
        $produkt = new posaopolja();
        $produkt->posao_vrsta = $request->posao_vrsta;
        $produkt->naziv = $request->naziv;

        $produkt->opis = $request->opis;
        $produkt->plata = $request->plata;
        $produkt->lokacija = $request->lokacija;
        $produkt->kontakt = $request->kontakt;
        $produkt->javno = 1;
        $produkt->sirina = $request->sirina;
        $produkt->duzina = $request->duzina;
        $produkt->user_id = $request->user_id;
        $produkt->index='posaopolja';
        $produkt->save();
        $zadnji = $produkt->id;


        if ($request->hasFile('prva_slika')) {
            $name = $request->file('prva_slika')->getClientOriginalName();
            $filenameonly = pathinfo($name,PATHINFO_FILENAME);
            $extension = $request->file('prva_slika')->getClientOriginalExtension();
            $compPic =str_replace(' ','_',$filenameonly).'_'.rand() .'_'.time(). '.'.
                $extension;
            $path = $request->file('prva_slika')->storeAs('public/file',$compPic);
            $slika = new slika();
            $slika->slika_posao = $zadnji;
            $slika->url = $compPic;
            $slika->save();
        } else {
            echo 'nema';
        }

        if ($request->hasfile('slike')) {
            foreach ($request->file('slike') as $key => $file) {
                $name = $file->getClientOriginalName();
                $filenameonly = pathinfo($name,PATHINFO_FILENAME);

                $compPic =str_replace(' ','_',$filenameonly).'_'.rand() .'_'.time(). '.'.'jpg';
                $path = $file->storeAs('public/file',$compPic);
                $slika = new slika();
                $slika->slika_posao = $zadnji;
                $slika->url = $compPic;
                $slika->save();

            }
        }


        return posaopolja::all();
    }
        public function modPostbyId(Request $request){
            $post= posaopolja::find($request->id);
            $post->posao_vrsta = $request->posao_vrsta;
            $post->naziv = $request->naziv;

            $post->opis = $request->opis;
            $post->plata = $request->plata;
            $post->lokacija = $request->lokacija;
            $post->kontakt = $request->kontakt;

            $post->sirina = $request->sirina;
            $post->duzina = $request->duzina;
            $post->user_id = $request->user_id;

            $post->save();




        return posaopolja::all();
    }

    public function Filter(Request $request){


        $id=$request->id;
        $cijena_min= $request->cijenaMin;
        $cijena_max= $request->cijenaMax;




        $sve= posaopolja::select('posaopolja.*')->where('javno',"1");

        if ($id)
            $sve= $sve->where('posao_vrsta',$id);

        if ($cijena_min)
            $sve = $sve->where('plata','>=',$cijena_min);

        if ($cijena_max)
            $sve = $sve->where('plata','<',$cijena_max);




        $sve = $sve->get();
        for($i=0; $i<sizeof($sve);$i++){

            $sve[$i]->slika = slika::where('slika_posao', $sve[$i]->id)->get();

        }

        return  $sve;
    }



}
