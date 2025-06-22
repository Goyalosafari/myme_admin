<?php

namespace App\Http\Controllers;

use App\Models\Coupen;
use Illuminate\Http\Request;

class CoupenController extends Controller
{
    protected $coupen;

    public function __construct(Coupen $coupen)
    {
        $this->coupen = $coupen;
    }

    public function index()
    {
        $coupenData = $this->coupen->all();
        return view('coupen',compact('coupenData'));
    }
    public function store(Request $request)
    {
        //validate input
        $request->validate([
            'title' => 'required|string|max:255'
        ]);
        
        //handle logic and store into db
        $coupen = new Coupen();
        $coupen->title = $request->input('title');
        $coupen->coupen_code = $request->input('coupen_code');
        $coupen->no_of_usage = $request->input('no_of_usage');
        $coupen->discount_type = $request->input('discount_type');
        $coupen->discount = $request->input('discount');
        $coupen->max_discount = $request->input('max_discount');
        $coupen->min_amount = $request->input('min_amount');
        $coupen->from_date = $request->input('from_date');
        $coupen->to_date = $request->input('to_date'); 
        $coupen->save();

        return redirect('/coupen')->with('success','Coupen created successfully');
    }

    public function edit($id)
    {
        $coupen = $this->coupen->find($id);
        
        return response()->json($coupen);
    }

    public function update(Request $request, $id)
    {
        //validate input
        $request->validate([
            'title' => 'required|string|max:255'
        ]);

        $coupen = $this->coupen->find($id);
        $coupen->title = $request->input('title');
        $coupen->coupen_code = $request->input('coupen_code');
        $coupen->no_of_usage = $request->input('no_of_usage');
        $coupen->discount_type = $request->input('discount_type');
        $coupen->discount = $request->input('discount');
        $coupen->max_discount = $request->input('max_discount');
        $coupen->min_amount = $request->input('min_amount');
        $coupen->from_date = $request->input('from_date');
        $coupen->to_date = $request->input('to_date'); 
        $coupen->save();

        return redirect('/coupen')->with('success','Coupen updated successfully');
    }
    public function destroy($id)
    {
        $coupen = $this->coupen->find($id);
        $coupen->delete();
        return redirect('/coupen')->with('success','Coupen Deleted successfully');
    }
}
