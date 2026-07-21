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
        $coupenData = $this->coupen->latest()->paginate(20);
        return view('coupen', compact('coupenData'));
    }

    private function rules(): array
    {
        return [
            'title'         => 'required|string|max:255',
            'coupen_code'   => 'required|string|max:100',
            'no_of_usage'   => 'required|integer|min:1',
            'discount_type' => 'required|in:flat,percentage',
            'discount'      => 'required|numeric|min:0',
            'max_discount'  => 'required|numeric|min:0',
            'min_amount'    => 'required|numeric|min:0',
            'from_date'     => 'required|date',
            'to_date'       => 'required|date|after_or_equal:from_date',
        ];
    }

    private function messages(): array
    {
        return [
            'title.required'          => 'Coupon title is required.',
            'title.max'               => 'Title must not exceed 255 characters.',
            'coupen_code.required'    => 'Coupon code is required.',
            'coupen_code.max'         => 'Coupon code must not exceed 100 characters.',
            'no_of_usage.required'    => 'Number of usages is required.',
            'no_of_usage.integer'     => 'Number of usages must be a whole number.',
            'no_of_usage.min'         => 'Number of usages must be at least 1.',
            'discount_type.required'  => 'Please select a discount type.',
            'discount_type.in'        => 'Discount type must be Flat or Percentage.',
            'discount.required'       => 'Discount value is required.',
            'discount.numeric'        => 'Discount must be a valid number.',
            'discount.min'            => 'Discount cannot be negative.',
            'max_discount.required'   => 'Maximum discount is required.',
            'max_discount.numeric'    => 'Max discount must be a valid number.',
            'max_discount.min'        => 'Max discount cannot be negative.',
            'min_amount.required'     => 'Minimum amount is required.',
            'min_amount.numeric'      => 'Minimum amount must be a valid number.',
            'min_amount.min'          => 'Minimum amount cannot be negative.',
            'from_date.required'      => 'From date is required.',
            'from_date.date'          => 'From date must be a valid date.',
            'to_date.required'        => 'To date is required.',
            'to_date.date'            => 'To date must be a valid date.',
            'to_date.after_or_equal'  => 'To date must be on or after the from date.',
        ];
    }

    public function store(Request $request)
    {
        $request->validate($this->rules(), $this->messages());

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

        return redirect()->route('coupen.index')->with('success', 'Coupon created successfully');
    }

    public function edit($id)
    {
        return response()->json($this->coupen->find($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate($this->rules(), $this->messages());

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

        return redirect()->route('coupen.index')->with('success', 'Coupon updated successfully');
    }

    public function destroy($id)
    {
        $this->coupen->find($id)->delete();
        return redirect()->route('coupen.index')->with('success', 'Coupon deleted successfully');
    }
}
