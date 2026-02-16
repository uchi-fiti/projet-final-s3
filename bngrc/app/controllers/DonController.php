<?php

namespace app\controllers;
use Flight;
use flight\Engine;

class DonController {
    public function showDon(){
        Flight::render("crud/dons");
    }
	
}