<?php

namespace App\Http\Controllers;

use App\Models\Pincode;
use Illuminate\Http\Request;

class PincodeController extends Controller
{
    protected $pincode;

    public function __construct(Pincode $pincode)
    {
        $this->pincode = $pincode;
    }

    public function index()
    {
        $pincodeData = $this->pincode->all();
        return view('pincode',compact('pincodeData'));
    }
    public function store(Request $request)
    {
        //validate input
        $request->validate([
            'pincode' => 'required|string|max:255'
        ]);
        
        //handle logic and store into db
        $pincode = new Pincode();
        $pincode->pincode = $request->input('pincode');
        $pincode->place_name = $request->input('place_name');
        $pincode->district = $request->input('district');
        $pincode->state = $request->input('state');
        $pincode->delivery_fee = $request->input('delivery_fee');
        $pincode->other_fee = $request->input('other_fee');

        $pincode->save();

        return redirect('/pincode')->with('success','Pincode created successfully');
    }

    public function edit($id)
    {
        $pincode = $this->pincode->find($id);
        
        return response()->json($pincode);
    }

    public function update(Request $request, $id)
    {
        //validate input
        $request->validate([
            'pincode' => 'required|string|max:255'
        ]);

        $pincode = $this->pincode->find($id);
        $pincode->pincode = $request->input('pincode');
        $pincode->place_name = $request->input('place_name');
        $pincode->district = $request->input('district');
        $pincode->state = $request->input('state');
        $pincode->delivery_fee = $request->input('delivery_fee');
        $pincode->other_fee = $request->input('other_fee');
        $pincode->save();

        return redirect('/pincode')->with('success','Pincode updated successfully');
    }
    public function destroy($id)
    {
        $pincode = $this->pincode->find($id);
        $pincode->delete();
        return redirect('/pincode')->with('success','Pincode Deleted successfully');
    }
}
