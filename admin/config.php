<?php
   
   
   
   //Die("A CALL WAS DONE TO THE OLD CONFIG FILE WHICH IS DISABLED");
   require_once  dirname(dirname(__FILE__)). '/general/general.php';  
      
	 if (true)
	 {	
		// Old Code
		
		if (session_status() == PHP_SESSION_NONE) 
		{
			session_start();
		}

		if (!isset($login_screen) and !isset($_SESSION["user_id"]))
		{
			header('Location: /admin/login/login.php'); 
		}
		else
		{
			$conn = get_db_conn();
		//  $sql = "Select id, title from advertisers";
		// $result = $conn->query($sql);
		// print_r(fetch_all_assoc($result,'id')[3]);
		// Check conn ection
			if (!isset($conn)or $conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}
		}
	 }
	 else
	 {
		 // Comment To replace
		// echo "Please replace old config file";
	 }
?>