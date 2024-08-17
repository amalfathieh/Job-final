<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Factory;

class FirebaseController extends Controller
{
    public $database;
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function index()
    {
        $firebase = (new Factory)->withServiceAccount(__DIR__.'/google_services.json');
        dd($firebase);
        // ->withDatabaseUri('firebase-adminsdk-jxw80@jobsapp-ea72c.iam.gserviceaccount.com')
    }
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $postData = [
            'user' => '',
            'token' => '',
        ];
        $postRef = $this->database->getReference('posts')->push($postData);
    }
    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }
    public function update(Request $request, string $id)
    {
        //
    }
    public function destroy(string $id)
    {
        //
    }
}
