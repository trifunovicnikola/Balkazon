<?php

namespace App\Http\Controllers;

use App\Models\automotopolja;
use App\Models\nekretnine;
use App\Models\nekretninepolja;
use App\Models\odjecapolja;
use App\Models\posaopolja;
use App\Models\razno;
use App\Models\raznopolja;
use App\Models\slika;
use App\Models\tehnikapolja;
use App\Models\hranapolja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Namshi\JOSE\JWT;
use phpDocumentor\Reflection\Types\String_;
use Tymon\JWTAuth\Contracts\Providers\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exception\JWTException;
use Illuminate\Contracts\Auth\Factory;
use function Symfony\Component\String\s;

class UserController extends Controller
{
    public function register(Request $request){
$user=User::where('email',$request['email'])->first();
$user1=User::where('name',$request['name'])->first();

if ($user){
    $response['status']=0;
    $response['message']='Duplikat email-a';
    $response['code']=409;
}


        elseif($user1){

                $response['status']=0;
                $response['message']='Duplikat username-a';
                $response['code']=409;

        }


            else{


 $user = User::create([

   'name' =>$request->name,
         'email' =>$request->email,
         'password' =>bcrypt($request->password),
         'role'=>$request->role
     ]

 );
$response['status']=1;
$response['message']='Čestitamo uspješno ste se registrovali na Balkazonu !';
$response['code']=200;


    }
        return $response;
    }



    public function  login(Request  $request){
        $credientals= $request->only('email','password');
        try {


            if (!JWTAuth::attempt($credientals)){
                $response['status']=0;
                $response['data']=null;
                $response['message']='Neuspjesno';
                $response['code']=401;
                return response()->json($response);
            }

}
       catch (Exception $e){

           $response['data']=null;
           $response['message']='Ne moze kreirati token';
           $response['code']=500;
           return response()->json($response);
       }
       $user = auth()->user();
$data['token'] = auth()->claims([
   'user_id' => $user->id,
    'email'=> $user->email
])->attempt($credientals);
        $response['status']=1;
        $response['data']=$data;
        $response['message']='Uspjesno ste se ulogovali';
        $response['code']=200;
        $response['user']=$user;
        return response()->json($response);
    }

    public function user(){
        return Auth::user();
    }

    public function getPostbyUser($id)
    {

        $svi=[];

        $teh = DB::select('select * from tehnikapolja where user_id=' . $id);
        $aut = DB::select('select * from automotopolja where user_id=' . $id);
        $hrana = DB::select('select * from hranapolja where user_id=' . $id);
        $nek = DB::select('select * from nekretninepolja where user_id=' . $id);
        $odj = DB::select('select * from odjecapolja where user_id=' . $id);
        $pos = DB::select('select * from posaopolja where user_id=' . $id);
        $raz = DB::select('select * from raznopolja where user_id=' . $id);
        if ($teh){
            for ($i = 0; $i < sizeof($teh); $i++) {
                $teh[$i]->slika = slika::where('slika_tehnika', $teh[$i]->id)->get();
            }
            array_push($svi, $teh);}
        if ($aut){
            for ($i = 0; $i < sizeof($aut); $i++) {
                $aut[$i]->slika = slika::where('slika_automoto', $aut[$i]->id)->get();
            }
            array_push($svi, $aut);}
        if ($hrana){
            for ($i = 0; $i < sizeof($hrana); $i++) {
                $hrana[$i]->slika = slika::where('slika_hrana', $hrana[$i]->id)->get();
            }
            array_push($svi, $hrana);}
        if ($nek){
            for ($i = 0; $i < sizeof($nek); $i++) {
                $nek[$i]->slika = slika::where('slika_nekretnine', $nek[$i]->id)->get();
            }
            array_push($svi, $nek);}
        if ($odj){
            for ($i = 0; $i < sizeof($odj); $i++) {
                $odj[$i]->slika = slika::where('slika_odjeca', $odj[$i]->id)->get();
            }
            array_push($svi, $odj);}
        if ($pos){
            for ($i = 0; $i < sizeof($pos); $i++) {
                $pos[$i]->slika = slika::where('slika_posao', $pos[$i]->id)->get();
            }
            array_push($svi, $pos);}
        if ($raz) {
            for ($i = 0; $i < sizeof($raz); $i++) {
                $raz[$i]->slika = slika::where('slika_razno', $raz[$i]->id)->get();
            }
            array_push($svi, $raz);
        }
      return $svi;

    }

