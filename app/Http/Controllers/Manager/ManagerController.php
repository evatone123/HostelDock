<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Hostel;

abstract class ManagerController extends Controller
{
    protected function managedHostels()
    {
        return auth()->user()->managedHostels();
    }

    protected function managedHostelIds()
    {
        return $this->managedHostels()->pluck('hostels.id');
    }

    protected function authorizeHostel(Hostel $hostel): void
    {
        abort_unless(auth()->user()->canManageHostel($hostel), 403, 'You do not manage this hostel.');
    }
}