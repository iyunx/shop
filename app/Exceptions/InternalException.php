<?php

namespace App\Exceptions;


use Exception;
use Illuminate\Http\Request;

//系统内容的错误 比如数据库链接错误等
class InternalException extends Exception
{
    protected $msgForUser;
    public function __construct(string $message, string $msgForUser='系统内部错误', int $code=500)
    {
        parent::__construct($message, $code);
        $this->message = $msgForUser;
    }

    public function render(Request $request)
    {
        if($request->expectsJson()) return response()->json(['msg'=>$this->message], $this->code);

        return view('page.error', ['msg'=>$this->msgForUser]);
    }
}
