regStatusChart = function(){

    regStatusChart.superclass.constructor.call(this, {
    	id:'regStatusChartDashboardChard',
        // Panel for the chart
        var nav = new Ext.Panel({
		title:'Registration Status',
		collapsible:true,	                                
		closable:false,
		border:false,
		layout:'fit',
		height:'auto',
		//html: 'regStatusChart.html'
		html:'<iframe id="regStatus" name="regStatus" src="regStatusChart.html" style="height:100%; width:100%; border:0px;" frameborder="true"></iframe>'
        });
    });
}

Ext.extend(regStatusChart, Ext.Panel);