    public function getPostbyIdUser(Request $request)
    {
$id=$request->id;
        switch($request->tabela) {
            case('automotopolja'):
              $aut=  automotopolja::find($id);
return $aut;
            case('hranapolja'):
                $hra=  hranapolja::find($id);
                return $hra;
            case('nekretninepolja'):
                $nek=  nekretninepolja::find($id);
                return $nek;
            case('odjecapolja'):
                $odj=  odjecapolja::find($id);
                return $odj;
            case('posaopolja'):
                $pos=  posaopolja::find($id);
                return $pos;
            case('raznopolja'):
                $raz=  raznopolja::find($id);
                return $raz;
            case('tehnikapolja'):
                $teh=  tehnikapolja::find($request->id);
                return $teh;

        }}


    public function DelAsUser(Request $request)
    {
        $tabela = $request->tabela;
        $tabela_slika = substr_replace($tabela, "", -5);
       $tabela_slika= 'slika_'.$tabela_slika;
        $sql1 = <<<SQL
                          delete  from slika
                          where $tabela_slika = $request->id
SQL;
        \DB::select(\DB::raw($sql1));

        $sql = <<<SQL
                          delete  from $tabela
                          where id = $request->id
SQL;
        \DB::select(\DB::raw($sql));
  return $this->getPostbyUser($request->user_id);



}
public function ModAsUser(Request $request){
        if($request->tabela == "automotopolja"){
            $a = new automotoController();
        return   $a->modPostbyId($request);
        }
    if($request->tabela == "hranapolja"){
        $a = new hranaController();
        return   $a->modPostbyId($request);
    }
    if($request->tabela == "nekretninepolja"){
        $a = new nekretnineController();
        return   $a->modPostbyId($request);
    }
    if($request->tabela == "odjecapolja"){
        $a = new odjecaController();
        return   $a->modPostbyId($request);
    }
    if($request->tabela == "posaopolja"){
        $a = new posaoController();
        return   $a->modPostbyId($request);
    }
    if($request->tabela == "raznopolja"){
        $a = new raznoController();
        return   $a->modPostbyId($request);
    }
    if($request->tabela == "tehnikapolja"){
        $a = new tehnikaController();
        return   $a->modPostbyId($request);
    }
}
    public function GetPosts(Request $request){

        switch($request->tabela) {
            case('automotopolja'):
                $a = new automotoController();
                return   $a->getType($request->tip);
            case('hranapolja'):
                $a = new hranaController();
                return   $a->getType($request->tip);
            case('nekretninepolja'):
                $a = new nekretnineController();
                return   $a->getType($request->tip);
            case('odjecapolja'):
                $a = new odjecaController();
                return   $a->getType($request->tip);
            case('posaopolja'):
                $a = new posaoController();
                return   $a->getType($request->tip);
            case('raznopolja'):
                $a = new raznoController();
                return   $a->getType($request->tip);
            case('tehnikapolja'):
                $a = new tehnikaController();
                return   $a->getType($request->tip);

        }}


public function getAllRandom(){

    $svi = array();

    $teh = DB::select('select * from tehnikapolja  where javno="1"' );
    $aut = DB::select('select * from automotopolja  where javno="1"' );
    $hrana = DB::select('select * from hranapolja  where javno="1"' );
    $nek = DB::select('select * from nekretninepolja where javno="1" ' );
    $odj = DB::select('select * from odjecapolja where javno="1" ' );
    $pos = DB::select('select * from posaopolja where javno="1" ' );
    $raz = DB::select('select * from raznopolja where javno="1" ' );
    if ($teh){
        for ($i = 0; $i < sizeof($teh); $i++) {
            $teh[$i]->slika = slika::where('slika_tehnika', $teh[$i]->id)->get();
        }
        array_push($svi, $teh);
    }
    if ($aut){
        for ($i = 0; $i < sizeof($aut); $i++) {
            $aut[$i]->slika = slika::where('slika_automoto', $aut[$i]->id)->get();
        }
        array_push($svi, $aut);}
    if ($hrana){
        for ($i = 0; $i < sizeof($hrana); $i++) {
            $hrana[$i]->slika = slika::where('slika_hrana', $hrana[$i]->id)->get();
        }
        array_push($svi, $hrana);}
    if ($nek){
        for ($i = 0; $i < sizeof($nek); $i++) {
            $nek[$i]->slika = slika::where('slika_nekretnine', $nek[$i]->id)->get();
        }
        array_push($svi, $nek);}
    if ($odj){
        for ($i = 0; $i < sizeof($odj); $i++) {
            $odj[$i]->slika = slika::where('slika_odjeca', $odj[$i]->id)->get();
        }
        array_push($svi, $odj);}
    if ($pos){
        for ($i = 0; $i < sizeof($pos); $i++) {
            $pos[$i]->slika = slika::where('slika_posao', $pos[$i]->id)->get();
        }
        array_push($svi, $pos);}
    if ($raz) {
        for ($i = 0; $i < sizeof($raz); $i++) {
            $raz[$i]->slika = slika::where('slika_razno', $raz[$i]->id)->get();
        }
        array_push($svi, $raz);
    }
    shuffle($svi);
    $niz=[];
    foreach ($svi as $dijete){
        foreach ($dijete as $value){
            $niz[]=$value;

        }
    }
    shuffle($niz);
    return $niz;
}

public function getAllFeatured(){


    $svi = array();

    $teh = DB::select('select * from tehnikapolja  where placen=true and javno="1"' );
    $aut = DB::select('select * from automotopolja  where placen=true and javno="1"' );
    $hrana = DB::select('select * from hranapolja  where placen=true and javno="1"' );
    $nek = DB::select('select * from nekretninepolja where placen=true and javno="1" ' );
    $odj = DB::select('select * from odjecapolja where placen=true and javno="1" ' );
    $pos = DB::select('select * from posaopolja where placen=true and javno="1" ' );
    $raz = DB::select('select * from raznopolja where placen=true and javno="1" ' );
    if ($teh){
        for ($i = 0; $i < sizeof($teh); $i++) {
            $teh[$i]->slika = slika::where('slika_tehnika', $teh[$i]->id)->get();
        }
        array_push($svi, $teh);
    }
    if ($aut){
        for ($i = 0; $i < sizeof($aut); $i++) {
            $aut[$i]->slika = slika::where('slika_automoto', $aut[$i]->id)->get();
        }
        array_push($svi, $aut);}
    if ($hrana){
        for ($i = 0; $i < sizeof($hrana); $i++) {
            $hrana[$i]->slika = slika::where('slika_hrana', $hrana[$i]->id)->get();
        }
        array_push($svi, $hrana);}
    if ($nek){
        for ($i = 0; $i < sizeof($nek); $i++) {
            $nek[$i]->slika = slika::where('slika_nekretnine', $nek[$i]->id)->get();
        }
        array_push($svi, $nek);}
    if ($odj){
        for ($i = 0; $i < sizeof($odj); $i++) {
            $odj[$i]->slika = slika::where('slika_odjeca', $odj[$i]->id)->get();
        }
        array_push($svi, $odj);}
    if ($pos){
        for ($i = 0; $i < sizeof($pos); $i++) {
            $pos[$i]->slika = slika::where('slika_posao', $pos[$i]->id)->get();
        }
        array_push($svi, $pos);}
    if ($raz) {
        for ($i = 0; $i < sizeof($raz); $i++) {
            $raz[$i]->slika = slika::where('slika_razno', $raz[$i]->id)->get();
        }
        array_push($svi, $raz);
    }
   shuffle($svi);
    $niz=[];
    foreach ($svi as $dijete){
        foreach ($dijete as $value){
            $niz[]=$value;

        }
    }
  shuffle($niz);
    return $niz;
}



