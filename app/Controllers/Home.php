<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $data = [
            'title' => 'Home',
            'description' => 'Bem vindo ao SisPAM',
            'keywords' => 'home, bem vindo, application, SisPAM'
        ];
        return view('index', $data);
    }
}
