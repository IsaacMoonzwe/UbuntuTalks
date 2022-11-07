<?php
/* $files = glob('user-uploads/*'); 
foreach( $files as $file ){ // iterate files
	if( is_file($file) ){
	  unlink($file);
	  echo 'done';
	}
} */

/* rrmdir( "admin-application/views" );
recurseRmdir( "admin-application/views" ); */

delRecFiles("user-uploads");

function delRecFiles( $dir ){
	$files = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
		RecursiveIteratorIterator::CHILD_FIRST
	);
	
	foreach ($files as $fileinfo) {
		//$todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
		
		$path = $fileinfo->getRealPath();
		
		if( $fileinfo->isDir() ){
			rmdir( $path );
		} else {
			unlink( $path );
		}
		
	}

	//rmdir($dir);
}

function recurseRmdir( $dir ) {
  $files = scandir($dir);
  
  foreach ($files as $file) {
    (is_dir("$dir/$file")) ? recurseRmdir("$dir/$file") : unlink("$dir/$file");
  }
  return rmdir($dir);
}

 function rrmdir($dir) { 
   if (is_dir($dir)) {
	   
     $objects = scandir($dir); 
     foreach ($objects as $object) { 
       if ($object != "." && $object != "..") { 
         if (is_dir($dir."/".$object))
           rrmdir($dir."/".$object);
         else
           unlink($dir."/".$object); 
       } 
     }
     rmdir($dir); 
   } 
 }