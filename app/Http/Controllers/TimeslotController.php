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
<<<<<<< HEAD
        $datas = $this->time_slot->all();
        return view('time_slot', compact('datas'));
    }

=======
        $datas = $this->time_slot->latest()->paginate(20);
        return view('time_slot', compact('datas'));
    }

    private function rules(): array
    {
        return [
            'time_slot'  => 'required|string|max:255',
            'time_start' => 'required|date_format:H:i',
            'time_end'   => 'required|date_format:H:i|after:time_start',
            'cutoff'     => 'nullable|string|max:255',
            'ref'        => 'nullable|string|max:255',
            'ref1'       => 'nullable|integer|min:0',
        ];
    }

    private function messages(): array
    {
        return [
            'time_slot.required'   => 'Time slot label is required.',
            'time_slot.max'        => 'Time slot label must not exceed 255 characters.',
            'time_start.required'  => 'Start time is required.',
            'time_start.date_format' => 'Start time must be in HH:MM format (e.g. 09:00).',
            'time_end.required'    => 'End time is required.',
            'time_end.date_format' => 'End time must be in HH:MM format (e.g. 17:00).',
            'time_end.after'       => 'End time must be after start time.',
            'ref1.integer'         => 'Ref1 must be a whole number.',
            'ref1.min'             => 'Ref1 cannot be negative.',
        ];
    }

>>>>>>> main
    public function store(Request $request)
    {
        $request->validate($this->rules(), $this->messages());

        $data = new TimeSlot();
        $data->time_slot = $request->input('time_slot');
        $data->cutoff = $request->input('cutoff');
        $data->time_start = $request->input('time_start');
        $data->time_end = $request->input('time_end');
        $data->ref = $request->input('ref');
        $data->ref1 = $request->input('ref1');
        $data->status = 'y';
        $data->save();

<<<<<<< HEAD
        return redirect('/timeslot')->with('success', 'Timeslot created successfully');
=======
        return redirect()->route('timeslot.index')->with('success', 'Time slot created successfully');
>>>>>>> main
    }

    public function edit($id)
    {
<<<<<<< HEAD
        $time_slot = $this->time_slot->find($id);
        return response()->json($time_slot);
=======
        return response()->json($this->time_slot->find($id));
>>>>>>> main
    }

    public function update(Request $request, $id)
    {
        $request->validate($this->rules(), $this->messages());

        $data = $this->time_slot->find($id);
        $data->time_slot = $request->input('time_slot');
        $data->cutoff = $request->input('cutoff');
        $data->time_start = $request->input('time_start');
        $data->time_end = $request->input('time_end');
        $data->ref = $request->input('ref');
        $data->ref1 = $request->input('ref1');
        $data->save();

<<<<<<< HEAD
        return redirect('/timeslot')->with('success', 'Timeslot updated successfully');
=======
        return redirect()->route('timeslot.index')->with('success', 'Time slot updated successfully');
>>>>>>> main
    }

    public function destroy($id)
    {
<<<<<<< HEAD
        $time_slot = $this->time_slot->find($id);
        $time_slot->delete();
        return redirect('/timeslot')->with('success', 'Timeslot Deleted successfully');
=======
        $this->time_slot->find($id)->delete();
        return redirect()->route('timeslot.index')->with('success', 'Time slot deleted successfully');
>>>>>>> main
    }

    public function toggleStatus(Request $request, $id)
    {
        $timeSlot = $this->time_slot->find($id);
        if ($timeSlot) {
            $timeSlot->status = $request->input('status');
            $timeSlot->save();
            return response()->json(['status' => $timeSlot->status]);
        }
        return response()->json(['error' => 'Time slot not found'], 404);
    }
}