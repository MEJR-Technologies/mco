<?php session_start(); ?>
<?php header('Content-Type: text/javascript'); ?>
var gatheringAdminToolBar = new Ext.Toolbar({
	items:[{
		text:'Add Gathering',
		handler:function(){
			gatheringsAdminFormWindow.show();
			gatheringsAdminForm.getForm().reset();
			gatheringsAdminForm.getForm().findField('recur_day').setValue(0);			
			gatheringsAdminForm.getForm().findField('action').setValue('add');
			
			//Set church_id
			<?php if(!$_SESSION['User']['site_admin']){ ?>
				gatheringsAdminForm.getForm().findField('church_id').setValue('<?php echo $_SESSION['User']['church_id']; ?>');
			<?php } ?>

			//Ext.getCmp('gatheringsAdminAddTabPanel').setActiveTab(0);						
			eventGatheringsMapStore.load();			
		}		
	},
	"-",
	{
		text:'Edit Gathering',
		id:'gatheringAdminToolBarEdit',
		disabled:true,
		handler:function(){
			var sel = gatheringsAdminGrid.getSelectionModel();
			var rec = sel.getSelected();
			gatheringsAdminFormWindow.show();			
			gatheringsAdminForm.getForm().findField('action').setValue('edit');			
			gatheringsAdminForm.getForm().findField('gathering_id').setValue(rec.data.id);			
			gatheringsAdminForm.load({
				url: '/server/proxy/?proxy=admin/gatherings.php',
				params:{
					"action":"load",
					"gathering_id":rec.data.id
				}				
			});
			eventGatheringsMapStore.load();
		}		
	},
	"-",
	{
		text:'Delete Gathering',
		disabled:true,
		id:'gatheringAdminToolBarDelete',		
		handler: function(){
			var sel = gatheringsAdminGrid.getSelectionModel();
			var rec = sel.getSelected();
			Ext.MessageBox.confirm('Confirmation','Delete this gathering?',function(btn,text){
				if(btn == 'yes'){
					Ext.Ajax.request({
						url: '/server/proxy/?proxy=admin/gatherings.php',
						params:{
							"action":"delete",
							"gathering_id":rec.data.id
						},
						success: function(){
							Ext.Msg.alert("Confirm", "Gathering Successfully Deleted.");
							gatheringsAdminGridStore.load();
							Ext.getCmp('gatheringAdminToolBarEdit').disable();
							Ext.getCmp('gatheringAdminToolBarDelete').disable();							
						}
					})
				}
			})
		}		
	},
	"->",
	{
		text:'Help',
		handler:function(){
			helpMIF.setTitle('My Church Office Help : Gatherings Administration');
			helpMIF.show();
			helpMIF.setSrc('help/gatherings.php',false);			
		},
		iconCls: 'helpIcon'
	}]
});

var eventGatheringsMapStore = new Ext.data.JsonStore({	
	totalProperty: 'total',
	root: 'results',
	proxy: new Ext.data.HttpProxy({url:'/server/proxy/?proxy=admin/events.php',method:'GET'}),
	remoteSort:'true',
	id: 'eventGatheringsMapStore',
	autoLoad:false,
	baseParams:{
		"limit":'',
		"action":"list"
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
	],
	listeners:{
		load:function(t,r,o){
			if(gatheringsAdminForm.getForm().findField('gathering_id').getValue()){
				Ext.Ajax.request({
					url: '/server/proxy/?proxy=admin/events.php',
					params: {
						action: "eventValuesList",
						gathering_id:gatheringsAdminForm.getForm().findField('gathering_id').getValue()
					},
					success: function(res,req){
						Ext.getCmp('event_ids').setValue(Ext.decode(res.responseText).map_values.split(','));						
					}
				})
			}
		}
	}
});

var gatheringsAdminForm = new Ext.FormPanel({
	layout:'form',
	labelWidth: 75,
        bodyStyle:'padding:5px 5px 0',
        defaults: {width: 230},
        defaultType: 'textfield',
	border:false,
	frame:false,
	plain:true,
	items:[{
		fieldLabel: 'Name',
		name: 'name',
		allowBlank:false
	},{
		xtype:'textarea',
		fieldLabel: 'Description',
		name: 'descr'
	},{
		xtype:'datefield',
		fieldLabel: 'Start',
		name: 'start'
	}, {
		xtype:'datefield',            	    
		fieldLabel: 'End',
		name: 'end'
	}, {
		xtype:'checkbox',
		fieldLabel: 'Recurring?',
		name: 'recur'            	    
	},{
		xtype:'combo',
		valueField: 'index',
		displayField:'day_of_week',    
		typeAhead: true,
		triggerAction: 'all',
		mode: 'local',
		allowBlank:true,
		store: new Ext.data.ArrayStore({
			fields: ['index','day_of_week'],
			data: [[0, 'Sunday'],[1, 'Monday'],[2, 'Tuesday'],[3, 'Wednesday'],[4, 'Thursday'],[5, 'Friday'],[6, 'Saturday']]
		}),
		hiddenName:'recur_day',
		fieldLabel: 'Recur Day',
		name: 'recur_day'
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
		name:'gathering_id',
		id:'gathering_id',
		value:''			
	},{
		xtype: 'multiselect',
		displayField:'name',
		valueField:'id',
		fieldLabel: 'Events',
		name: 'event_ids',
		id:'event_ids',
		hiddenName: 'event_ids',
		style:'margin-bottom:10px',
		scroll:true,
		autoscroll:true,
		width: 230,
		height: 200,
		allowBlank:true,
		store:eventGatheringsMapStore,
		tbar:[{
			text: 'Unselet All',
			handler: function(){
				gatheringsAdminForm.getForm().findField('event_ids').reset();
			}
		}]
	}]
});

