<?php

    namespace App\Jobs;

    class MailJob extends Job{

        /**
         * Create a new job instance.
         *
         * @return void
         */

        protected $data;

        public function __construct($data){
            
            $this->data = $data;

        }

        /**
         * Execute the job.
         *
         * @return void
         */

        public function server_parse2($socket, $expected_response)
        {   
            try {
                //code...

                $server_response = '';
                while (substr($server_response, 3, 1) != ' ')
                {
                    if (!($server_response = fgets($socket, 256)))
                        echo 'Couldn\'t get mail server response codes. Please contact the forum administrator.', __FILE__, __LINE__;
                }
            
                if (!(substr($server_response, 0, 3) == $expected_response))
                    echo 'Unable to send e-mail. Please contact the forum administrator with the following error message reported by the SMTP server: "'.$server_response.'"', __FILE__, __LINE__;

            } catch (\Throwable $th) {
                //throw $th;
            }
            
        } 
        
        public function handle(){
            
            foreach ($this->data as $mail_send) {
                
                $mail_send = (object) $mail_send;
                
                $subject = $mail_send->subject;
                $message = $mail_send->body;
                $to = $mail_send->email;

                $user = 'productoscatastrales';
                $pass = '$dcai2015$';
                $smtp_host = 'mail2.muniguate.com';
                $smtp_port = 25;
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=utf-8\r\n";

                try {
                    //code...

                    if (!($socket = fsockopen($smtp_host, $smtp_port, $errno, $errstr, 15)))

                        echo "Could not connect to smtp host '$smtp_host' ($errno) ($errstr)", __FILE__, __LINE__;

                    $this->server_parse2($socket, '220');
                    fwrite($socket, 'EHLO '.$smtp_host."\r\n");

                    $this->server_parse2($socket, '250');
                    fwrite($socket, 'AUTH LOGIN'."\r\n");
                    
                    $this->server_parse2($socket, '334'); 
                    fwrite($socket, base64_encode($user)."\r\n");
                    
                    $this->server_parse2($socket, '334'); 
                    fwrite($socket, base64_encode($pass)."\r\n");
                    
                    $this->server_parse2($socket, '235');
                    fwrite($socket, 'MAIL FROM: <productoscatastrales@muniguate.com>'."\r\n");
                    
                    $this->server_parse2($socket, '250');

                    fwrite($socket, 'RCPT TO: <'.$to.'>'."\r\n");
                    $this->server_parse2($socket, '250');

                    fwrite($socket, 'DATA'."\r\n");

                    $this->server_parse2($socket, '354'); 
                    fwrite($socket, 'Subject: '.$subject."\r\n".'To: <'.$to.'>'."\r\n".$headers."\r\n\r\n".$message."\r\n");
                    fwrite($socket, '.'."\r\n");
                    
                    $this->server_parse2($socket, '250'); 
                    fwrite($socket, 'QUIT'."\r\n");
                    
                    fclose($socket);
                    
                } catch (\Throwable $th) {
                    //throw $th;
                }
                

            }

        }
    }

?>
