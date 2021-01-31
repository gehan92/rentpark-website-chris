<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SampleController extends Controller
{
    public function user_chat() {

    	return view('sample.user_chat')->with('user_id', 7)->with('provider_id', 1)->with('request_id', 1);
    }

    public function provider_chat() {

    	return view('sample.provider_chat')->with('user_id', 7)->with('provider_id', 1)->with('request_id', 1);
    }

    /**
     *
     * @method 
     *
     * @uses
     *
     * @created
     *
     * @updated
     *
     * @param
     *
     * @return
     * 
     */

    public function name() {

    	try {

    		$validator = Validator::make($request->all(), [

            ]);

            if($validator->fails()) {

                $error = implode(',', $validator->messages()->all());

                throw new Exception($error , 101);

            }

            DB::beginTransaction();

            DB::commit();

    		return $this->sendResponse($message, $code, $data);

    	} catch(Exception $e) {

    		DB::rollback();

    		return $this->sendError($e->getMessage(), $e->getCode());

    	}
    
    }
}
