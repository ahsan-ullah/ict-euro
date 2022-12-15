<?php
namespace App\Http\Controllers;

use Stripe\Charge;
use Stripe\Stripe;
use App\Models\Installment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InstallmentController extends Controller
{
    protected $installment;

    public function __construct(Installment $installment)
    {
        $this->middleware('auth:api');
        $this->installment = $installment;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index()
    {
        $installments =  $this->installment::with('customer')->get();
        if (!empty($installments)) {
            return response()->json([
                'status' => true,
                'errors' => [],
                'data' => $installments,
                'message' => "Customers successfully loaded"
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => [],
                'data' => $installments,
                'message' => "Customers not found"
            ]);
        }
    }

    public function searchByPhoneNameEmail($query)
    {
        $installments = $this->installment->searchByPhoneNameEmail($query);
        if (!empty($customers)) {
            return response()->json([
                'status' => true,
                'errors' => [],
                'data' => $installments,
                'message' => "installments successfully loaded"
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => [],
                'data' => $installments,
                'message' => "installments not found"
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
        // return $request;
        // $request->validate([
        //     'expire_date' => 'required|max:100',
        //     'amount' => 'required|max:20',
        //     'note' => 'string|max:200'
        // ]);
        
        if (count($request->all()) == count($request->all(), COUNT_RECURSIVE)) 
        {
            $installment = $this->installment::create($request->all());
        }
        else
        {
            foreach ($request->all() as $key => $installment) {
                $model = new $this->installment;
                $model->customer_id = $installment['customer_id'];
                $model->expire_date = date('Y-m-d', strtotime( $installment['expire_date']));
                $model->amount = $installment['amount'];
                $model->note = $installment['note'];
                // return $model;
                $model->save();
            }
        }
        return response()->json([
            'status' => true,
            'data' => [],
            'errors' => '', 
            'message' => "A new isntallment has been successfully created",
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

    public function payment(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    
        Charge::create ([
                "amount" => $request->amount,
                "currency" => "euro",
                "source" => $request->stripeToken,
                "description" => "Test payment from ict-euro.com." 
        ]);

        return response()->json([
            'status' => true,
            'data' => [],
            'errors' => '', 
            'message' => "An installment payment has been successfully paid",
        ]);
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
