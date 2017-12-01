<?php
/*---------------------------------------
Author: Anoop Santhanam
Date created: 27/11/17 13:59
Last modified: 27/11/17 13:59
Comments: Main class for email_master
table.
---------------------------------------*/
class emailMaster extends instituteMaster
{
    public $app=NULL;
    public $emailValid=false;
    private $email_id=NULL;
    function __construct($emailID=NULL)
    {
        $this->app=$GLOBALS['app'];
        if($emailID!=NULL)
        {
            $this->email_id=addslashes(htmlentities($emailID));
            $this->emailValid=$this->verifyEmail();
        }
    }
    function verifyEmail()
    {
        if($this->email_id!=NULL)
        {
            $app=$this->app;
            $emailID=$this->email_id;
            $em="SELECT institute_master_idinstitute_master FROM email_master WHERE stat='1' AND idemail_master='$emailID'";
            $em=$app['db']->fetchAssoc($em);
            if(($em!="")&&($em!=NULL))
            {
                $insID=$em['institute_master_idinstitute_master'];
                instituteMaster::__construct($insID);
                if($this->instituteValid)
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
    function getEmail()
    {
        if($this->emailValid)
        {
            $app=$this->app;
            $emailID=$this->email_id;
            $em="SELECT * FROM email_master WHERE idemail_master='$emailID'";
            $em=$app['db']->fetchAssoc($em);
            if(($em!="")&&($em!=NULL))
            {
                $insID=$em['institute_master_idinstitute_master'];
                instituteMaster::__construct($insID);
                $institute=instituteMaster::getInstitute();
                if(is_array($institute))
                {
                    $em['institute_master_idinstitute_master']=$institute;
                }
                return $em;
            }
            else
            {
                return "INVALID_EMAIL_ID";
            }
        }
        else
        {
            return "INVALID_EMAIL_ID";
        }
    }
    function getEmails($insID,$offset=0)
    {
        $insID=addslashes(htmlentities($insID));
        instituteMaster::__construct($insID);
        if($this->instituteValid)
        {
            $app=$this->app;
            $offset=addslashes(htmlentities($offset));
            if(($offset!="")&&($offset!=NULL)&&(is_numeric($offset))&&($offset>=0))
            {
                $em="SELECT idemail_master FROM email_master WHERE stat='1' AND institute_master_idinstitute_master='$insID' ORDER BY idemail_master DESC LIMIT $offset,100";
                $em=$app['db']->fetchAll($em);
                $emailArray=array();
                for($i=0;$i<count($em);$i++)
                {
                    $email=$em[$i];
                    $emailID=$email['idemail_master'];
                    $this->__construct($emailID);
                    $email=$this->getEmail();
                    if(is_array($email))
                    {
                        array_push($emailArray,$email);
                    }
                }
                if(count($emailArray)>0)
                {
                    return $emailArray;
                }
                else
                {
                    return "NO_EMAILS_FOUND";
                }
            }
            else
            {
                return "INVALID_OFFSET_VALUE";
            }
        }
        else
        {
            return "INVALID_INSTITUTE_ID";
        }
    }
    function addEmail($email,$name,$website,$desc,$insID)
    {
        $app=$this->app;
        $insID=addslashes(htmlentities($insID));
        instituteMaster::__construct($insID);
        if($this->instituteValid)
        {
            $email=trim(strtolower(addslashes(htmlentities($email))));
            if(($email!="")&&($email!=NULL)&&(filter_var($email, FILTER_VALIDATE_EMAIL)))
            {
                $name=trim(ucwords(addslashes(htmlentities($name))));
                if(($name!="")&&($name!=NULL))
                {
                    $website=trim(strtolower(addslashes(htmlentities($website))));
                    $desc=trim(addslashes(htmlentities($desc)));
                    $em="SELECT idemail_master FROM email_master WHERE stat='1' AND email_id='$email' AND institute_master_idinstitute_master='$insID'";
                    $em=$app['db']->fetchAssoc($em);
                    if(($em=="")||($em==NULL))
                    {
                        $in="INSERT INTO email_master (timestamp,email_id,institute_master_idinstitute_master,emailee_name,emailee_website,emailee_category) VALUES (NOW(),'$email','$insID','$name','$website','$desc')";
                        $in=$app['db']->executeQuery($in);
                        return "EMAIL_ADDED";
                    }
                    else
                    {
                        return "EMAIL_ALREADY_ADDED";
                    }
                }
                else
                {
                    return "INVALID_NAME";
                }
            }
            else
            {
                return "INVALID_EMAIL_ID";
            }
        }
        else
        {
            return "INVALID_INSTITUTE_ID";
        }
    }
    function countEmailIDs($insID)
    {
        $app=$this->app;
        $insID=addslashes(htmlentities($insID));
        instituteMaster::__construct($insID);
        if($this->instituteValid)
        {
            $em="SELECT COUNT(idemail_master) FROM email_master WHERE stat='1' AND institute_master_idinstitute_master='$insID'";
            $em=$app['db']->fetchAssoc($em);
            $em=json_encode($em);
            return $em;
        }
        else
        {
            return "INVALID_INSTITUTE_ID";
        }
    }
    function sendEmail($insID,$subject,$content)
    {
        $app=$this->app;
        $insID=addslashes(htmlentities($insID));
        instituteMaster::__construct($insID);
        if($this->instituteValid)
        {
            $content=trim($content);
            if(($content!="")&&($content!=NULL))
            {
                $subject=trim($subject);
                if(($subject!="")&&($subject!=NULL))
                {
                    $from = new SendGrid\Email("Dust", "dust@dusthq.com");
                    $emails=$this->getEmails($insID);
                    if(is_array($emails))
                    {
                        for($i=0;$i<count($emails);$i++)
                        {
                            $email=$emails[$i];
                            $emailID=$email['email_id'];
                            $name=stripslashes($email['emailee_name']);
                            $idEmail=$email['idemail_master'];
                            $body='<p>Hello '.$name.'!</p>'.$content;
                            $to = new SendGrid\Email($name, $emailID);
                            $emailBody = new SendGrid\Content("text/html", $body);
                            $mail = new SendGrid\Mail($from, $subject, $to, $emailBody);
                            $apiKey = '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';
                            $sg = new \SendGrid($apiKey);
                            $response = $sg->client->mail()->send()->post($mail);
                            $history=new emailHistoryMaster;
                            $emailresponse=$history->addEmailHistory($idEmail,$subject,$body);
                        }
                        return "EMAILED_".count($emails);
                    }
                    else
                    {
                        return $emails;
                    }
                }
                else
                {
                    return "INVALID_SUBJECT";
                }
            }
            else
            {
                return "INVALID_MAIL_CONTENT";
            }
        }
        else
        {
            return "INVALID_INSTITUTE_ID";
        }
    }
}
?>