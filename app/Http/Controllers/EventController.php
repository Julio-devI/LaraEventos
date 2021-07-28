<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Event;
use App\Models\User;

class EventController extends Controller
{
    public function index()
    {
        $search = request('search');

        if ($search) {

            $events = Event::where([
                ['title', 'like', '%' . $search . '$']
            ])->get();
        } else {
            $events = Event::all();
        }

        return view('welcome', ['events' => $events, 'search' => $search]);
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

        $event->date = $request->input('date');
        $event->title = $request->input('title');
        $event->city = $request->input('city');
        $event->private = $request->input('private');
        $event->description = $request->input('description');
        $event->items = $request->input('items');
        $event->image = $request->input('image');

        //image upload
        if ($request->image && $request->file('image')->isValid()) {

            $requestImage = $request->image;

            $extension = $requestImage->extension();

            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;

            $request->image->move(public_path('img/events'), $imageName);

            $event->image = $imageName;
        }

        $user = auth()->user();
        $event->user_id = $user->id;

        $event->save();

        return redirect('/')->with('msg', 'evento criado com sucesso!');
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);

        $user = auth()->user();
        $hasUserJoined = false;

        if ($user) {

            $userEvents = $user->participants->toArray();

            foreach ($userEvents as $userEvent) {
                if ($userEvent['id'] == $id) {
                    $hasUserJoined = true;
                };
            };
        }

        $eventOwner = User::where('id', $event->user_id)->first()->toArray();

        return view('events.show', ['event' => $event, 'eventOwner' => $eventOwner, 'hasUserJoined' => $hasUserJoined]);
    }

    public function dashboard()
    {
        $user = auth()->user();

        $events = $user->events;

        $participants = $user->participants;

        return view(
            'events.dashboard',
            ['events' => $events, 'participants' => $participants]
        );
    }

    public function destroy($id)
    {

        Event::findOrFail($id)->delete();

        return redirect('/dashboard')->with('msg', 'Evento excluído com sucesso');
    }

    public function edit($id)
    {
        $user = auth()->user();

        $event = Event::findOrFail($id);

        //segurança de rota----------------
        if ($user->id != $event->user->id) {
            return redirect('/dashboard');
        }
        //---------------------------------

        return view('events.edit', ['event' => $event]);
    }

    public function update(Request $request)
    {
        $data = $request->all();

        if ($request->image && $request->file('image')->isValid()) {

            $requestImage = $request->image;

            $extension = $requestImage->extension();

            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;

            $request->image->move(public_path('img/events'), $imageName);

            $data['image'] = $imageName;
        }


        Event::findOrFail($request->id)->update($data);

        return redirect('/dashboard')->with('msg', 'Evento editado com sucesso!');
    }

    public function joinEvent($id)
    {

        $user = auth()->user();

        $user->participants()->attach($id);

        $event = Event::findOrFail($id);

        return redirect('/dashboard')->with('msg', 'Sua presença foi confirmada no evento: ' . $event->title);
    }

    public function leaveEvent($id)
    {
        $user = auth()->user();

        $user->participants()->detach($id);

        $event = Event::findOrFail($id);

        return redirect('/dashboard')->with('msg', 'Sua presença foi retirada no evento: ' . $event->title);
    }
}