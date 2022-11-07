<?php  
	$defaultUploadPath = '/user-uploads/editor';
	/*assetmanager\server\delfile.php use defaultUploadPath variable for application folder*/
	
	$path_for_images = ''; /* Relative to URI Root - This is for Innova */	
	
	/*assetmanager\server\delfile.php use path_for_images variable for admin-application folder*/
	
	$teacherSession = UserAuthentication::SESSION_ELEMENT_NAME;
	$adminSession = AdminAuthentication::SESSION_ELEMENT_NAME;
	
	$is_teacher_for_file_manager = 0;
	$is_admin_for_file_manager = 0;
	
	$admin = (isset($_SESSION[$adminSession]['admin_id']) && is_numeric($_SESSION[$adminSession]['admin_id']) && intval($_SESSION[$adminSession]['admin_id']) > 0 && strlen(trim($_SESSION[$adminSession]['admin_name'])) >= 4);
		
	$teacher = (isset($_SESSION[$teacherSession]['user_id']) && is_numeric($_SESSION[$teacherSession]['user_id']) && intval($_SESSION[$teacherSession]['user_id']) > 0 && (strlen(trim($_SESSION[$teacherSession]['user_first_name'])) >= 4)); 
	
	if( !($admin || $teacher) ){
		echo '<br/>You do not have access to file manager, Please contact admin!';
		exit(0);
	}	
	
	if($admin){
		$is_admin_for_file_manager = 1;
	}else if($teacher){
		$is_admin_for_file_manager = 1;
		$is_teacher_for_file_manager = 1;
	}else{
		/*exit(0)*/
	}
	
	if($is_teacher_for_file_manager){
		$path_for_images = $defaultUploadPath."/".$_SESSION[$teacherSession]['user_id']; /* Relative to URI Root - This is for Innova */
	}else if($is_admin_for_file_manager){	
		$path_for_images = $defaultUploadPath; /* Relative to URI Root - This is for Innova */	
	}else{
		exit(0);	
	}
	
		
	if(!file_exists($path_for_images)){	
		//create the folder
		//$dir_to_create = ealpath(dirname(__FILE__). '/../').$path_for_images;
		//@mkdir($dir_to_create, 0777, true);
		//create the folder
		$dir_to_create = realpath(dirname(__FILE__). '/../').$path_for_images;
		if(!file_exists($dir_to_create)){
			mkdir($dir_to_create, 0777, true);
		}

	} 
?>