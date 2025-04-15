<?php

namespace addons\apidoc\controller;

use fun\addons\Controller;

class Index extends Controller{

    public function index(){

       return redirect('/apidoc/index');
    }
}