    public function getAllCheap(){


        $svi = array();

        $teh = DB::select('select * from tehnikapolja  where  cijena<100 and javno="1"' );
        $aut = DB::select('select * from automotopolja  where  cijena<100 and javno="1"' );
        $hrana = DB::select('select * from hranapolja  where  cijena<100 and javno="1"' );
        $nek = DB::select('select * from nekretninepolja where  cijena<100 and javno="1" ' );
        $odj = DB::select('select * from odjecapolja where cijena<100 and javno="1" ' );
        $raz = DB::select('select * from raznopolja where  cijena<100 and javno="1" ' );
        if ($teh){
            for ($i = 0; $i < sizeof($teh); $i++) {
                $teh[$i]->slika = slika::where('slika_tehnika', $teh[$i]->id)->get();
            }
            array_push($svi, $teh);
        }
        if ($aut){
            for ($i = 0; $i < sizeof($aut); $i++) {
                $aut[$i]->slika = slika::where('slika_automoto', $aut[$i]->id)->get();
            }
            array_push($svi, $aut);}
        if ($hrana){
            for ($i = 0; $i < sizeof($hrana); $i++) {
                $hrana[$i]->slika = slika::where('slika_hrana', $hrana[$i]->id)->get();
            }
            array_push($svi, $hrana);}
        if ($nek){
            for ($i = 0; $i < sizeof($nek); $i++) {
                $nek[$i]->slika = slika::where('slika_nekretnine', $nek[$i]->id)->get();
            }
            array_push($svi, $nek);}
        if ($odj){
            for ($i = 0; $i < sizeof($odj); $i++) {
                $odj[$i]->slika = slika::where('slika_odjeca', $odj[$i]->id)->get();
            }
            array_push($svi, $odj);}
        if ($raz) {
            for ($i = 0; $i < sizeof($raz); $i++) {
                $raz[$i]->slika = slika::where('slika_razno', $raz[$i]->id)->get();
            }
            array_push($svi, $raz);
        }
        shuffle($svi);
        $niz=[];
        foreach ($svi as $dijete){
            foreach ($dijete as $value){
                $niz[]=$value;

            }
        }
        shuffle($niz);
        return $niz;
    }
    public function getNewest(){


        $svi = array();

        $teh = DB::select('select * from tehnikapolja  where    javno="1"' );
        $aut = DB::select('select * from automotopolja  where    javno="1"' );
        $hrana = DB::select('select * from hranapolja  where    javno="1"' );
        $nek = DB::select('select * from nekretninepolja where    javno="1" ' );
        $odj = DB::select('select * from odjecapolja where   javno="1" ' );
        $raz = DB::select('select * from raznopolja where    javno="1" ' );
        if ($teh){
            for ($i = 0; $i < sizeof($teh); $i++) {
                $teh[$i]->slika = slika::where('slika_tehnika', $teh[$i]->id)->get();
            }
            array_push($svi, $teh);
        }
        if ($aut){
            for ($i = 0; $i < sizeof($aut); $i++) {
                $aut[$i]->slika = slika::where('slika_automoto', $aut[$i]->id)->get();
            }
            array_push($svi, $aut);}
        if ($hrana){
            for ($i = 0; $i < sizeof($hrana); $i++) {
                $hrana[$i]->slika = slika::where('slika_hrana', $hrana[$i]->id)->get();
            }
            array_push($svi, $hrana);}
        if ($nek){
            for ($i = 0; $i < sizeof($nek); $i++) {
                $nek[$i]->slika = slika::where('slika_nekretnine', $nek[$i]->id)->get();
            }
            array_push($svi, $nek);}
        if ($odj){
            for ($i = 0; $i < sizeof($odj); $i++) {
                $odj[$i]->slika = slika::where('slika_odjeca', $odj[$i]->id)->get();
            }
            array_push($svi, $odj);}
        if ($raz) {
            for ($i = 0; $i < sizeof($raz); $i++) {
                $raz[$i]->slika = slika::where('slika_razno', $raz[$i]->id)->get();
            }
            array_push($svi, $raz);
        }
        shuffle($svi);
        $niz=[];
        foreach ($svi as $dijete){
            foreach ($dijete as $value){
                $niz[]=$value;

            }
        }

        return $niz;
    }


