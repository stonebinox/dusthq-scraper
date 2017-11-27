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
            $app=$this->app['db'];
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
    function addEmail($email,$name,$insID)
    {
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
                    $em="SELECT idemail_master FROM email_master WHERE stat='1' AND email_id='$email' AND institute_master_idinstitute_master='$insID'";
                    $em=$app['db']->fetchAssoc($em);
                    if(($em=="")||($em==NULL))
                    {
                        $in="INSERT INTO email_master (timestamp,email_id,institute_master_idinstitute_master,emailee_name) VALUES (NOW(),'$email','$insID','$name')";
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
}
?>