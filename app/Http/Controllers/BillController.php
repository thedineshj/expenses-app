<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Bill;
use App\Models\UserExpense;
use App\Models\BalanceRecord;


class BillController extends Controller
{


    public function newExpense(Request $request){


        // Basic Validations
        $validator = Validator::make($request->all(), [
            "expense_type" => "required|in:0,1,2",  // 0 = equal, exact = 1, percent = 2
            "bill_paid_by" => "required",
            "expense" => "required|numeric|regex:/^\d*(\.\d{1,2})?$/",
            "about_bill" => "required|min:1|max:255",
            "friend_ids" => "required", // ids seperated by ' , ' ( ids of people to whom bill is being paid )
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

        $count_of_ids = User::whereIn('id', $user_ids)->count();

        if($count_of_ids !== count(  array_unique( $user_ids))  ){

            return response()->json([
                'status' => false,
                'errors' => [],
                'message' => "User IDs are invalid"
            ]);
        }




        // EXPENSE TYPE = EQUAL

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


        // We need percentages or exact values for Expense type 2 & 1

        if( ($request->input('expense_type') == '1' || $request->input('expense_type') == '2' )&&  empty( $request->input("amount_paid_by_friends") )  ){

            return response()->json([
                'status' => false,
                'errors' => [],
                'message' => "Amount / percentage of amount paid to friends can not be empty "
            ]);

        }


        // EXPENSE TYPE = EXACT

        if($request->input('expense_type') == '1'){



            $exact_amounts = explode(",", $request->input('amount_paid_by_friends') )  ;

            // If sum doesn't match total bill
            if( array_sum($exact_amounts) !=   $request->input('expense') ){

                return response()->json([
                    'status' => false,
                    'errors' => [],
                    'message' => "Sum of amounts is not equal to the total bill"
                ]);

            }


            $user_ids =  explode(",", $request->input('friend_ids') ) ;
            $amounts_paid_by_each_user = $exact_amounts;


            if( count($user_ids) != count($exact_amounts) ){

                return response()->json([
                    'status' => false,
                    'errors' => [],
                    'message' => "Counts of amounts supplied is not equal to count of friends"
                ]);

            }



        }



        // EXPENSE TYPE = PERCENT

        if($request->input('expense_type') == '2'){

            $percents = explode(",", $request->input('amount_paid_by_friends') )  ;

            if( array_sum($percents) != 100 ){

                // if sum of percentages is not equal to 100
                return response()->json([
                    'status' => false,
                    'errors' => [],
                    'message' => "Sum of percentages is not equal to the total bill (100%)"
                ]);
            }


            $user_ids =  explode(",", $request->input('friend_ids') ) ;
            $amounts_paid_by_each_user = [];

            foreach($percents as $percent){

                $amounts_paid_by_each_user[] = $request->input('expense')  * ( $percent / 100 );  // Calculate amount from percentage

            }

            if( count($user_ids) != count($amounts_paid_by_each_user) ){

                return response()->json([
                    'status' => false,
                    'errors' => [],
                    'message' => "Counts of amounts supplied is not equal to count of friends"
                ]);

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
                "amount_paid" => round( $amounts_paid_by_each_user[$user_id_index] , 2)
            ];

            $insert_data[] = $data;


            // Update balances for all users

            if(  $user_id != $request->input('bill_paid_by')  ){

                $query = BalanceRecord::query();
                $query->where([
                    ['lender_id','=',$request->input('bill_paid_by')] ,
                    ['borrower_id','=',$user_id ] ,

                ]);


                $balanceRecord = $query->first();

                // Record already exists ,  update existing record
                if($balanceRecord){

                    $balanceRecord->balance = round( $balanceRecord->balance + $data["amount_paid"] , 2);
                    $balanceRecord->save();
                }

                if(!$balanceRecord){

                    $balanceRecord = new BalanceRecord;

                    $balanceRecord->lender_id = $request->input('bill_paid_by');
                    $balanceRecord->borrower_id = $user_id ;
                    $balanceRecord->balance = $data["amount_paid"];
                    $balanceRecord->save();
                }






            }



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
