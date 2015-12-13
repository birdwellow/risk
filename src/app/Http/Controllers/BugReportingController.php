<?php namespace Game\Http\Controllers;

use Game\Handlers\Messages\SuccessFeedback;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class BugReportingController extends Controller {
        

        public function __construct(){
                
	}

        
	public function report() {
            
                $contactinfo = Request::get("contactinfo");
                $description = Request::get("description");
                Log::info($contactinfo);
                Log::info($description);
                Mail::send('emails.bugreport',
                    [
                        'contactinfo' => $contactinfo,
                        'description' => $description
                    ], function($message){
                        $message->to('florian.vogel84@gmx.net');
                        $message->subject('Bugreport');
                    }
                );
                
                return redirect()
                            ->back()
                            ->with("message", new SuccessFeedback("message.bugreporter.report.sent"));
            
	}

}
