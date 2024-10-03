<?php
namespace App\Http\Controllers\Api\V1;

use App\Models\Invoice;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use App\Filters\V1\InvoiceFilter;
use App\Http\Requests\V1\StoreInvoiceRequest;
use App\Http\Requests\V1\BulkStoreInvoiceRequest;
use App\Http\Resources\V1\InvoiceResource;
use App\Http\Requests\V1\UpdateInvoiceRequest;
use App\Http\Resources\V1\InvoiceCollection;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $filter = new InvoiceFilter();
        $queryItems = $filter->transform($request);
       
        if(count($queryItems) == 0){
            return new InvoiceCollection(Invoice::paginate());
        }else{
            $invoices = Invoice::where($queryItems)->paginate();
            return new InvoiceCollection($invoices->appends($request->query()));
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreInvoiceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreInvoiceRequest $request)
    {
        $data = $request->all();
        return new InvoiceResource(Invoice::create($data));
    }

    public function bulkStore(BulkStoreInvoiceRequest $request)
    {
        $bulk = collect($request->all())->map(function($arr, $key)
        {
            return Arr::except($arr, ['customerId','billedDate', 'paidDate']);
        });
        Invoice::insert($bulk->toArray());
    }

    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        return new InvoiceResource($invoice);
    }

   
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateInvoiceRequest  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        $invoice->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
    }
}