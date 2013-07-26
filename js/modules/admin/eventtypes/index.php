<?php session_start(); ?>
<?php header('Content-Type: text/javascript'); ?>
var eventTypeAdminToolBar = new Ext.Toolbar({
	items:[{
		text:'Add Event Type',	
		handler:function(){
			eventTypeAdminFormEdit.getForm().reset();			
			eventTypeAdminFormEdit.getForm().findField('action').setValue('add');
			
			//Set church_id
			<?php if(!$_SESSION['User']['site_admin']){ ?>
				eventTypeAdminFormEdit.getForm().findField('church_id').setValue('<?php echo $_SESSION['User']['church_id']; ?>');
			<?php } ?>

			eventTypeAdminFormWindow.show();
		}
	},
	"-",
	{
		text:'Edit Event Type',
		id:'eventTypeAdminToolBarEdit',
		disabled:true,
		handler:function(){
			var sel = eventTypeAdminGrid.getSelectionModel();
			var rec = sel.getSelected();			
			eventTypeAdminFormWindow.show();
			eventTypeAdminFormEdit.getForm().findField('action').setValue('edit');
			eventTypeAdminFormEdit.getForm().findField('event_type_id').setValue(rec.data.id);			
			eventTypeAdminFormEdit.load({
				url: '/server/proxy/?proxy=admin/eventtypes.php',
				params:{
					"action":"load",
					"event_type_id":rec.data.id
				}				
			});
		}		
	},
	"-",
	{
		text:'Delete Event Type',
		disabled:true,
		id:'eventTypeAdminToolBarDelete',		
		handler: function(){
			var sel = eventTypeAdminGrid.getSelectionModel();
			var rec = sel.getSelected();
			Ext.MessageBox.confirm('Confirmation','Delete this event type?',function(btn,text){
				if(btn == 'yes'){
					Ext.Ajax.request({
						url: '/server/proxy/?proxy=admin/eventtypes.php',
						params:{
							"action":"delete",
							"event_type_id":rec.data.id
						},
						success: function(){
							Ext.Msg.alert("Confirm", "Event Type Successfully Deleted.");
							eventTypeAdminGridStore.load();
							Ext.getCmp('eventTypeAdminToolBarEdit').disable();
							Ext.getCmp('eventTypeAdminToolBarDelete').disable();							
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
			helpMIF.setTitle('My Church Office Help : Event Types Administration');
			helpMIF.show();
			helpMIF.setSrc('help/eventtypes.php',false);			
		},
		iconCls: 'helpIcon'
	}]
});

var eventTypeAdminFormEdit = new Ext.FormPanel({
        labelWidth: 150,
        frame:false,
        bodyStyle:'padding:5px 5px 0',
        width: 350,
        defaults: {width: 230},
        defaultType: 'textfield',
        items: [{
                fieldLabel: 'Name',
                name: 'name',
                allowBlank:false
            },{
            	xtype:'textarea',
                fieldLabel: 'Description',
                name: 'descr'
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
			name:'event_type_id',
			id:'event_type_id',
			value:''			
		}
        ]
    });

var eventTypeAdminFormWindow = new Ext.Window({
	layout:'fit',
	title:'Add Event Type',
	width:450,
	height:200,
	closeAction:'hide',
	plain: true,	
	items:[eventTypeAdminFormEdit],
	buttons: [{
	    text:'Submit',
	    handler:function(){
	    	    eventTypeAdminFormEdit.getForm().submit({
			url:'/server/proxy/?proxy=admin/eventtypes.php',
			success:function(f,a){
				Ext.Msg.alert("Confirm",a.result.message);
				eventTypeAdminGridStore.load();
				eventTypeAdminFormEdit.getForm().reset();
				eventTypeAdminFormWindow.hide();
				Ext.getCmp('eventTypeAdminToolBarEdit').disable();
				Ext.getCmp('eventTypeAdminToolBarDelete').disable();				
			}
		    });	    
	    }
	},{
	    text: 'Close',
	    handler: function(){
		eventTypeAdminFormWindow.hide();
	    }
	}]
});

var eventTypeAdminGridStore = new Ext.data.JsonStore({	
	totalProperty: 'total',
	root: 'results',
	proxy: new Ext.data.HttpProxy({url:'/server/proxy/?proxy=admin/eventtypes.php',method:'GET'}),
	remoteSort:'true',
	id: 'eventTypeAdminGridStore',
	autoLoad:false,
	baseParams:{
		"limit":25
	},	
	fields:[
		'id',
		'name',
		'descr'
	]	
});


var eventTypeAdminGrid = new Ext.grid.GridPanel({
	store: eventTypeAdminGridStore,
	loadMask:false,
	frame:false,
	tbar:eventTypeAdminToolBar,
	columns: [
		{header: "ID", width: 30, sortable: true, dataIndex: 'id',hidden:true},
		{header: "Name", width: 100, sortable: true, dataIndex: 'name'},
		{header: "Description", width: 150, sortable: true, dataIndex: 'descr'}	
	  ],
	stripeRows: true,
	viewConfig: {
		forceFit: true
	},
	bbar: new Ext.PagingToolbar({
		store: eventTypeAdminGridStore,
		pageSize:25,
		displayInfo: true
	}),	
	listeners:{
		afterrender:function(){
			this.store.load();	
		},
		rowclick:function(){
			Ext.getCmp('eventTypeAdminToolBarEdit').enable();
			Ext.getCmp('eventTypeAdminToolBarDelete').enable();			
		}
		
	}
});

document.title = 'Event Types Admin';

var eventTypePanel = new Ext.Panel({
	id:'events-types-admin-tab',
	closable:true,
	title:'Event Types Admin',
	layout:'fit',
	items:[eventTypeAdminGrid]
});

Ext.onReady(function(){

		addTab(eventTypePanel);		

});
