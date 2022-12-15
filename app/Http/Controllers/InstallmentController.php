<?php
namespace App\Http\Controllers;

use Stripe\Charge;
use Stripe\Stripe;
use App\Models\Payment;
use Stripe\StripeClient;
use App\Models\Installment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InstallmentController extends Controller
{
    protected $installment;

    public function __construct(Installment $installment, Payment $payment)
    {
        $this->middleware('auth:api');
        $this->installment = $installment;
        $this->payment = $payment;
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
            'message' => "A new installment has been successfully created",
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
        $installment = $this->installment::with('customer', 'payment')
            ->where('id', $id)
            ->first();
        if (!empty($installment)) {
            return response()->json([
                'status' => true,
                'errors' => [],
                'data' => $installment,
                'message' => "Installment successfully loaded"
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => [],
                'data' => $installment,
                'message' => "Installment not found"
            ]);
        }
    }

    public function payment(Request $request)
    {
        $installment = $this->getById($request->id);
        if ($installment) 
        {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            try {
                $tokenObj = $this->stripeToken($request);
                $trxInfo = Charge::create ([
                    "amount" => $installment->amount,
                    "currency" => "usd",
                    "source" => $tokenObj['id'],
                    "description" => "Test payment from ict-euro.com." 
                ]);
                
                $stripeInfo = [];
                $stripeInfo['id'] = $trxInfo->id;
                $stripeInfo['balance_transaction'] = $trxInfo->balance_transaction;
                $stripeInfo['calculated_statement_descriptor'] = $trxInfo->balance_calculated_statement_descriptortransaction;
                $stripeInfo['payment_method'] = $trxInfo->payment_method;
                $stripeInfo['payment_method_details'] = $trxInfo->payment_method_details;

                $payment = $this->payment;
                $payment->installment_id = $installment->id;
                $payment->trx_number = $trxInfo->balance_transaction;
                $payment->customer_id = $installment->customer_id;
                $payment->method = $trxInfo->payment_method;
                $payment->currency = $trxInfo->currency;
                $payment->payment_date = date('Y-m-d');
                $payment->description = json_encode($stripeInfo);
                $payment->status = 1;
                $payment->save();
                $installment->status = 1;
                $installment->update();
                return response()->json([
                    'status' => true,
                    'data' => $payment,
                    'errors' => '', 
                    'message' => "An installment payment has been successfully paid",
                ]);
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => false,
                    'data' => [],
                    'errors' => $th->getMessage(), 
                    'message' => $th->getMessage()
                ]);
            }
            
            
        }
        else
        {
            return response()->json([
                'status' => false,
                'data' => [],
                'errors' => '', 
                'message' => "installment is invalid",
            ]);
        }
        
    }

    public function stripeToken($cardInfo)
    {
        $stripe = new StripeClient(env('STRIPE_SECRET'));
          return $stripe->tokens->create([
            'card' => [
              'number' => $cardInfo->card_number,
              'exp_month' => $cardInfo->exp_month,
              'exp_year' => $cardInfo->exp_year,
              'cvc' => $cardInfo->cvc,
            ],
          ]);
    }

    public function getById($id)
    {
        return $this->installment::with('customer', 'payment')
            ->where('id', $id)
            ->first();
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
