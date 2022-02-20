<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Bill;
use App\Models\UserExpense;

class BillController extends Controller
{


    public function newExpense(Request $request){


        // Basic Validations
        $validator = Validator::make($request->all(), [
            "expense_type" => "required|in:0,1,2",  // 0 = equal, exact = 1, percent = 2
            "bill_paid_by" => "required",
            "expense" => "required|numeric|regex:/^\d*(\.\d{1,2})?$/",
            "about_bill" => "required|min:1|max:255",
            "friend_ids" => "required", // ids seperated by ,
            "amount_paid_by_friends" => "nullable" // values seperated by comma
        ]);


        if ($validator->fails() ){
            return response()->json([
                'status' => false,
                'errors' => $validator->messages(),
                'message' => "Invalid data"
            ]);
        }


        $expense_type = [ 'EQUAL' , 'EXACT' , 'PERCENT' ];



        $user_ids =  explode(",", $request->input('friend_ids') ) ;


        $user_ids = [ $request->input('bill_paid_by') , ...$user_ids ];  // array of user ids

        $amounts_paid_by_each_user = []; // array of amounts paid by each user




        // Checking if users ids are valid or not

        $count_of_ids = User::whereIn('id', $user_ids )->count();

        if($count_of_ids !== count($user_ids)){

            return response()->json([
                'status' => false,
                'errors' => [],
                'message' => "User IDs are invalid"
            ]);
        }


        // EQUAL

        if($request->input('expense_type') == '0'){



            $equal_amount = round( $request->input('expense') / count($user_ids) , 2); // upto 2 decimals

            $amounts_paid_by_each_user = array_fill( 0 ,  count($user_ids) , $equal_amount   );


            // 100 Rs for 3 Friends
            // Each one gets 33.33 , so first one has to pay 33.34

            if( $equal_amount * count($user_ids) != $request->input('expense')  ){

                $amount_to_be_paid_by_first_user =   round ( $equal_amount + (  $request->input('expense')  -   $equal_amount * count($user_ids)  )  , 2 );

                $amounts_paid_by_each_user[0] = $amount_to_be_paid_by_first_user;


            }




        }










        // Generate new Bill

        $bill = new Bill;
        $bill->about_bill = $request->input('about_bill');
        $bill->expense_type = $request->input('expense_type');
        $bill->expense = $request->input('expense');
        $bill->bill_paid_by = $request->input('bill_paid_by');
        $bill->save();



        // Insert how much each user paid to db

        $insert_data = [];

        foreach($user_ids as $user_id_index=>$user_id){

            $data = [
                "bill_id" => $bill->id,
                "user_id" => $user_id,
                "amount_paid" => $amounts_paid_by_each_user[$user_id_index]
            ];

            $insert_data[] = $data;

        }

        UserExpense::insert( $insert_data);







        return response()->json([
            'status' => false,
            'errors' => [],
            'data' => [
               $bill->id, $user_ids , $amounts_paid_by_each_user
            ]
        ]);






    }
}