    public function setAllNew(Request $request)
    {

        switch($request->index) {
            case('automotopolja'):
                $post = automotopolja::find($request->id);
                $post->javno = 1;
                $post->save();
                return $this->getAllNew();
            case('hranapolja'):
                $post = hranapolja::find($request->id);
                $post->javno = 1;
                $post->save();
                return $this->getAllNew();
            case('nekretninepolja'):
                $post = nekretninepolja::find($request->id);
                $post->javno = 1;
                $post->save();
                return $this->getAllNew();
            case('odjecapolja'):
                $post = odjecapolja::find($request->id);
                $post->javno = 1;
                $post->save();
                return $this->getAllNew();
            case('posaopolja'):
                $post = posaopolja::find($request->id);
                $post->javno = 1;
                $post->save();
                return $this->getAllNew();
            case('raznopolja'):
                $post = raznopolja::find($request->id);
                $post->javno = 1;
                $post->save();
                return $this->getAllNew();
            case('tehnikapolja'):
                $post = tehnikapolja::find($request->id);
                $post->javno = 1;
                $post->save();
                return $this->getAllNew();

        }






    }








    public function modPostUser(Request $request)
    {
        $id=$request->user_id;
        switch($request->index) {
            case('automotopolja'):
                $post= automotopolja::find($request->id);

                $post->naziv=$request->naziv;
                $post->opis=$request->opis;
                if($post->cijena != $request->cijena) {
                    $post->modcijena=$request->cijena;
                    $a = 100 * ($post->cijena - $request->cijena) / $post->cijena;
                     $b=round($a);
                    $c= abs($b);
                    $post->procenat = $c;
                }


                $post->save();
                return $this->getPostbyUser($id);
                break;
            case('hranapolja'):
                $post = hranapolja::find($request->id);

                $post->naziv=$request->naziv;
                $post->opis=$request->opis;
                if($post->cijena != $request->cijena) {
                    $post->modcijena=$request->cijena;
                    $a = 100 * ($post->cijena - $request->cijena) / $post->cijena;
                    $b=round($a);
                    $c= abs($b);
                    $post->procenat = $c;
                }
                $post->save();
                return $this->getPostbyUser($id);
                break;
            case('nekretninepolja'):
                $post = nekretninepolja::find($request->id);

                $post->naziv=$request->naziv;
                $post->opis=$request->opis;
                if($post->cijena != $request->cijena) {
                    $post->modcijena=$request->cijena;
                    $a = 100 * ($post->cijena - $request->cijena) / $post->cijena;
                    $b=round($a);
                    $c= abs($b);
                    $post->procenat = $c;
                }
                $post->save();
                return $this->getPostbyUser($id);
                break;
            case('odjecapolja'):
                $post = odjecapolja::find($request->id);

                $post->naziv=$request->naziv;
                $post->opis=$request->opis;
                if($post->cijena != $request->cijena) {
                    $post->modcijena=$request->cijena;
                    $a = 100 * ($post->cijena - $request->cijena) / $post->cijena;
                    $b=round($a);
                    $c= abs($b);
                    $post->procenat = $c;
                }
                $post->save();
                return $this->getPostbyUser($id);
                break;
            case('posaopolja'):
                $post = posaopolja::find($request->id);

                $post->naziv=$request->naziv;
                $post->opis=$request->opis;
                if($post->cijena != $request->cijena) {
                    $post->modcijena=$request->cijena;
                    $a = 100 * ($post->cijena - $request->cijena) / $post->cijena;
                    $b=round($a);
                    $c= abs($b);
                    $post->procenat = $c;
                }
                $post->save();
                return $this->getPostbyUser($id);
                break;
            case('raznopolja'):
                $post = raznopolja::find($request->id);

                $post->naziv=$request->naziv;
                $post->opis=$request->opis;
                if($post->cijena != $request->cijena) {
                    $post->modcijena=$request->cijena;
                    $a = 100 * ($post->cijena - $request->cijena) / $post->cijena;
                    $b=round($a);
                    $c= abs($b);
                    $post->procenat = $c;
                }
                $post->save();
                return $this->getPostbyUser($id);
                 break;
            case('tehnikapolja'):
                $post = tehnikapolja::find($request->id);

                $post->naziv=$request->naziv;
                $post->opis=$request->opis;
                if($post->cijena != $request->cijena) {
                    $post->modcijena=$request->cijena;
                    $a = 100 * ($post->cijena - $request->cijena) / $post->cijena;
                    $b=round($a);
                    $c= abs($b);
                    $post->procenat = $c;

                }
                $post->save();
                return $this->getPostbyUser($id);
                break;

        }







    }


