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
        $pincodeData = $this->pincode->latest()->paginate(20);
        return view('pincode', compact('pincodeData'));
    }

    private function rules(): array
    {
        return [
            'pincode'      => 'required|string|max:10',
            'place_name'   => 'required|string|max:100',
            'district'     => 'required|string|max:100',
            'state'        => 'required|string|max:100',
            'delivery_fee' => 'required|numeric|min:0',
            'other_fee'    => 'required|numeric|min:0',
        ];
    }

    private function messages(): array
    {
        return [
            'pincode.required'       => 'Pincode is required.',
            'pincode.max'            => 'Pincode must not exceed 10 characters.',
            'place_name.required'    => 'Place name is required.',
            'place_name.max'         => 'Place name must not exceed 100 characters.',
            'district.required'      => 'District is required.',
            'district.max'           => 'District must not exceed 100 characters.',
            'state.required'         => 'State is required.',
            'state.max'              => 'State must not exceed 100 characters.',
            'delivery_fee.required'  => 'Delivery fee is required.',
            'delivery_fee.numeric'   => 'Delivery fee must be a valid number.',
            'delivery_fee.min'       => 'Delivery fee cannot be negative.',
            'other_fee.required'     => 'Other fee is required.',
            'other_fee.numeric'      => 'Other fee must be a valid number.',
            'other_fee.min'          => 'Other fee cannot be negative.',
        ];
    }

    public function store(Request $request)
    {
        $request->validate($this->rules(), $this->messages());

        $pincode = new Pincode();
        $pincode->pincode = $request->input('pincode');
        $pincode->place_name = $request->input('place_name');
        $pincode->district = $request->input('district');
        $pincode->state = $request->input('state');
        $pincode->delivery_fee = $request->input('delivery_fee') ?? 0;
        $pincode->other_fee = $request->input('other_fee') ?? 0;
        $pincode->save();

        return redirect()->route('pincode.index')->with('success', 'Pincode created successfully');
    }

    public function edit($id)
    {
        return response()->json($this->pincode->find($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate($this->rules(), $this->messages());

        $pincode = $this->pincode->find($id);
        $pincode->pincode = $request->input('pincode');
        $pincode->place_name = $request->input('place_name');
        $pincode->district = $request->input('district');
        $pincode->state = $request->input('state');
        $pincode->delivery_fee = $request->input('delivery_fee') ?? 0;
        $pincode->other_fee = $request->input('other_fee') ?? 0;
        $pincode->save();

        return redirect()->route('pincode.index')->with('success', 'Pincode updated successfully');
    }

    public function destroy($id)
    {
        $this->pincode->find($id)->delete();
        return redirect()->route('pincode.index')->with('success', 'Pincode deleted successfully');
    }
}
