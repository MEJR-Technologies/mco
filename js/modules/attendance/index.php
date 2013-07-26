<?php session_start(); ?>
<?php header('Content-Type: text/javascript'); ?>
	var attendanceReportEvents = new Ext.data.JsonStore({	
		totalProperty: 'total',
		root: 'results',
		proxy: new Ext.data.HttpProxy({url:'/server/proxy/?proxy=admin/events.php',method:'POST'}),
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
		]
	});

	var attendanceFormAdd = new Ext.FormPanel({
		labelWidth: 150,
		frame:false,
		bodyStyle:'padding:5px 5px 0',
		width: 350,
		defaults: {width: 230},
		defaultType: 'textfield',
		items: [{
			xtype:'combo',
			valueField: 'id',
			displayField:'name',           	
			typeAhead: true,
			triggerAction: 'all',
			mode: 'local',
			store: new Ext.data.JsonStore({
					url: '/server/proxy/?proxy=admin/gatherings.php',
					root: 'results',
					autoLoad:true,
					fields: ['id', 'name']
				}),
			hiddenName:'gathering_id',		
			fieldLabel: 'Select Gathering',
			name: 'gathering_id',
			listeners:{
				select:function(c,r,i){
					Ext.getCmp('attendance_event_combo').enable();
					Ext.getCmp('attendance_event_combo').store.load({params:{gathering_id:r.data.id,action:"attendance"}});
					//r.data.id;
				}
			}
		},{
			xtype:'combo',
			valueField: 'id',
			displayField:'name',           	
			typeAhead: true,
			disabled:true,
			triggerAction: 'all',
			mode: 'local',
			store: new Ext.data.JsonStore({
					url: '/server/proxy/?proxy=admin/events.php',
					root: 'results',
					autoLoad:false,
					fields: ['id', 'name']
				}),
			hiddenName:'event_id',		
			fieldLabel: 'Select Event',
			name: 'event_id',
			id:'attendance_event_combo'
		},{
			xtype:'datefield',
			fieldLabel: 'Date',
			name: 'event_date'			
		},{
			fieldLabel: 'Attendance Count',
			name: 'count',
			allowBlank:false			
		},{
			xtype:'hidden',
			name:'action',
			value:'add'			
		}]
	    });
	
	var attendanceFormWindow = new Ext.Window({
		layout:'fit',
		title:'Add Attendance Record',
		width:450,
		height:200,
		closeAction:'hide',
		plain: true,	
		items:[attendanceFormAdd],
		buttons: [{
		    text:'Submit',
		    handler:function(){
			attendanceFormAdd.getForm().submit({
				url:'/server/proxy/?proxy=attendance.php',
				success:function(f,a){
					Ext.Msg.alert("Confirm",a.result.message);
					//gatheringsAdminGridStore.load();
					attendanceFormAdd.getForm().reset();
					attendanceFormWindow.hide();
					attendanceStore.load();
				}
			});			    
		    }
		},{
		    text: 'Close',
		    handler: function(){
			attendanceFormWindow.hide();
		    }
		}]
	});	

    Ext.QuickTips.init();

    var xg = Ext.grid;
    
	var attendanceStore = new Ext.data.GroupingStore({
		proxy: new Ext.data.HttpProxy({
			url: '/server/proxy/?proxy=attendance.php',
			method: 'POST',
			autoLoad:true
		}),
		baseParams:{
			"limit":25
		},
		remoteSort:true,
		reader: new Ext.data.JsonReader({
			root: 'results',
			totalProperty: 'total'
		},
		[
		    {name: 'gathering_id', type: 'int'},
		    {name: 'gathering_name', type: 'string'},
		    {name: 'event_id', type: 'int'},
		    {name: 'event_name', type: 'string'},
		    {name: 'attendance_count', type: 'int'},
		    {name: 't_stamp', type: 'date', dateFormat:'Y-m-d h:i:s'},
		    {name: 'teacher', type: 'string'},
		    'attendance_id'
		]
		),
		sortInfo:{field: 't_stamp', direction: 'DESC'},
		groupField:'t_stamp',
		groupOnSort: true
	});

    // define a custom summary function
    Ext.ux.grid.GroupSummary.Calculations['totalCost'] = function(v, record, field){
        return v + (record.data.estimate * record.data.rate);
    };

	// utilize custom extension for Group Summary
    var summary = new Ext.ux.grid.GroupSummary();

    var grid = new Ext.grid.GridPanel({
        ds: attendanceStore,
	border:false,	
        columns: [
				{
                header: 'Gathering Name',
                width: 50,
                sortable: false,
                dataIndex: 'gathering_name'
            },{
                id: 'description',
                header: 'Event',
                width: 80,
                sortable: false,
                dataIndex: 'event_name',
                summaryType: 'count',
                hideable: false,
                summaryRenderer: function(v, params, data){
                    return ((v === 0 || v > 1) ? '(' + v +' Events)' : '(1 Event)');
                }
            },{
                header: 'Head Count',
                width: 20,
                sortable: false,
                dataIndex: 'attendance_count',
                summaryType: 'sum',
				xtype:'numbercolumn',
				format:'0,000'
            },{
                header: 'Date',
                width: 20,
                sortable: false,
                renderer: Ext.util.Format.dateRenderer('m/d/Y'),
                dataIndex: 't_stamp'
            },{
                header: 'Teacher',
                width: 20,
                sortable: false,
                dataIndex: 'teacher'
            }
        ],

        view: new Ext.grid.GroupingView({
            forceFit: true,
            showGroupName: false,
            enableNoGroups: false,
			enableGroupingMenu: false,
            hideGroupedColumn: true,
            emptyText:'No Attendance Records Available'            
        }),
	bbar: new Ext.PagingToolbar({
		store: attendanceStore,
		pageSize:25,
		displayInfo: true
	}),	
        plugins: summary,
	listeners:{
		afterrender:function(){
			this.store.load({params:{action:"listRecords"}});	
		},
		rowclick:function(){
			Ext.getCmp('attendanceToolBarDelete').enable();			
		}		
	},
        tbar : [{
            text: 'Toggle',
            tooltip: 'Toggle the visibility of summary row',
            handler: function(){summary.toggleSummaries();}
        },
	"-",
	{
		text:'Add Attendance Record',	
		handler:function(){
			attendanceFormAdd.getForm().reset();
			attendanceFormAdd.getForm().findField('action').setValue('add');			
			attendanceFormWindow.show();			
		}
	},
	"-",
	{
		text:'Delete Attendance Record',
		disabled:true,
		id:'attendanceToolBarDelete',		
		handler: function(){
			var sel = grid.getSelectionModel();
			var rec = sel.getSelected();
			Ext.MessageBox.confirm('Confirmation','Delete this attendance record?',function(btn,text){
				if(btn == 'yes'){
					Ext.Ajax.request({
						url: '/server/proxy/?proxy=attendance.php',
						params:{
							"action":"delete",
							"attendance_id":rec.data.attendance_id
						},
						success: function(){
							Ext.Msg.alert("Confirm", "Attendance Record Successfully Deleted.");
							attendanceStore.load();
							Ext.getCmp('attendanceToolBarDelete').disable();							
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
			helpMIF.setTitle('My Church Office Help : Attendance');			
			helpMIF.show();
			helpMIF.setSrc('help/attendance.php');			
			helpMIF.setSrc();			
		},
		iconCls: 'helpIcon'
	}],

        frame: false,
        clicksToEdit: 1,
        collapsible: false,
        animCollapse: false,
        trackMouseOver: true,
        //enableColumnMove: false,
        iconCls: 'icon-grid'
    });

//});

var reportPiePanel = {
	xtype:'fusion',	
	collapsible: false,
	id:'reportPanelChartPie',
	floating:false,
	autoSize:false,
	autoScroll:false,
	border:false,       
	fusionCfg:{
		params:{
			flashVars:{
				debugMode:0,
				autoScale:false                       
			}
		}                          
	},
	chartURL:'media/FCF_Pie3D.swf',
	dataURL:'/server/proxy/?proxy=xml2.php&noData=1',
	mediaMask:{msg:'Loading Chart Object'},
	autoMask:true,
	width:500,
	height:350
 };
 
 var reportColumnPanel = {
	xtype:'fusion',	
	collapsible: false,
	id:'reportPanelChartColumn',
	floating:false,
	autoSize:false,
	autoScroll:false,
	border:false,       
	fusionCfg:{
		params:{
			flashVars:{
				debugMode:0,
				autoScale:false                       
			}
		}                          
	},
	chartURL:'media/FCF_Column3D.swf',
	dataURL:'/server/proxy/?proxy=xml2.php&noData=1',
	mediaMask:{msg:'Loading Chart Object'},
	autoMask:true,
	width:500,
	height:350
 };

//Create card layout panel here
//This panel will hold the attendance grid and the report panel
var attendancePanelCard = {
	layout:'card',
	deferredRender:true,
	id:'attendancePanelCard',
	activeItem: 0,
	items: [grid,reportPiePanel,reportColumnPanel]
}

document.title = 'Attendance';

var attendancePanel = new Ext.Panel({
	id:'attendance-panel',
	closable:true,
	title:'Attendance',
	//layout:'fit',
    layout:'border',
    border:false,
    style:'background:#FFFFFF;',
	items:[{
        layout:'fit',
        region:'center',
        border:false,
        items:[attendancePanelCard]
    },{
        layout:'fit',
        region:'east',
        collapsible:true,
        split:true,
        width:225,
        title:'Reporting',
        items:[{
            xtype:'form',
            id:"reportFormPanel",
            autoScroll: true,
            bodyStyle:"padding:10px;",
            border:false,
            layout:'form',
            labelAlign: "top",
            labelWidth:100,
            width:200,
            defaultType:'textfield',
	    buttons: [{
		icon: "images/icons/application_form_magnify.png",
		text: "Display Report",
		cls:"x-btn-text-icon x-btn-icon-text",
		handler: function () {
			if(Ext.getCmp('start_date').getValue() && Ext.getCmp('end_date').getValue()){
				if(Ext.getCmp('start_date').getValue() > Ext.getCmp('end_date').getValue()){
					Ext.MessageBox.alert('Error', 'The Start Date cannot be after the End Date');
					return;
				}
			}
			
			if(!Ext.getCmp('a_r_gathering_id').getValue()){
					Ext.MessageBox.alert('Error', 'You must select a gathering.');
					return;				
			}
			
			
			if(Ext.getCmp('rb-chart-type').getValue().getGroupValue() == 'pie'){
				Ext.getCmp('attendancePanelCard').getLayout().setActiveItem(1);				
			} else {
				Ext.getCmp('attendancePanelCard').getLayout().setActiveItem(2);				
			}
			
			Ext.Ajax.request({
				   url:'/server/proxy/?proxy=xml2.php',
				   params:{
					   start_date:Ext.getCmp('start_date').getValue(),
					   end_date:Ext.getCmp('end_date').getValue(),
					   gathering_id:Ext.getCmp('a_r_gathering_id').getValue(),
					   event_ids:Ext.getCmp('a_r_event_ids').getValue(),
					   chart_type:Ext.getCmp('rb-chart-type').getValue().getGroupValue()
				   },
				   success:function(response){
				   },
				   callback:function(o,s,r){
					   if(Ext.getCmp('rb-chart-type').getValue().getGroupValue() == 'pie'){
						   Ext.getCmp('reportPanelChartPie').setChartData(r.responseText);
					   } else {
						   Ext.getCmp('reportPanelChartColumn').setChartData(r.responseText);						   
					   }
				   }
			});
			
			Ext.getCmp('filterGridButton').enable();
		}
	    },{
		icon: "images/icons/database_refresh.png",
		text: "Display Grid",
		cls:"x-btn-text-icon x-btn-icon-text",
		disabled:true,
		id:'filterGridButton',
		handler: function () {						
			//alert('Clear Filters');
			Ext.getCmp('attendancePanelCard').getLayout().setActiveItem(0);
			Ext.getCmp('filterGridButton').disable();			
		}
	    }],	    
            items:[
                new Ext.form.DateField({
                    fieldLabel:'Start Date',
                    id:'start_date',
                    labelStyle:'font-weight:bold;',
                    name:'start_date',
                    width:180
                }),
                new Ext.form.DateField({
                    fieldLabel:'End Date',
                    id:'end_date',
                    labelStyle:'font-weight:bold;',
                    name:'end_date',
                    width:180		    
                }),{
			xtype:'combo',
			valueField: 'id',
			displayField:'name',
			width:180,
			typeAhead: true,
			triggerAction: 'all',
			mode: 'local',
			emptyText:'Select a Gathering',			
			store: new Ext.data.JsonStore({
					url: '/server/proxy/?proxy=admin/gatherings.php',
					root: 'results',
					autoLoad:true,
					fields: ['id', 'name']
				}),
			editable:false,
			fieldLabel: 'Select Gathering',
			labelStyle: 'font-weight:bold;',			
			id:'a_r_gathering_id',
			listeners:{
				select:function(c,r,i){
					Ext.getCmp('a_r_event_ids').enable();
					Ext.getCmp('a_r_event_ids').reset();					
					Ext.getCmp('a_r_event_ids').store.load({params:{gathering_id:r.data.id,action:"attendance"}});
				}
			}
		},{
			xtype: 'multiselect',
			displayField:'name',
			valueField:'id',
			disabled:true,
			fieldLabel: 'Events',
			labelStyle: 'font-weight:bold;',			
			name: 'a_r_event_ids',
			id:'a_r_event_ids',
			hiddenName: 'event_ids',
			style:'margin-bottom:10px',
			scroll:true,
			autoscroll:true,
			width: 180,
			height: 200,
			allowBlank:true,
			store:attendanceReportEvents,
			tbar:[{
				text: 'Unselet All',
				handler: function(){
					gatheringsAdminForm.getForm().findField('event_ids').reset();
				}
			}]
		},{
			xtype: 'radiogroup',
			fieldLabel: 'Chart Type',
			labelStyle: 'font-weight:bold;',
			id:'rb-chart-type',			
			items: [
				{boxLabel: 'Pie',name: 'rb-chart-type', inputValue:'pie', value:'pie', checked: true},
				{boxLabel: 'Column', name: 'rb-chart-type', inputValue:'column', value:'column'}
			]
		}		
            ]	    
        }]
    }]
});

Ext.onReady(function(){

		addTab(attendancePanel);		

});