    public function deleteNew(Request $request)
    {

        switch ($request->index) {
            case('automotopolja'):
                DB::select('delete  from slika where slika_automoto=' . $request->id);
                DB::select('delete  from automotopolja where id=' . $request->id);

            case('hranapolja'):
                DB::select('delete  from slika where slika_hrana=' . $request->id);
                DB::select('delete  from hranapolja where id=' . $request->id);
            case('nekretninepolja'):
                DB::select('delete  from slika where slika_nekretnine=' . $request->id);
                DB::select('delete  from nekretninepolja where id=' . $request->id);
            case('odjecapolja'):
                DB::select('delete  from slika where slika_odjeca=' . $request->id);
                DB::select('delete  from odjecapolja where id=' . $request->id);

//
//            case('hranapolja'):
//                DB::select('delete  from slika where slika_hrana=' . $request->id);
//                DB::select('delete  from hranapolja where id=' . $request->id);

//            case('nekretninepolja'):
//                DB::select('delete  from slika where slika_nekretnine=' . $request->id);
//                DB::select('delete  from nekretninepolja where id=' . $request->id);
//
//            case('odjecapolja'):
//                DB::select('delete  from slika where slika_odjeca=' . $request->id);
//                DB::select('delete  from odjecapolja where id=' . $request->id);

            case('posaopolja'):
                DB::select('delete  from slika where slika_posao=' . $request->id);
                DB::select('delete  from posaopolja where id=' . $request->id);
            case('raznopolja'):
                DB::select('delete  from slika where slika_razno=' . $request->id);
                DB::select('delete  from raznopolja where id=' . $request->id);
            case('tehnikapolja'):
                DB::select('delete  from slika where slika_tehnika=' . $request->id);
                DB::select('delete  from tehnikapolja where id=' . $request->id);

                return $this->getAllNew();
        }
    }










