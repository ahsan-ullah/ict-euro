<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customer;

class CustomerController extends Controller
{
    protected $customer;
    protected $company;
    protected $shop;

    public function __construct(Customer $customer)
    {
        $this->middleware('auth:api');
        $this->customer = $customer;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index()
    {
        $customers =  $this->customer::with('installments')->get();
        if (!empty($customers)) {
            return response()->json([
                'status' => true,
                'errors' => [],
                'data' => $customers,
                'message' => "Customers successfully loaded"
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => [],
                'data' => $customers,
                'message' => "Customers not found"
            ]);
        }
    }

    public function searchByPhoneNameEmail($query)
    {
        $companies = [];
        // if (auth()->user()->can('show customers')) {
            // $companies = $this->company->getAll();
        // }

        $customers = $this->customer->searchByPhoneNameEmail($query);
        if (!empty($customers)) {
            return response()->json([
                'status' => true,
                'errors' => [],
                'data' => $customers,
                'message' => "Customers successfully loaded"
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => [],
                'data' => $customers,
                'message' => "Customers not found"
            ]);
        }
    }


   /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:20',
            'address' => 'required|string|max:20'
        ]);
        $customer = $this->customer::create($request->all());
        return response()->json([
            'status' => true,
            'data' => $customer,
            'errors' => '', 
            'message' => $request->name." Category has been successfully created",
        ]);

    }

   /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */

     public function show($id)
    {
        $customer = $this->customer::with('installments')
            ->where('id', $id)
            ->first();
        if (!empty($customer)) {
            return response()->json([
                'status' => true,
                'errors' => [],
                'data' => $customer,
                'message' => "Customer successfully loaded"
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => [],
                'data' => $customer,
                'message' => "Customer not found"
            ]);
        }
    }
    

   /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request)
    {
        $customer = $this->customer->update($request->id, $request);
        return response()->json([
            'status' => true,
            'data' => $customer,
            'errors' => '', 
            'message' => $request->name." has been successfully updated",
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request)
    {
        if($this->customer->delete($request->id)){
            return response()->json([
                'status' => true,
                'data' => [],
                'errors' => '', 
                'message' => "Customer has been successfully deleted",
            ]);
        }else{
            return response()->json([
                'status' => false,
                'data' => [],
                'errors' => '', 
                'message' => "An error has been occurred",
            ]);
        }
        
    }
}
