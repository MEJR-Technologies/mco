AttendanceGrid = function(limitColumns){

    function italic(value){
        return '<i>' + value + '</i>';
    }

    function pctChange(val){
        if(val > 0){
            return '<span style="color:green;">' + val + '%</span>';
        }else if(val < 0){
            return '<span style="color:red;">' + val + '%</span>';
        }
        return val;
    }


    var columns = [
    	{id:'service', header: "Service", width: 160, sortable: true, dataIndex: 'name'},
        {header: "Last Record", width: 75, sortable: true, dataIndex: 'last_rec'},
        {header: "YTD Total", width: 75, sortable: true, dataIndex: 'ytd_total'},
        {header: "YTD Average", width: 75, sortable: true, dataIndex: 'ytd_avg'},        
        {header: "Current Month Average", width: 75, sortable: true, dataIndex: 'mon_avg'},
        {header: "YTD Monthly Average", width: 75, sortable: true, dataIndex: 'ytd_mon_avg'}            
    ];

    // allow samples to limit columns
    if(limitColumns){
        var cs = [];
        for(var i = 0, len = limitColumns.length; i < len; i++){
            cs.push(columns[limitColumns[i]]);
        }
        columns = cs;
    }

    AttendanceGrid.superclass.constructor.call(this, {
    	id:'attendanceDashboardGrid',
        store: new Ext.data.JsonStore({	
        	totalProperty: 'total',
        	root: 'results',
        	proxy: new Ext.data.HttpProxy({url:'/server/proxy/?proxy=attendance.php',method:'POST'}),
        	remoteSort:'true',
        	id: 'attendanceDashboardGridStore',
        	autoLoad:false,
        	fields:[
			'gathering_id',
			'name',
			'last_rec',
			'ytd_total',
			'ytd_avg',			
			'mon_avg',
			'ytd_mon_avg'
		]	
	}),
        columns: columns,
        autoExpandColumn: 'service',
        loadMask:true,
        viewConfig:{
        	forceFit:true,
        	emptyText:'No Attendance Records Available'        	
        },
        height:125,
        listeners:{
		afterrender:function(){
			this.store.load({params:{action:"dashboard"}});	
		}	
        }
    });
}

Ext.extend(AttendanceGrid, Ext.grid.GridPanel);
