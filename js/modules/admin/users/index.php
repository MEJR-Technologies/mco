<?php session_start(); ?>
<?php header('Content-Type: text/javascript'); ?>
var userAdminToolBar = new Ext.Toolbar({
	items:[{
		text:'Add User',
		icon:'/images/icons/user_add.png',	
		handler:function(){
			userAdminFormEdit.getForm().reset();			
			userAdminFormWindow.show();
			userAdminFormEdit.getForm().findField('username').enable();
			userAdminFormEdit.getForm().findField('action').setValue('add');
			
			//Set church_id
			<?php if(!$_SESSION['User']['site_admin']){ ?>
				userAdminFormEdit.getForm().findField('church_id').setValue('<?php echo $_SESSION['User']['church_id']; ?>');
			<?php } ?>
		}
	},
	"-",
	{
		text:'Edit User',
		icon:'/images/icons/user_edit.png',	
		id:'userAdminToolBarEdit',
		disabled:true,
		handler:function(){
			var sel = userAdminGrid.getSelectionModel();
			var rec = sel.getSelected();			
			userAdminFormWindow.show();
			userAdminFormEdit.getForm().findField('action').setValue('edit');
			userAdminFormEdit.getForm().findField('user_id').setValue(rec.data.user_id);
			userAdminFormEdit.getForm().findField('username').disable();			
			userAdminFormEdit.load({
				url: '/server/proxy/?proxy=admin/users.php',
				params:{
					"action":"load",
					"user_id":rec.data.user_id
				}				
			});
		}		
	},
	"-",
	{
		text:'Delete User',
		icon:'/images/icons/user_delete.png',	
		disabled:true,
		id:'userAdminToolBarDelete',		
		handler: function(){
			var sel = userAdminGrid.getSelectionModel();
			var rec = sel.getSelected();
			Ext.MessageBox.confirm('Confirmation','Delete this user?',function(btn,text){
				if(btn == 'yes'){
					Ext.Ajax.request({
						url: '/server/proxy/?proxy=admin/users.php',
						params:{
							"action":"delete",
							"user_id":rec.data.user_id
						},
						success:function(res,req){
							var o = Ext.decode(res.responseText);
							if(o.success == false){
								Ext.Msg.alert("Error", o.message);								
							} else {
								Ext.Msg.alert("Confirm", "User Successfully Deleted.");
								userAdminGridStore.load();
								Ext.getCmp('userAdminToolBarEdit').disable();
								Ext.getCmp('userAdminToolBarDelete').disable();								
							}							
						},
						failure:function(res,req){
							Ext.Msg.alert("Error", "An Error Occurred.");						
						}						
					});
				}
			});
		}		
	},
	"->",
	{
		text:'Help',
		handler:function(){
			helpMIF.setTitle('My Church Office Help : User Administration');
			helpMIF.show();
			helpMIF.setSrc('help/users.php',false);			
		},
		iconCls: 'helpIcon'
	}]
});

var userAdminFormEdit = new Ext.FormPanel({
        labelWidth: 150,
        frame:false,
        bodyStyle:'padding:5px 5px 0',
        width: 350,
        defaults: {width: 230},
        defaultType: 'textfield',
        items: [{
                fieldLabel: 'Username',
                name: 'username',
                disabled:true
            },{
                fieldLabel: 'Password',
                name: 'password',
		inputType:'password'
            },{
                fieldLabel: 'Confirm Password',
                name: 'confirm_password',
		inputType:'password'
            },{            	    
                fieldLabel: 'Email Address',
                name: 'user_email',
		allowBlank:false
            },{
            	xtype:'checkbox',
                fieldLabel: 'Church Admin?',
                name: 'has_admin'            	    
            },{
            	xtype:'checkbox',
                fieldLabel: 'Edit Rights?',
                name: 'has_edit'        	    
            },{
            	xtype:'checkbox',
                fieldLabel: 'Account Active?',
                name: 'is_active'            	    
            },{
            	xtype:'checkbox',
                fieldLabel: 'Can Upload?',
                name: 'has_upload'            	    
            },{
<?php if(!$_SESSION['User']['site_admin']){ ?>
		xtype:'hidden',
		name:'church_id',
		value:''
<?php } else { ?>
		xtype:'combo',
		valueField: 'church_id',
		displayField:'church_name',           	
		typeAhead: true,
		triggerAction: 'all',
		mode: 'local',
		store: new Ext.data.JsonStore({
				url: '/server/proxy/?proxy=admin/churches.php',
				root: 'churches',
				autoLoad:true,
				fields: ['church_id', 'church_name']
			}),
		hiddenName:'church_id',		
		fieldLabel: 'Church',
		name: 'church_id'          	    
<?php } ?>            	                	    
            },{
		xtype:'hidden',
		name:'action',
		id:'action',
		value:''			
	    },{
		xtype:'hidden',
		name:'user_id',
		id:'user_id',
		value:''			
	    }
        ]
    });

