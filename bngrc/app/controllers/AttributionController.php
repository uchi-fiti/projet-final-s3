<?php

namespace app\controllers;

use app\repository\AttributionRepository;
use Flight;

class AttributionController {

    public static function index() {
        $repo = new AttributionRepository();
        $attributions = $repo->getAllAttributions();

        Flight::render('attributions/attributions', [
            'attributions' => $attributions
        ]);
    }
}
