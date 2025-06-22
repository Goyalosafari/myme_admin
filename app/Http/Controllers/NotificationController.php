<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function index()
    {
        $datas = $this->notification->all();
        return view('notification',compact('datas'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:255',
        ]);
        
        $data = new Notification();
        $data->message = $request->input('message');
        $data->general = 'yes';
        $data->status = 'yes';
        $data->save();

        return redirect('/notification')->with('success','Notification created successfully');
    }

    public function edit($id)
    {
        $notification = $this->notification->find($id);
        
        return response()->json($notification);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:255',
        ]);

        $data = $this->notification->find($id);
        $data->message = $request->input('message');
        $data->save();

        return redirect('/notification')->with('success','Notification updated successfully');
    }
    public function destroy($id)
    {
        $notification = $this->notification->find($id);
        $notification->delete();
        return redirect('/notification')->with('success','Notification Deleted successfully');
    }
}
