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

    public static function revertLast() {
        // permission check can be added here (TODO: admin only)
        $repo = new AttributionRepository();
        try {
            $repo->revertLastBatch();
            Flight::json(['ok' => true, 'message' => 'Revert effectué.']);
        } catch (\Exception $e) {
            Flight::json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }
} 
