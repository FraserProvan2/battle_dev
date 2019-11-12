<?php

namespace App\Http\Controllers;

use App\Invite;
use Illuminate\Http\Request;

class InvitesController extends Controller
{
    /**
     * Gets all invites in the Db
     * 
     * @return Object Instances of Invite
     */
    public function getAll()
    {
        return Invite::all();
    }

}
