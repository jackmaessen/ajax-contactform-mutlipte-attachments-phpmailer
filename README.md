# ajax-contactform-mutlipte-attachments-phpmailer
## AJAX contactform with multiple attachments and validation in phpmailer

What is this?

A basic AJAX contactform with multiple attachments and validation using PHPMailer

How to use:

Copy the 2 files **form.html** and **send.php** with the folder **PHPMailer** to your webroot or subfolder.
Open **send.php** and fill in your email and name between the ///////// SETTINGS//////// at the top.
The validation of the attachments is by mime-type. You can change or add these mime-types where inside the function under the line **// function check mime type attachments**
The default SMTP server is set to your localhost. If you want to use a different SMTP server for sending mails, you can change it in the **settings phpmailer**. This documentation might be helpfull: https://github.com/PHPMailer/PHPMailer/tree/master/examples

If you have any questions, please contact me at : jcmg.maessen@gmail.com