var userAdminFormWindow = new Ext.Window({
	layout:'fit',
	title:'Add User',
	width:450,
	height:350,
	closeAction:'hide',
	plain: true,	
	items:[userAdminFormEdit],
	buttons: [{
	    text:'Submit',
	    handler:function(){
	    	    userAdminFormEdit.getForm().submit({
			url:'/server/proxy/?proxy=admin/users.php',
			success:function(f,a){
				Ext.Msg.alert("Confirm",a.result.message);
				userAdminGridStore.load();
				userAdminFormEdit.getForm().reset();
				userAdminFormWindow.hide();
				Ext.getCmp('userAdminToolBarEdit').disable();
				Ext.getCmp('userAdminToolBarDelete').disable();				
			},
			failure:function(f,a){
				Ext.Msg.alert("Error",a.result.message);				
			}			
		    });	    
	    }
	},{
	    text: 'Close',
	    handler: function(){
		userAdminFormWindow.hide();
	    }
	}]
});

var userAdminGridStore = new Ext.data.JsonStore({	
	totalProperty: 'total',
	root: 'results',
	proxy: new Ext.data.HttpProxy({url:'/server/proxy/?proxy=admin/users.php',method:'GET'}),
	remoteSort:'true',
	id: 'userAdminGridStore',
	autoLoad:false,
	baseParams:{
		"limit":25
	},	
	fields:[
		'user_id',
		'username',
		'user_email',		
		{name: 'user_last_login', type: 'date', dateFormat: 'Y-m-d h:i:s'},
		'has_admin',
		'has_edit',
		'is_active',
		'has_upload'
	]	
});


var userAdminGrid = new Ext.grid.GridPanel({
	store: userAdminGridStore,
	loadMask:false,
	frame:false,
	tbar:userAdminToolBar,
	columns: [
		{header: "ID", width: 30, sortable: true, dataIndex: 'user_id',hidden:true},
		{header: "Username", width: 100, sortable: true, dataIndex: 'username'},
		{header: "Email", width: 150, sortable: true, dataIndex: 'user_email'},
		{header: "Last Login", width: 50, sortable: true, dataIndex: 'user_last_login',renderer: Ext.util.Format.dateRenderer('m/d/Y')},
		{header: "Church Admin", width: 50, sortable: true, dataIndex: 'has_admin',renderer: returnYesNo},
		{header: "Edit Rights", width: 30, sortable: true, dataIndex: 'has_edit',renderer:returnYesNo},
		{header: "Account Active", width: 50, sortable: true, dataIndex: 'is_active', renderer:returnYesNo},
		{header: "Upload Rights", width: 30, sortable: true, dataIndex: 'has_upload',renderer: returnYesNo}		
	  ],
	stripeRows: true,
	viewConfig: {
		forceFit: true
	},
	bbar: new Ext.PagingToolbar({
		store: userAdminGridStore,
		pageSize:25,
		displayInfo: true
	}),	
	listeners:{
		afterrender:function(){
			this.store.load();	
		},
		rowclick:function(){
			Ext.getCmp('userAdminToolBarEdit').enable();
			Ext.getCmp('userAdminToolBarDelete').enable();			
		}
		
	}
});

document.title = 'User Admin';

var userPanel = new Ext.Panel({
	id:'user-admin-panel',
	closable:true,
	title:'User Admin',
	layout:'fit',
	items:[userAdminGrid]
});

Ext.onReady(function(){

		addTab(userPanel);		

});