var gatheringsAdminFormWindow = new Ext.Window({
	layout:'fit',
	title:'Add Gathering',
	width:400,
	height:500,
	closeAction:'hide',
	plain: true,	
	items:[gatheringsAdminForm],
	buttons: [{
	    text:'Submit',
	    handler:function(){
	    	    gatheringsAdminForm.getForm().submit({
			url:'/server/proxy/?proxy=admin/gatherings.php',
			success:function(f,a){
				Ext.Msg.alert("Confirm",a.result.message);
				gatheringsAdminGridStore.load();
				gatheringsAdminFormWindow.hide();
				Ext.getCmp('gatheringAdminToolBarEdit').disable();
				Ext.getCmp('gatheringAdminToolBarDelete').disable();				
			}
		    });	    
	    }
	},{
	    text: 'Close',
	    handler: function(){
		gatheringsAdminFormWindow.hide();
	    }
	}]
});

var gatheringsAdminGridStore = new Ext.data.JsonStore({	
	totalProperty: 'total',
	root: 'results',
	proxy: new Ext.data.HttpProxy({url:'/server/proxy/?proxy=admin/gatherings.php',method:'GET'}),
	remoteSort:'true',
	id: 'gatheringsAdminGridStore',
	autoLoad:false,
	baseParams:{
		"limit":25
	},	
	fields:[
		'id',
		'name',
		'descr',
		{name: 'start', type: 'date', dateFormat: 'Y-m-d h:i:s'},
		{name: 'end', type: 'date', dateFormat: 'Y-m-d h:i:s'},
		'recur',
		'recur_day',
		{name: 'date_added', type: 'date', dateFormat: 'Y-m-d h:i:s'}
	]	
});


var gatheringsAdminGrid = new Ext.grid.GridPanel({
	store: gatheringsAdminGridStore,
	loadMask:false,
	frame:false,
	tbar:gatheringAdminToolBar,
	columns: [
		{header: "ID", width: 30, sortable: true, dataIndex: 'id',hidden:true},
		{header: "Name", width: 100, sortable: true, dataIndex: 'name'},
		{header: "Description", width: 150, sortable: true, dataIndex: 'descr'},
		{header: "Start", width: 50, sortable: true, dataIndex: 'start',renderer: Ext.util.Format.dateRenderer('m/d/Y')},
		{header: "End", width: 50, sortable: true, dataIndex: 'end',renderer: Ext.util.Format.dateRenderer('m/d/Y')},
		{header: "Recurring", width: 30, sortable: true, dataIndex: 'recur',renderer:returnYesNo},
		{header: "Day Recurring", width: 50, sortable: true, dataIndex: 'recur_day', renderer:returnDay},
		{header: "Date Added", width: 30, sortable: true, dataIndex: 'date_added',renderer: Ext.util.Format.dateRenderer('m/d/Y')}		
	  ],
	stripeRows: true,
	viewConfig: {
		forceFit: true
	},
	bbar: new Ext.PagingToolbar({
		store: gatheringsAdminGridStore,
		pageSize:25,
		displayInfo: true
	}),	
	listeners:{
		afterrender:function(){
			this.store.load();	
		},
		rowclick:function(){
			Ext.getCmp('gatheringAdminToolBarEdit').enable();
			Ext.getCmp('gatheringAdminToolBarDelete').enable();			
		}
		
	}
});

document.title = 'Gatherings Admin';

var gatheringsPanel = new Ext.Panel({
	id:'gatheringsAdminPanel',
	closable:true,
	title:'Gatherings Admin',
	layout:'fit',
	items:[gatheringsAdminGrid],
	listeners:{
		'beforeclose':function(p){
			//This seems to be necessary, without it if a user closes the gatherings tab and then re-opens it
			//the events select list is not rendered. It seems as though the item was not being destroyed?
			Ext.getCmp('event_ids').destroy();
		}
	}
});

Ext.onReady(function(){

		addTab(gatheringsPanel);		

});
