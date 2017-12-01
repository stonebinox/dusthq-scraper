<?php
/*--------------------------------------------
Author: Anoop Santhanam
Date created: 01/12/17 13:04
Last modified: 01/12/17 13:04
Comments: Main class file for 
email_history_master table.
---------------------------------------------*/
class emailHistoryMaster extends emailMaster
{
    public $app=NULL;
    public $emailHistoryValid=false;
    private $email_history_id=NULL;
    function __construct($emailHistoryID=NULL)
    {
        $this->app=$GLOBALS['app'];
        if($emailHistoryID!=NULL)
        {
            $this->email_history_id=addslashes(htmlentities($emailHistoryID));
            $this->emailHistoryValid=$this->verifyEmailHistory();
        }
    }
    function verifyEmailHistory()
    {
        if($this->email_history_id!=NULL)
        {
            $app=$this->app;
            $emailHistoryID=$this->email_history_id;
            $ehm="SELECT email_master_idemail_master FROM email_history_master WHERE stat='1' AND idemail_history_master='$emailHistoryID'";
            $ehm=$app['db']->fetchAssoc($ehm);
            if(($ehm!="")&&($ehm!=NULL))
            {
                $emailID=$ehm['email_master_idemail_master'];
                emailMaster::__construct($emailID);
                if($this->emailValid)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    function addEmailHistory($emailID,$subject,$content)
    {
        $app=$this->app;
        $emailID=addslashes(htmlentities($emailID));
        emailMaster::__construct($emailID);
        if($this->emailValid)
        {
            $subject=trim(addslashes(htmlentities($subject)));
            $content=addslashes($content);
            $in="INSERT INTO email_history_master (timestamp,email_master_idemail_master,email_subject,email_content) VALUES (NOW(),'$emailID','$subject','$content')";
            $in=$app['db']->executeQuery($in);
            return "EMAIL_HISTORY_ADDED";            
        }
        else
        {
            return "INVALID_EMAIL_ID";
        }
    }
}
?>