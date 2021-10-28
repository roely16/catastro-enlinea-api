<?php 

    namespace App\Http\Controllers;
            
    use Illuminate\Http\Request;

    use App\Jobs\MailJob;

    class MailController extends Controller{

        public function test_mail(){

            \Queue::push(new MailJob());

        }

    }

?>