<?php session_start(); ?>
<?php header('Content-Type: text/javascript'); ?>

Ext.ns('MCO.user');
MCO.user.cid = <?php echo $_SESSION['User']['church_id']; ?>;
MCO.user.admin = <?php echo $_SESSION['User']['site_admin']; ?>;

function addTab(el){
	var center = Ext.getCmp('mainTabPanel');
	var card = false;
	if (!card) {
		center.add(el);
		center.getLayout().setActiveItem(el);
	} else {	
		center.getLayout().setActiveItem(card);
	}
}

	Ext.onReady(function(){
		Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

	Ext.QuickTips.init();
    
		Ext.BLANK_IMAGE_URL = './ext/resources/images/default/s.gif';        
    
		var viewport = new Ext.Viewport({
			layout: 'border',
			border:false,
			style:'background:#FFFFFF;',   			
			items: [{
				border: false,				
				id: "headerMain",
				style: "text-align:center;",			
				region: 'north',
				height:96,
				contentEl:'header',
				margins: '5 0 5 0'     				
			},{
				region: 'west',
				id: 'west-panel',
				title: 'Navigation Menu',
				split: true,
				width: 200,
				minSize: 175,
				maxSize: 400,
				collapsible: true,
				margins: '0 0 5 5',
				layout: {
					type: 'accordion',
					animate: true
				},
			items: [{
				contentEl: 'west',
				title: 'Applications',
				border: false,
				iconCls: 'nav',
				layout:'fit',
				items:[appNavTree]
			}
			<?php if($_SESSION['User']['has_admin'] == 1 || $_SESSION['User']['site_admin'] == 1){ ?>
			,{
				title: 'Administrator',
				border: false,
				iconCls: 'admin',
				layout:'fit',
				items:[adminNavTree]					
			}
			<?php } 
				// Check for site_admin
				if($_SESSION['User']['site_admin'] == 1){ ?>
			,{
				title: 'System Admin',
				border: false,
				iconCls: 'admin',
				layout:'fit',
				items:[sysadminNavTree]					
			}
			<?php } ?>				
			]
		},{
			region: 'center',
			id: 'center-panel',
			collapsible: false,
			margins: '0 5 5 0',
			border:false,
			layout:'fit',
			items:[
				new Ext.TabPanel({
					id:'mainTabPanel',
					//region:'center',
					deferredRender: false,
					margins: '0 5 0 0',
					activeTab: 0,
					items: [{
						title: 'Home',
						closable: false,
						autoScroll: true,
						items:[{
							xtype:'portal',
							margins:'5 5 5 5',
							border:false,
							items:[{
								columnWidth:.50,
								style:'padding:10px 10px 10px 10px',
								items:[{
									title: 'Attendance',
									layout:'fit',
									tools: pTools,
									items: new AttendanceGrid()
									},{
									    title: 'Another Panel 2',
									    tools: pTools,
									    html: 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Sed metus nibh, sodales a, porta at, vulputate eget, dui. Pellentesque ut nisl. Maecenas tortor turpis, interdum non, sodales non, iaculis ac, lacus. Vestibulum auctor, tortor quis iaculis malesuada, libero lectus bibendum purus, sit amet tincidunt quam turpis vel lacus. In pellentesque nisl non sem. Suspendisse nunc sem, pretium eget, cursus a, fringilla vel, urna.'
									}]
								},{
								columnWidth:.50,
								style:'padding:10px 0 10px 10px',
								items:[{
								    title: 'Registration Status',
								    tools: pTools,
								    html:'<iframe id="regStatus" name="regStatus" src="/mco/js/modules/dashboard/regStatusChart.html" style="height:450; width:100%; border:0px;" frameborder="true"></iframe>'
								}]
							}]				
						}],							
						bbar: new Ext.Toolbar({
							items:[
								new Ext.Toolbar.Fill()
								,new Ext.Toolbar.Button({
									cls: 'topBtn',
									id: 'contactBtn',
									text: 'Contact',
									icon: 'images/icons/email.png',
									iconCls: "x-button-icon",
									listeners: { 
										'click': {
											fn: function() {
												contactForm.getForm().findField('action').setValue('add');
												contactWindow.show();
											}
										}
									}
								})
								,new Ext.Toolbar.SplitButton({
									text: 'Profile'
									,tooltip: {text:'Click for profile options', title:'Profile Info'}
									,icon: 'images/icons/user.png'
									// Menus can be built/referenced by using nested menu config objects
									,menu : {
										items: [{
											text: 'Change Password',
											handler:function(){
												changePassword();	
											}
										}/*, {
											text: 'Change Email'
											//,handler: onItemClick
										}*/]
									}
								})
								,new Ext.Toolbar.Button({
									cls: 'topBtn',
									id: 'logoutBtn',
									text: 'Logout',
									icon: 'images/icons/disconnect.png',
									iconCls: "x-button-icon",
									listeners: { 
										'click': {
											fn: function() { window.location='/?doLogout=true'; }
											,dummy: true
										}
									}
								})
							]
						})
					}]															
				})
			]
		}
	  ]
	});
});
