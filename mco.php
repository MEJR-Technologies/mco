<?php
	//Start session
	session_start();
	
	//If the user does not have a valid session, redirect them to the login page.
	if(!isset($_SESSION['User']['id'])){
		header('Location: index.php?notAuthenticated=true');	
	}
?>

<html> 
<head> 
  <title>My Church Office - Home</title> 
    <link rel="stylesheet" type="text/css" href="ext/resources/css/ext-all.css" /> 
 
    <!-- overrides to base library --> 
    <link rel="stylesheet" type="text/css" href="ext/ux/portal/portal.css" />
    
    <!-- multiselect CSS --> 
    <link rel="stylesheet" type="text/css" href="ext/resources/css/ux/multiselect/MultiSelect.css" />
    
    <!-- Group Summary CSS --> 
    <link rel="stylesheet" type="text/css" href="ext/resources/css/ux/groupsummary/GroupSummary.css" />         
    
    <!--  MCO CSS -->
    <link rel="stylesheet" type="text/css" href="css/mco.css" />    
 
    <!-- Ext --> 
    <script type="text/javascript" src="ext/adapter/ext/ext-base.js"></script>  
    <script type="text/javascript" src="ext/ext-all-debug.js"></script>    
 
    <!-- Ext Portal UX --> 
    <script type="text/javascript" src="ext/ux/portal/Portal.js"></script> 
    <script type="text/javascript" src="ext/ux/portal/PortalColumn.js"></script>
    <script type="text/javascript" src="ext/ux/portal/Portlet.js"></script>
        
    <!-- Ext MultiSelect UX --> 
    <script type="text/javascript" src="ext/ux/multiselect/MultiSelect.js"></script>  

    <!-- Ext GroupSummary UX --> 
    <script type="text/javascript" src="ext/ux/groupsummary/GroupSummary.js"></script>

    <!-- Ext MIF UX -->
    <script type="text/javascript" src="ext/ux/mif/multidom.js"></script>    
    <script type="text/javascript" src="ext/ux/mif/mif.js"></script>
    <script type="text/javascript" src="ext/ux/mif/mifmsg.js"></script>
    <script type="text/javascript" src="ext/ux/mif/uxvismode.js"></script>
    
    <!-- Ext Fusion UX -->
    <script type="text/javascript" src="ext/ux/media/uxfusionpak-debug.js"></script>
    
    <!-- MCO -->
    <script type="text/javascript" src="js/nav/site-navigation.js"></script>   
    <script type="text/javascript" src="js/common/common.js"></script>
    <script type="text/javascript" src="js/common/contact.js"></script>
    <script type="text/javascript" src="js/common/help.js"></script> 
    <script type="text/javascript" src="js/common/tools.js"></script>
    <script type="text/javascript" src="js/common/changePassword.js"></script>     
    
    <!-- Portal Widgets -->
    <script type="text/javascript" src="js/modules/dashboard/attendance-grid.js"></script>    
  
 	<!--  MCO Application -->
    <script type="text/javascript" src="js/app.php"></script> 	
</head> 
<body> 
	<!-- use class="x-hide-display" to prevent a brief flicker of the content --> 
	<div id="west" class="x-hide-display"></div>
    
	<!-- Header and Footer divs -->
	<div id="header" class="x-hide-display" style="height:96;background-color:#fff;">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td style="text-align:left;padding-left:5px;" colspan="5">
					<a href="http://beta.mychurchoffice.net/mco.php"><img border="0" src="images/mco_logo.jpg"></a>
				</td>
				<td style="text-align:right;padding-right:5px;">
					<?php echo "Welcome [" . $_SESSION['User']['username'] . "], " . $_SESSION['User']['church_name']; ?>
				</td>
			</tr>
		</table>
	</div>
</body> 
</html> 
