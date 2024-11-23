<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Http\Resources\V1\InvoiceResource;
use App\Http\Requests\InvoiceRequest;
use Illuminate\Support\Facades\Validator;
use App\Traits\HttpResponses;

class InvoiceController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth:sanctum')->only(['store','update','destroy']);
    }
    public function index(Request $request)
    {

        //return InvoiceResource::collection(Invoice::with('user')->get());
        return (new Invoice())->filter($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'type' => 'required',
            'value' => 'required|numeric|between:1,9999.99',
            'paid' => 'required|boolean|between:0,1',
            'payment_date' => 'nullable|date',
        ]);
        if($validator->fails()){
            return $this->error('Data Invalid',422,$validator->errors());
        }

        $created = Invoice::create($request->all());

        if($created){
            return $this->response('Invoice created',200,new InvoiceResource($created->load('user')));
        }
        return $this->error('Invoice not created',500);
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        return new InvoiceResource($invoice);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validator = Validator::make($request->all(),[
            'user_id' => 'required|exists:users,id',
            'type' => 'required|max:1|in:B,C,P',
            'value' => 'required|numeric|between:1,9999.99',
            'paid' => 'required|boolean|between:0,1',
            'payment_date' => 'nullable|date_format:Y-m-d H:i:s',
            
        ]);

        if($validator->fails()){
            return $this->error('Validation failed',422,$validator->errors());
        }

        $validated = $validator->validated();

        $updated = $invoice->update([
            'user_id' => $validated['user_id'],
            'type' => $validated['type'],
            'value' => $validated['value'],
            'paid' => $validated['paid'],
            'payment_date' => $validated['paid'] ? $validated['payment_date']: null,
        ]);
        if($updated){
            return $this->response('Invoice updated',200,new InvoiceResource($invoice->load('user')));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $deleted = $invoice->delete();

        if($deleted){
            return $this->response('Invoice deleted',200);
        }
        return $this->error('Invoice not deleted',500);
    }
}
