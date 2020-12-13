<?php


namespace Applications\JsonRpc;


class ErrorHandler
{
    public static function handle($errno, $errstr ,$errfile, $errline){
// $errstr may need to be escaped:
        $errstr = htmlspecialchars($errstr);

        $error_msg = '';
        switch ($errno) {
            case E_USER_ERROR:
                $error_msg.="ERROR [$errno] $errstr\n";
                //$error_msg.= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")\n";
                //$error_msg.= "Aborting...\n";
                //exit(1);
                break;

            case E_USER_WARNING:
                $error_msg.= "WARNING [$errno] $errstr\n";
                break;

            case E_USER_NOTICE:
                $error_msg.= "NOTICE [$errno] $errstr\n";
                break;

            default:
                $error_msg.= "Unknown error type: [$errno] $errstr\n";
                break;
        }

        $error_msg.= "Fatal error in file $errfile:$errline\n";

        //echo $error_msg;
        throw new \Error($error_msg);

        /* Don't execute PHP internal error handler */
        return true;
    }
}