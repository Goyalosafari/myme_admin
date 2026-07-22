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
        $datas = $this->notification->latest()->paginate(20);
        return view('notification', compact('datas'));
    }

    private function rules(): array
    {
        return [
            'message' => 'required|string|max:255',
        ];
    }

    private function messages(): array
    {
        return [
            'message.required' => 'Notification message is required.',
            'message.max'      => 'Message must not exceed 255 characters.',
        ];
    }

    public function store(Request $request)
    {
        $request->validate($this->rules(), $this->messages());

        $data = new Notification();
        $data->message = $request->input('message');
        $data->general = 'yes';
        $data->status = 'yes';
        $data->save();

        return redirect()->route('notification.index')->with('success', 'Notification created successfully');
    }

    public function edit($id)
    {
        return response()->json($this->notification->find($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate($this->rules(), $this->messages());

        $data = $this->notification->find($id);
        $data->message = $request->input('message');
        $data->save();

        return redirect()->route('notification.index')->with('success', 'Notification updated successfully');
    }

    public function destroy($id)
    {
        $this->notification->find($id)->delete();
        return redirect()->route('notification.index')->with('success', 'Notification deleted successfully');
    }
}
