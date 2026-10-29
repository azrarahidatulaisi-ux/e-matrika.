<?php

namespace App\Controllers;

use App\Models\CaseModel;
use App\Models\AuditLogModel;

class MapController extends BaseController
{
    public function index(): string
    {
        // Catat akses peta
        (new AuditLogModel())->log('buka_peta');

        return view('layouts/main', [
            'pageTitle' => 'Peta Sebaran Penyakit',
            'activePage' => 'map',
            'content' => view('map/index'),
        ]);
    }
}
