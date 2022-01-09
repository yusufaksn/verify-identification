<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SoapClient;
use DB;

class CustomerController extends Controller
{

    public $nowDateTime;
    public function __construct()
    {
        $this->nowDateTime = Date('Y-m-d H:i:s');
    }

    public function storeCustomer(Request $request){

       if($this->companyHasVerification($request->input('company_id')) == 1){
           $this->verifyIdentification($request->input('name'),$request->input('surname'),$request->input('identification'),$request->input('birth_year'));
       }else{
           $this->insertCustomer($request->input('name'),$request->input('surname'),$request->input('identification'));
       }
    }

    public function verifyIdentification($name,$surname,$identification,$birth_year){
        try {
            $data = array(
                'TCKimlikNo' => $identification,
                'Ad' => $this->fixCharacter($name),
                'Soyad' => $this->fixCharacter($surname),
                'DogumYili' => $birth_year
            );

            $connect = new SoapClient("https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx?WSDL");
            $sonuc = $connect->TCKimlikNoDogrula($data);
            if($sonuc == true){
                $this->insertCustomer($name,$surname,$identification);
            }
        } catch (Exception $e) {
            echo $e->faultstring;
        }
    }

    public function insertCustomer($name,$surname,$identification){
        DB::table('customers')->insert([
            'name' => $name,
            'surname' => $surname,
            'identification_number' => $identification,
            'created_at' => $this->nowDateTime
        ]);
    }


    private function companyHasVerification($companyId){
        if($companyId){
            return DB::table('company')->where(['id' => $companyId])->first()->identification_verification;
        }
    }

    private function fixCharacter($text){
        $search=array("ç","i","ı","ğ","ö","ş","ü");
        $change=array("Ç","İ","I","Ğ","Ö","Ş","Ü");
        $text=str_replace($search,$change,$text);
        $text=strtoupper($text);
        return $text;
    }

}
