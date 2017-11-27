<?php
/*------------------------------
Author: Anoop Santhanam
Date created: 27/11/17 13:51
Last modified: 27/11/17 13:51
Comments: Main class file for
institute_master table.
------------------------------*/
class instituteMaster
{
    public $app=NULL;
    public $instituteValid=false;
    private $institute_id=NULL;
    function __construct($insID=NULL)
    {
        $this->app=$GLOBALS['app'];
        if($insID!=NULL)
        {
            $this->institute_id=addslashes(htmlentities($insID));
            $this->instituteValid=$this->verifyInstitute();
        }
    }
    function verifyInstitute()
    {
        if($this->institute_id!=NULL)
        {
            $app=$this->app;
            $insID=$this->institute_id;
            $im="SELECT idinstitute_master FROM institute_master WHERE stat='1' AND idinstitute_master='$insID'";
            $im=$app['db']->fetchAssoc($im);
            if(($im!="")&&($im!=NULL))
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
    function getInstitute()
    {
        if($this->instituteValid)
        {
            $app=$this->app;
            $insID=$this->institute_id;
            $im="SELECT * FROM institute_master WHERE idinstitute_master='$insID'";
            $im=$app['db']->fetchAssoc($im);
            if(($im!="")&&($im!=NULL))
            {
                return $im;
            }
            else
            {
                return "INVALID_INSTITUTE_ID";
            }
        }
        else
        {
            return "INVALID_INSTITUTE_ID";
        }
    }
}
?>