<?php

namespace App\Http\Controllers\Assistant;

use Illuminate\Http\Request;
use App\Http\Requests\Events as modelRequest;
use App\Http\Controllers\Controller;
use App\Models\Events as Model;
use App\Models\EventUsers;
use App\Models\EventMessages;
use App\Models\CongratulationMessages;
use Illuminate\Support\Facades\Auth;
use Response;

class EventsController extends Controller
{
    private $view = 'assistant.events.';
    private $redirect = 'assistant_panel/events';




    public function show_pdf($id)
    {

        $Item = Model::findOrFail($id);

        $filename = 'file';

        $path = $Item->file;

        return Response::make(file_get_contents($path), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"'
        ]);

    }

    public function index()
    {

        $admin = Auth::guard('assistant')->user();
        $Item = Model::where('assistant_id',$admin->id)->get(['id','title','address','file','user_id','first_name' , 'last_name','date']);
        return view($this->view . 'index', compact('Item'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->view . 'create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(modelRequest $request)
    {

        $Item = Model::create($this->gteInput($request, null));
        return redirect($this->redirect)->with('success', trans('home.save_msg'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $admin = Auth::guard('assistant')->user();

        $Item = Model::where('assistant_id',$admin->id)->findOrFail($id);
      	//dd($Item);
        return view($this->view . 'edit', [ 'Item' => $Item ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(modelRequest $request, $id)
    {
        $Item = Model::findOrFail($id);
        $Item->update($this->gteInput($request, $Item));

        return redirect()->back()->with('info', trans('home.update_msg'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Item = Model::findOrFail($id);

      	$mobiles = EventUsers::where('event_id',$Item->id)->pluck('mobile')->toArray();

      	EventMessages::whereIn('mobile',$mobiles)->delete();
        CongratulationMessages::whereIn('mobile',$mobiles)->delete();

        $Item->delete();

        return redirect($this->redirect)->with('error', trans('home.delete_msg'));
    }


    private function gteInput($request, $modelClass)
    {

        $input = $request->only([
            'title','lat', 'long', 'address', 'showing_qr', 'user_id' ,
            'have_reminder' , 'date', 'assistant_id'
        ]);

        if(! isset($modelClass)) {
            $input['add_by'] = 'admin';
        } else {
            $input['add_by'] = $modelClass->add_by;
        }

        $path = 'images';

        if($request->file('file') != null) {

            $extension = $request->file('file')->extension();
            $filename = uniqid() . '.' . $extension;
            $request->file('file')->move($path, $filename);

            $input['file'] = $filename;

        }

        return  $input;
    }


}
