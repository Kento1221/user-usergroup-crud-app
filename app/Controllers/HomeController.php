<?php

namespace Kento1221\UserUsergroupCrudApp\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        $this->render('homepage');
    }
}