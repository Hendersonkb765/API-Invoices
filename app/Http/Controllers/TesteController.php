<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\HttpResponses;
class TesteController extends Controller
{
    use HttpResponses;
    public function index(){
        return $this->response('authorized',200);
    }
    public function store(){
        
    }
}
