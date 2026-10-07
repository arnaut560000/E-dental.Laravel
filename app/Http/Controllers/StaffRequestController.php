<?php

namespace App\Http\Controllers;

use App\Models\ClientRequest;

class StaffRequestController extends Controller
{
    public function index()
    {
        $requests = ClientRequest::query()
            ->where('status', 'Waiting for schedule')
            ->orderBy('created_at')
            ->orderBy('id')
            ->paginate(20);

        return view('staff.requests.index', ['requests' => $requests]);
    }
}
