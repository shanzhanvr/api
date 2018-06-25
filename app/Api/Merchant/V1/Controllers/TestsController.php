<?php
namespace App\Api\V1\Controllers;


use App\Api\Transformers\TestsTransformer;
use App\Account;

class TestsController extends BaseController {
    public function index(){

        $tests = Account::all();
        return $this->collection($tests, new TestsTransformer());
    }
    public function show($id){
        $test = Account::find($id);
        if (!$test) {
            return $this->response->errorNotFound('Test not found');
        }
        return $this->item($test, new TestsTransformer());
    }
}