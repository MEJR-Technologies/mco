<?php
	require_once 'server/includes/db/db.php';
	
	//Set a default form message
	$formMessage = "Access to this location is restricted to authorized users only.<br>Please type your username and password.";
	
	if($_GET['doLogout'] == 'true'){
		session_start();
		session_destroy();
	}
	
	if(!empty($_POST['username']) && !empty($_POST['password'])){
		//Set success to false initially
		$success = 'false';
		
		$submittedPassword = $_POST['password'];
		$submittedUsername = $_POST['username'];
		
		//Query to see if the user is valid
		$getUserParams = "SELECT u.*,c.church_name FROM users u INNER JOIN churches c ON u.church_id = c.church_id WHERE username = '" . $submittedUsername . "' AND password = '" . $submittedPassword . "' AND is_active = 1";
		$queryResults = mysql_query($getUserParams)or die(mysql_error());
		
		//If one result returned for a given username and password, the user is presumed valid
		if (mysql_num_rows($queryResults) == 1){
			//User successfully logged in, start session
			session_start();

			//Put user parameters into array			
			$userParams = mysql_fetch_assoc($queryResults);
			
			//Set session variables
			$_SESSION['User']['id'] 			= $userParams['user_id'];
			$_SESSION['User']['church_id'] 		= $userParams['church_id'];
			$_SESSION['User']['username'] 		= $userParams['username'];
			$_SESSION['User']['church_name']	= $userParams['church_name'];
			$_SESSION['User']['has_admin']		= $userParams['has_admin'];
			$_SESSION['User']['site_admin']		= $userParams['site_admin'];
			$_SESSION['User']['user_email']		= $userParams['user_email'];			

			//Set success to true
			$success = 'true';	
			
			//Update last login date
			$updateLastLogin = "UPDATE users SET user_last_login = now() WHERE user_id = " . $userParams['user_id'];
			$res = mysql_query($updateLastLogin) or die(mysql_error());			
			
		}
		
		//Echo back JSON string
		echo '{"success":' .  $success . ', "session_id":"' . session_id() . '"}';		
		exit;
	}

?>
<html>
<head>
	<title>MyChurchOffice.net Login</title>
	<LINK REL="SHORTCUT ICON" href="media/img/favicon.ico" type="image/x-icon">
	<link rel="icon" href="media/img/favicon.ico" type="image/x-icon"> 	
	<link rel="stylesheet" type="text/css" href="ext/resources/css/ext-all.css" />
	
	<!-- LIBS --> 
	<script type="text/javascript" src="ext/adapter/ext/ext-base.js"></script>
	<script type="text/javascript" src="js/crypt/md5.js"></script>	
	<!-- ENDLIBS --> 

	<script type="text/javascript" src="ext/ext-all.js"></script>
        <link rel="stylesheet" type="text/css" href="ext/resources/css/ux/login/overrides.css" />

        <link rel="stylesheet" type="text/css" href="ext/resources/css/ux/login/flags.css" />
        <link rel="stylesheet" type="text/css" href="ext/resources/css/ux/login/virtualkeyboard.css" />
        
        <script type="text/javascript" src="ext/ux/login/overrides.js"></script>

        <script type="text/javascript" src="ext/ux/login/virtualkeyboard.js"></script>
        <script type="text/javascript" src="ext/ux/login/plugins/virtualkeyboard.js"></script>
        <script type="text/javascript" src="ext/ux/login/Ext.ux.Crypto.SHA1.js"></script>
        <script type="text/javascript" src="ext/ux/login/Ext.ux.form.IconCombo.js"></script>
        <script type="text/javascript" src="ext/ux/login/Ext.ux.form.LoginDialog.js"></script>
        <script type="text/javascript" src="js/common/forgotPassword.js"></script>        

        <script type="text/javascript">
		Ext.onReady(function() {
			Ext.QuickTips.init();
		
			var loginDialog = new Ext.ux.form.LoginDialog({
				forgotPasswordLink:'javascript:passwordWindow.show();',
				basePath: 'ext/resources/images/ux/login/icons',
				title:'MyChurchOffice.net Login',
				url:'index.php',
				message:'<?php echo $formMessage; ?>',
				modal:true,
				listeners:{
					success:function(w,r){
						var s = Ext.decode(r.response.responseText).session_id;
						window.location.href = 'mco.php?s=' + s;
					},
					failure:function(f,a){
						Ext.Msg.alert("Error", "<b>Login failed.</b><br><br>This may have happened for several reasons:<br>1. The Username and Password you entered are not correct.<br>2. The account is not valid<br>3. The account is inactive.<br><br>Please contact your administrator.");	
					}
				},
				encrypt:true
			});
		
			loginDialog.show();
		});
	</script>	
</head>
<body>
</body>
</html>