    public function getAllNew(){


        $svi = array();

        $teh = DB::select('select * from tehnikapolja  where  javno=0' );
        $aut = DB::select('select * from automotopolja  where  javno=0' );
        $hrana = DB::select('select * from hranapolja  where  javno=0' );
        $nek = DB::select('select * from nekretninepolja where  javno=0 ' );
        $odj = DB::select('select * from odjecapolja where  javno=0 ' );
        $pos = DB::select('select * from posaopolja where  javno=0 ' );
        $raz = DB::select('select * from raznopolja where  javno=0 ' );
        if ($teh){
            for ($i = 0; $i < sizeof($teh); $i++) {
                $teh[$i]->slika = slika::where('slika_tehnika', $teh[$i]->id)->get();
            }
            array_push($svi, $teh);}
        if ($aut){
            for ($i = 0; $i < sizeof($aut); $i++) {
                $aut[$i]->slika = slika::where('slika_automoto', $aut[$i]->id)->get();
            }
            array_push($svi, $aut);}
        if ($hrana){
            for ($i = 0; $i < sizeof($hrana); $i++) {
                $hrana[$i]->slika = slika::where('slika_hrana', $hrana[$i]->id)->get();
            }
            array_push($svi, $hrana);}
        if ($nek){
            for ($i = 0; $i < sizeof($nek); $i++) {
                $nek[$i]->slika = slika::where('slika_nekretnine', $nek[$i]->id)->get();
            }
            array_push($svi, $nek);}
        if ($odj){
            for ($i = 0; $i < sizeof($odj); $i++) {
                $odj[$i]->slika = slika::where('slika_odjeca', $odj[$i]->id)->get();
            }
            array_push($svi, $odj);}
        if ($pos){
            for ($i = 0; $i < sizeof($pos); $i++) {
                $pos[$i]->slika = slika::where('slika_posao', $pos[$i]->id)->get();
            }
            array_push($svi, $pos);}
        if ($raz) {
            for ($i = 0; $i < sizeof($raz); $i++) {
                $raz[$i]->slika = slika::where('slika_razno', $raz[$i]->id)->get();
            }
            array_push($svi, $raz);
        }
        $niz=[];
        foreach ($svi as $dijete){
            foreach ($dijete as $value){
                $niz[]=$value;

            }
        }

        return $niz;

    }







