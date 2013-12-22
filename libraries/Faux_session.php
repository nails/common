<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Faux_session
{
	public function sess_read(){		return FALSE;	}
	public function sess_write(){		return FALSE;	}
	public function sess_create(){		return FALSE;	}
	public function sess_update(){		return FALSE;	}
	public function sess_detroy(){		return FALSE;	}
	public function userdata(){			return FALSE;	}
	public function all_userdata(){		return FALSE;	}
	public function set_userdata(){		return FALSE;	}
	public function set_flashdata(){	return FALSE;	}
	public function keep_flashdata(){	return FALSE;	}
	public function flashdata(){		return FALSE;	}
}

/* End of file Faux_session.php */
/* Location: ./application/core/Faux_session.php */