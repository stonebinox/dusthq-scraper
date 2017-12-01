<?php
/*----------------------------------
Author: Anoop Santhanam
Date Created: 1/12/17 17:54
Last modified: 1/12/17 17:54
Comments: Main class file for 
unsubscribe_master table.
----------------------------------*/
class unsubscribeMaster extends emailHistoryMaster
{
    public $app=NULL;
    public $unsubscribeValid=false;
    private $unsubscribe_id=NULL;
    function __construct($unsubID=NULL)
    {
        $this->app=$GLOBALS['app'];
        if($unsub!=NULL)
        {
            $this->unsibscribe_id=addslashes(htmlentities($unsubID));
            $this->unsubscribeValid=$this->verifyUnsubscribe();
        }
    }
    function verifyUnsubscribe()
    {
        if($this->unsubscribe_id!=NULL)
        {
            $app=$this->app;
            $unsubID=$this->unsubscribe_id;
            $um="SELECT email_master_idemail_master FROM unsubscribe_master WHERE stat='1' AND idunsubscribe_master='$unsubID'";
            $um=$app['db']->fetchAssoc($um);
            if(($um!="")&&($um!=NULL))
            {
                $emailID=$um['email_master_idemail_master'];
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
    function unsubscribeEmail($emailID)
    {
        $emailID=addslashes(htmlentities($emailID));
        $app=$this->app;
        emailMaster::__construct($emailID);
        if($this->emailValid)
        {
            $um="SELECT idunsubscribe_master FROM unsubscribe_master WHERE stat='1' AND email_master_idemail_master='$emailID'";
            $um=$app['db']->fetchAssoc($um);
            if(($um=="")||($um==NULL))
            {
                $in="INSERT INTO unsubscribe_master (timestamp,email_master_idemail_master) VALUES (NOW(),'$emailID')";
                $in=$app['db']->executeQuery($in);
                return "EMAIL_UNSUBSCRIBED";
            }
            else
            {
                return "ALREADY_UNSUBSCRIBED";
            }
        }
        else
        {
            return "INVALID_EMAIL_ID";
        }
    }
    function checkSubscription($emailID)
    {
        $emailID=addslashes(htmlentiites($emailID));
        emailMaster::__construct($emailID);
        if($this->emailValid)
        {
            $app=$this->app;
            $um="SELECT idunsubscribe_master FROM unsubscribe_master WHERE stat='1' AND email_master_idemail_master='$emailID'";
            $um=$app['db']->fetchAssoc($um);
            if(($um!="")&&($um!=NULL))
            {
                return "UNSUBSCRIBED";
            }
            else
            {
                return "SUBSCRIBED";
            }
        }
        else
        {
            return "INVALID_EMAIL_ID";
        }

    }
}
?>