    public function AddAsUser(Request $request){
        if($request->tabela == "automotopolja"){
            $a = new automotoController();
            return   $a->addPost($request);
        }
        if($request->tabela == "hranapolja"){
            $a = new hranaController();
            return   $a->addPost($request);
        }
        if($request->tabela == "nekretninepolja"){
            $a = new nekretnineController();
            return   $a->addPost($request);
        }
        if($request->tabela == "odjecapolja"){
            $a = new odjecaController();
            return   $a->addPost($request);
        }
        if($request->tabela == "posaopolja"){
            $a = new posaoController();
            return   $a->addPost($request);
        }
        if($request->tabela == "raznopolja"){
            $a = new raznoController();
            return   $a->addPost($request);
        }
        if($request->tabela == "tehnikapolja"){
            $a = new tehnikaController();
            return   $a->addPost($request);
        }
    }
    public function Filter(Request $request){
        if($request->tabela == "automotopolja"){
            $a = new automotoController();
            return   $a->Filter($request);
        }
        if($request->tabela == "hranapolja"){
            $a = new hranaController();
            return   $a->Filter($request);
        }
        if($request->tabela == "nekretninepolja"){
            $a = new nekretnineController();
            return   $a->Filter($request);
        }
        if($request->tabela == "odjecapolja"){
            $a = new odjecaController();
            return   $a->Filter($request);
        }
        if($request->tabela == "posaopolja"){
            $a = new posaoController();
            return   $a->Filter($request);
        }
        if($request->tabela == "raznopolja"){
            $a = new raznoController();
            return   $a->Filter($request);
        }
        if($request->tabela == "tehnikapolja"){
            $a = new tehnikaController();
            return   $a->Filter($request);
        }
    }
    public function GetId(Request $request){
        if($request->tabela == "automotopolja"){
            $a = new automotoController();
            $b= $request->id;
            return   $a->getId($b);
        }
        if($request->tabela == "hranapolja"){
            $a = new hranaController();
            $b= $request->id;
            return   $a->getId($b);
        }
        if($request->tabela == "nekretninepolja"){
            $a = new nekretnineController();
            $b= $request->id;
            return   $a->getId($b);
        }
        if($request->tabela == "odjecapolja"){
            $a = new odjecaController();
            $b= $request->id;
            return   $a->getId($b);
        }
        if($request->tabela == "posaopolja"){
            $a = new posaoController();
            $b= $request->id;
            return   $a->getId($b);
        }
        if($request->tabela == "raznopolja"){
            $a = new raznoController();
            $b= $request->id;
            return   $a->getId($b);
        }
        if($request->tabela == "tehnikapolja"){
            $a = new tehnikaController();
            $b= $request->id;
            return   $a->getId($b);
        }
    }

    public function deleteAdmin()
    {
        if(Auth::user()->hasRole('admin'))
        {
           return razno::all();
        }else
        {
            return 23;
        }
    }


    public function show()
    {
        return User::all();
    }
    public function showId($id)
    {
        return User::find($id);
    }

    public function delete($id)
    {

        DB::select('DELETE FROM users WHERE id='.$id);
        return User::all();
    }

}
