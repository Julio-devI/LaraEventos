<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::all();

        return view('welcome', ['events' => $events]);
    }

    public function create()
    {
        return view('events.create');
    }


    /**
     * Store a new event
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $event = new Event;

        $event->title = $request->input('title');
        $event->city = $request->input('city');
        $event->private = $request->input('private');
        $event->description = $request->input('description');

        $event->save();

        return redirect('/')->with('msg', 'evento criado com sucesso!');
    }
}