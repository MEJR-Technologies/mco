var pTools = [/*{
	id:'gear'
	,handler: function(){
		//Ext.Msg.alert('Dashboard Settings', 'The settings for each dashboard module will be available here.');
		new Ext.ToolTip({
			target: 'tip2',
			html: 'Click the X to close me',
			title: 'My Tip Title',
			autoHide: false,
			closable: true,
			draggable:true
		}).show();		
	}
},*/{
	id:'help'
	,handler:function(){
				helpMIF.setTitle('My Church Office Help : Attendance Dashboard');
				helpMIF.show();
				helpMIF.setSrc('help/attendanceDashboard.php',false);				
			}	
},{
	id:'refresh'
	,handler: function(){
		//Ext.Msg.alert('Refresh Grid', 'The data in the grid will be refreshed.');
		Ext.getCmp('attendanceDashboardGrid').store.load({params:{action:"dashboard"}});
	}
}];
