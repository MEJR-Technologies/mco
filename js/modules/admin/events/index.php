<?php session_start(); ?>
<?php header('Content-Type: text/javascript'); ?>
var eventAdminToolBar = new Ext.Toolbar({
	items:[{
		text:'Add Event',	
		handler:function(){
			eventAdminFormEdit.getForm().reset();			
			eventAdminFormEdit.getForm().findField('action').setValue('add');
			
			//Set church_id
			<?php if(!$_SESSION['User']['site_admin']){ ?>
				eventAdminFormEdit.getForm().findField('church_id').setValue('<?php echo $_SESSION['User']['church_id']; ?>');
			<?php } ?>

			eventAdminFormWindow.show();
		}
	},
	"-",
	{
		text:'Edit Event',
		id:'eventAdminToolBarEdit',
		disabled:true,
		handler:function(){
			var sel = eventAdminGrid.getSelectionModel();
			var rec = sel.getSelected();			
			eventAdminFormWindow.show();
			eventAdminFormEdit.getForm().findField('action').setValue('edit');
			eventAdminFormEdit.getForm().findField('event_id').setValue(rec.data.id);			
			eventAdminFormEdit.load({
				url: '/server/proxy/?proxy=admin/events.php',
				params:{
					"action":"load",
					"event_id":rec.data.id
				}				
			});
		}		
	},
	"-",
	{
		text:'Delete Event',
		disabled:true,
		id:'eventAdminToolBarDelete',		
		handler: function(){
			var sel = eventAdminGrid.getSelectionModel();
			var rec = sel.getSelected();
			Ext.MessageBox.confirm('Confirmation','Delete this event?',function(btn,text){
				if(btn == 'yes'){
					Ext.Ajax.request({
						url: '/server/proxy/?proxy=admin/events.php',
						params:{
							"action":"delete",
							"event_id":rec.data.id
						},
						success: function(){
							Ext.Msg.alert("Confirm", "Event Successfully Deleted.");
							eventAdminGridStore.load();
							Ext.getCmp('eventAdminToolBarEdit').disable();
							Ext.getCmp('eventAdminToolBarDelete').disable();							
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
			helpMIF.setTitle('My Church Office Help : Events Administration');
			helpMIF.show();
			helpMIF.setSrc('help/events.php',false);			
		},
		iconCls: 'helpIcon'
	}]
});

var eventAdminFormEdit = new Ext.FormPanel({        
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
			xtype:'checkbox',
			fieldLabel: 'Attendance Data?',
			name: 'attendance_data'            	    
		},{
			xtype:'checkbox',
			fieldLabel: 'Offering Data?',
			name: 'offering_data'            	    
		},{
			fieldLabel: 'Teacher',
			name: 'teacher',
			allowBlank:false
		},{
			fieldLabel: 'Event Subject',
			name: 'event_subject',
			allowBlank:false
		},{
			xtype:'combo',
			valueField: 'id',
			displayField:'name',           	
			typeAhead: true,
			triggerAction: 'all',
			mode: 'local',
			store: new Ext.data.JsonStore({
				    url: '/server/proxy/?proxy=admin/eventtypes.php',
				    root: 'results',
				    autoLoad:true,
				    fields: ['id', 'name']
				}),
			hiddenName:'event_type_id',		
			fieldLabel: 'Event Type',
			name: 'event_type_id'	    
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
			name:'event_id',
			id:'event_id',
			value:''			
		}]
    });

var eventAdminFormWindow = new Ext.Window({
	layout:'fit',
	title:'Add Event',
	width:450,
	height:350,
	closeAction:'hide',
	plain: true,	
	items:[eventAdminFormEdit],
	buttons: [{
	    text:'Submit',
	    handler:function(){
	    	    eventAdminFormEdit.getForm().submit({
			url:'/server/proxy/?proxy=admin/events.php',
			success:function(f,a){
				Ext.Msg.alert("Confirm",a.result.message);
				eventAdminGridStore.load();
				eventAdminFormEdit.getForm().reset();
				eventAdminFormWindow.hide();
				Ext.getCmp('eventAdminToolBarEdit').disable();
				Ext.getCmp('eventAdminToolBarDelete').disable();				
			}
		    });	    
	    }
	},{
	    text: 'Close',
	    handler: function(){
		eventAdminFormWindow.hide();
	    }
	}]
});

var eventAdminGridStore = new Ext.data.JsonStore({	
	totalProperty: 'total',
	root: 'results',
	proxy: new Ext.data.HttpProxy({url:'/server/proxy/?proxy=admin/events.php',method:'POST'}),
	remoteSort:'true',
	id: 'eventAdminGridStore',
	autoLoad:false,
	baseParams:{
		"limit":25
	},	
	fields:[
		'id',
		'name',
		'descr',
		{name: 'date_added', type: 'date', dateFormat: 'Y-m-d h:i:s'},
		'attendance_data',
		'offering_data',
		'teacher',
		'event_subject',
		'event_type'		
	]	
});

var eventAdminGrid = new Ext.grid.GridPanel({
	store: eventAdminGridStore,
	loadMask:false,
	frame:false,
	tbar:eventAdminToolBar,
	columns: [
		{header: "ID", width: 30, sortable: true, dataIndex: 'id',hidden:true},
		{header: "Name", width: 100, sortable: true, dataIndex: 'name'},
		{header: "Description", width: 150, sortable: true, dataIndex: 'descr'},
		{header: "Date Added", width: 50, sortable: true, dataIndex: 'date_added',renderer: Ext.util.Format.dateRenderer('m/d/Y')},
		{header: "Attendance Data?", width: 30, sortable: true, dataIndex: 'attendance_data',renderer:returnYesNo},
		{header: "Offering Data?", width: 30, sortable: true, dataIndex: 'offering_data',renderer:returnYesNo},
		{header: "Teacher", width: 150, sortable: true, dataIndex: 'teacher'},
		{header: "Subject", width: 150, sortable: true, dataIndex: 'event_subject'},
		{header: "Event Type", width: 150, sortable: true, dataIndex: 'event_type'}		
	  ],
	stripeRows: true,
	viewConfig: {
		forceFit: true,
		emptyText:'No Events Defined',
  		deferEmptyText:false
	},
	bbar: new Ext.PagingToolbar({
		store: eventAdminGridStore,
		pageSize:25,
		displayInfo: true
	}),	
	listeners:{
		afterrender:function(){
			this.store.load();	
		},
		rowclick:function(){
			Ext.getCmp('eventAdminToolBarEdit').enable();
			Ext.getCmp('eventAdminToolBarDelete').enable();			
		}
		
	}
});

document.title = 'Events Admin';

var eventPanel = new Ext.Panel({
	id:'events-admin-tab',
	closable:true,
	title:'Events Admin',
	layout:'fit',
	items:[eventAdminGrid]
});

Ext.onReady(function(){

		addTab(eventPanel);		

});
