<?php

namespace App\Http\Controllers;
use App\Models\TimeSlot;
use Illuminate\Http\Request;

class TimeslotController extends Controller
{
    protected $time_slot;

    public function __construct(TimeSlot $time_slot)
    {
        $this->time_slot = $time_slot;
    }

    public function index()
    {
        $datas = $this->time_slot->all();
        return view('time_slot',compact('datas'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'time_slot' => 'required|string|max:255',
            'time_start' => 'required|string|max:255',
            'time_end' => 'required|string|max:255'
        ]);
        
        $data = new TimeSlot();
        $data->time_slot = $request->input('time_slot');
        $data->cutoff = $request->input('cutoff');
        $data->time_start = $request->input('time_start');
        $data->time_end = $request->input('time_end');
        $data->ref = $request->input('ref');
        $data->ref1 = $request->input('ref1');
        $data->status = 'y';
        $data->save();

        return redirect('/timeslot')->with('success','Timeslot created successfully');
    }

    public function edit($id)
    {
        $time_slot = $this->time_slot->find($id);
        
        return response()->json($time_slot);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'time_slot' => 'required|string|max:255',
            'time_start' => 'required|string|max:255',
            'time_end' => 'required|string|max:255'
        ]);

        $data = $this->time_slot->find($id);
        $data->time_slot = $request->input('time_slot');
        $data->cutoff = $request->input('cutoff');
        $data->time_start = $request->input('time_start');
        $data->time_end = $request->input('time_end');
        $data->ref = $request->input('ref');
        $data->ref1 = $request->input('ref1');
        $data->save();

        return redirect('/timeslot')->with('success','Timeslot updated successfully');
    }
    public function destroy($id)
    {
        $time_slot = $this->time_slot->find($id);
        $time_slot->delete();
        return redirect('/timeslot')->with('success','Timeslot Deleted successfully');
    }
}
