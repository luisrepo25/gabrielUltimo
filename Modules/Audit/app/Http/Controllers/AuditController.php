<?php

namespace Modules\Audit\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Audit\Models\Bitacora;

class AuditController extends Controller
{
    /**
     * Display a listing of the bitacora logs.
     */
    public function index()
    {
        $registros = Bitacora::orderBy('created_at', 'desc')->get();
        return view('bitacora.index', compact('registros'));
    }
}
