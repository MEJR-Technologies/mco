<?php session_start(); ?>
<?php header('Content-Type: text/javascript'); ?>
var churchAdminToolBar = new Ext.Toolbar({
	items:[{
		text:'Add Church',
		icon:'/images/icons/database_add.png',	
		handler:function(){
			churchAdminFormEdit.getForm().reset();			
			churchAdminFormWindow.show();
			churchAdminFormEdit.getForm().findField('church_name').enable();
			churchAdminFormEdit.getForm().findField('action').setValue('add');
		}
	},
	"-",
	{
		text:'Edit Church',
		icon:'/images/icons/database_edit.png',	
		id:'churchAdminToolBarEdit',
		disabled:true,
		handler:function(){
			var sel = churchAdminGrid.getSelectionModel();
			var rec = sel.getSelected();			
			churchAdminFormWindow.show();
			churchAdminFormEdit.getForm().findField('action').setValue('edit');
			churchAdminFormEdit.getForm().findField('church_id').setValue(rec.data.church_id);
			churchAdminFormEdit.getForm().findField('church_name').disable();			
			churchAdminFormEdit.load({
				url: '/server/proxy/?proxy=sysadmin/churches.php',
				params:{
					"action":"load",
					"church_id":rec.data.church_id
				}				
			});
		}		
	},
	"-",
	{
		text:'Delete Church',	
		disabled:true,
		id:'churchAdminToolBarDelete',		
		icon:'/images/icons/database_edit.png',	
		handler: function(){
			var sel = churchAdminGrid.getSelectionModel();
			var rec = sel.getSelected();
			Ext.MessageBox.confirm('Confirmation','Delete this church?',function(btn,text){
				if(btn == 'yes'){
					Ext.Ajax.request({
						url: '/server/proxy/?proxy=sysadmin/churches.php',
						params:{
							"action":"delete",
							"church_id":rec.data.church_id
						},
						success:function(res,req){
							var o = Ext.decode(res.responseText);
							if(o.success == false){
								Ext.Msg.alert("Error", o.message);								
							} else {
								Ext.Msg.alert("Confirm", "Church Successfully Deleted.");
								churchAdminGridStore.load();
								Ext.getCmp('churchAdminToolBarEdit').disable();
								Ext.getCmp('churchAdminToolBarDelete').disable();								
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
			helpMIF.setTitle('My Church Office Help : Church Administration');
			helpMIF.show();
			helpMIF.setSrc('help/churches.php',false);			
		},
		iconCls: 'helpIcon'
	}]
});

var churchAdminFormEdit = new Ext.FormPanel({
        labelWidth: 150,
        frame:false,
        bodyStyle:'padding:5px 5px 0',
        width: 350,
        defaults: {width: 230},
        defaultType: 'textfield',
        items: [{
                fieldLabel: 'Church Name',
                name: 'church_name',
                disabled:false
            },{
				xtype:'hidden',
				name:'action',
				id:'action',
				value:''			
			},{
				xtype:'hidden',
				name:'church_id',
				id:'church_id',
				value:''			
			}
        ]
    });

var churchAdminFormWindow = new Ext.Window({
	layout:'fit',
	title:'Add Church',
	width:450,
	height:350,
	closeAction:'hide',
	plain: true,	
	items:[churchAdminFormEdit],
	buttons: [{
	    text:'Submit',
	    handler:function(){
	    	    churchAdminFormEdit.getForm().submit({
			url:'/server/proxy/?proxy=sysadmin/churches.php',
			success:function(f,a){
				Ext.Msg.alert("Confirm",a.result.message);
				churchAdminGridStore.load();
				churchAdminFormEdit.getForm().reset();
				churchAdminFormWindow.hide();
				Ext.getCmp('churchAdminToolBarEdit').disable();
				Ext.getCmp('churchAdminToolBarDelete').disable();				
			},
			failure:function(f,a){
				Ext.Msg.alert("Error",a.result.message);				
			}			
		    });	    
	    }
	},{
	    text: 'Close',
	    handler: function(){
		churchAdminFormWindow.hide();
	    }
	}]
});

var churchAdminGridStore = new Ext.data.JsonStore({	
	totalProperty: 'total',
	root: 'results',
	proxy: new Ext.data.HttpProxy({url:'/server/proxy/?proxy=sysadmin/churches.php',method:'GET'}),
	remoteSort:'true',
	id: 'churchAdminGridStore',
	autoLoad:false,
	baseParams:{
		"limit":25
	},	
	fields:[
		'church_id',
		'church_name'
	]	
});


var churchAdminGrid = new Ext.grid.GridPanel({
	store: churchAdminGridStore,
	loadMask:false,
	frame:false,
	tbar:churchAdminToolBar,
	columns: [
		{header: "ID", width: 30, sortable: true, dataIndex: 'church_id',hidden:true},
		{header: "Church Name", width: 100, sortable: true, dataIndex: 'church_name'}
	  ],
	stripeRows: true,
	viewConfig: {
		forceFit: true
	},
	bbar: new Ext.PagingToolbar({
		store: churchAdminGridStore,
		pageSize:25,
		displayInfo: true
	}),	
	listeners:{
		afterrender:function(){
			this.store.load();	
		},
		rowclick:function(){
			Ext.getCmp('churchAdminToolBarEdit').enable();
			Ext.getCmp('churchAdminToolBarDelete').enable();			
		}
		
	}
});

document.title = 'Churches Administration';

var churchPanel = new Ext.Panel({
	id:'church-admin-panel',
	closable:true,
	title:'Church Administration',
	layout:'fit',
	items:[churchAdminGrid]
});

Ext.onReady(function(){

		addTab(churchPanel);		

});
