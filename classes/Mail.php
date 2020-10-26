<?php
declare(strict_types=1);

class Mail
{
    public static function send($to, $subject, $message) {
        $headers = "From: info@retro-planer.de\n";
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-Type:text/html;charset=UTF-8\n";

        $footer = "<br><br>Mit freundlichen Grüßen";
        $footer .= "<br>Ihr Kochbuch-Team";

        mail($to, "Kochbuch - ".$subject, $message.$footer, $headers);
    }
}