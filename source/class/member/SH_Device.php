<?php
class SH_Device implements IAuth{ 

public $did=NULL;
	
	use Auth_location_object{ 
        get_access_location as private ;
        get_access_object as private;
    }
	
	public function get_device_id() {
        if(!empty($this->did)){
            return 'did_'.$this->did;
        }else{
            return NULL;
        }
    }
	//device id for the device
	
	public function get_access($type){
		if($this->did==NULL)
		{
			return false;
		}
		switch($type){
			case AuthType::AuthGlobal :
				return $this->get_access_global();
			case AuthType::AuthLocation :
				return $this->get_access_location();
			case AuthType::AuthObject :
				return $this->get_access_object();
			default:
				throw new Exception("Error in using SH_Device::get_access, AuthType unknown");
		}

		
	}
	
	public function generate_seckey(){
		$uid= $this->get_device_id();
        //$sec = generate_secret_key($did);
        $cond['seckey']=$sec;
        $cond['type']=SecType::AuthDevice;
        $cond['timeout']=time()+core::$config['sec']['time'];
        $cond['authz']=serialize($this->auth);
        DB::insert('authz',$cond);
        return $sec;
    }
		//information about the table
	public static function instance_from_authz(authz $authz);
	
	
	public function git_device_informatin{
		//git the unique bluetooth device id
		
		
	}
	


}

