function do_ajax_request(){

  var par;
 
	switch(arguments.length){
		case 1:
			url = arguments[0];
			meth = 'get';
			break;
		case 2:
			url = arguments[0];
			meth = arguments[1];
		  break;
		case 3:
			url = arguments[0];
			meth = arguments[1];
			par = arguments[2];
		  break;
	  default:
			url = '';
			meth = '';
	}
	
	switch (meth){
		case 'get':
		  met = 'GET';
			if (par===undefined) {par = '';}
			break;
		case 'post':
		  met = 'POST';
			if (par===undefined) {par = '';}
			break;
	  case 'put':
			met = 'POST';
			if (par===undefined) {par = { _method: 'PUT' };}
			break;
	  case 'delete':
			met = 'POST';
			if (par===undefined) {par = { _method: 'DELETE' };}
			break;
		default:	
		  met = 'GET';
			if (par===undefined) {par = '';}
			break;
	}

	// alert('URL:'+url+' meth:'+meth+' par:'+Ext.util.JSON.encode(par));

	Ext.Ajax.request({
	   url: url,
	   method: met,
	   params: par,
	   failure: function(request){
	   	alert('Failure: ' + response.responseText);
	   },
	   success:function(response){
	   	eval(response.responseText);
	   }
	});
	
	Ext.Ajax.on('requestexception', this.showexceptionmessage, this);
}

var adminNavTree = new Ext.tree.TreePanel({
	id: 'adminNavTree',
	border:false,
	autoScroll: true,
	rootVisible: false,
	lines: false,
	useArrows: false,
	collapsible:false,
	loadMask:true,
	loader: new Ext.tree.TreeLoader({
	    dataUrl:'/server/proxy/adminNavTree.json'
	}),
	root: new Ext.tree.AsyncTreeNode()
});

adminNavTree.on('click', function(n){
	var adminNavTreeArr = n.id.split('-');
	var title = adminNavTreeArr[1];
	var url = '/js/modules/' + adminNavTreeArr[2];	
	var sn = this.selModel.selNode || {};
	
	if(n.leaf && n.id != sn.id){			
		//loadTab(title,url);
		do_ajax_request(url,'GET')
	}
});

var sysadminNavTree = new Ext.tree.TreePanel({
	id: 'sysadminNavTree',
	border:false,
	autoScroll: true,
	rootVisible: false,
	lines: false,
	useArrows: false,
	collapsible:false,
	loadMask:true,
	loader: new Ext.tree.TreeLoader({
	    dataUrl:'/server/proxy/sysadminNavTree.json'
	}),
	root: new Ext.tree.AsyncTreeNode()
});

sysadminNavTree.on('click', function(n){
	var sysadminNavTreeArr = n.id.split('-');
	var title = sysadminNavTreeArr[1];
	var url = '/js/modules/' + sysadminNavTreeArr[2];	
	var sn = this.selModel.selNode || {};
	
	if(n.leaf && n.id != sn.id){			
		//loadTab(title,url);
		do_ajax_request(url,'GET')
	}
});

var appNavTree = new Ext.tree.TreePanel({
	id: 'appNavTree',
	border:false,
	autoScroll: true,
	rootVisible: false,
	lines: false,
	useArrows: false,
	collapsible:false,
	loadMask:true,
	loader: new Ext.tree.TreeLoader({
	    dataUrl:'/server/proxy/appNavTree.json'
	}),
	root: new Ext.tree.AsyncTreeNode()
});

appNavTree.on('click', function(n){
	var appNavTreeArr = n.id.split('-');
	var title = appNavTreeArr[1];
	var url = '/js/modules/' + appNavTreeArr[2];	
	var sn = this.selModel.selNode || {};
	
	if(n.leaf && n.id != sn.id){			
		//loadTab(title,url);
		do_ajax_request(url,'GET')
	}
});

function loadTab(title,url){
	var tabs = Ext.getCmp('mainTabPanel');
	var openTabs = tabs.items.items;
	
	var min = 0;
	var max = 10000;
	var makeTab = true;
	var i;
	
	for (i=0; i < openTabs.length; i++) {
		if (openTabs[i].title == title) {
			makeTab = openTabs[i];
		}
	}
	
	// If tab exists set to that tab. else create new tab and set that tab to active tab. //
	if (makeTab == true) {
		var newId = 'tab'+Math.floor(Math.random() * (max - min + 1)) + min;
		var newTab = new Ext.Panel( {
				title:title,
				collapsible:false,	                                
				closable:true,
				id:newId,
				border:false,
				html:'<iframe id="frame_'+newId+'" name="frame_'+newId+'" src="'+url+'" style="height:100%; width:100%; border:1" frameborder="true"></iframe>'
		})
		
		tabs.add(newTab);
		tabs.activate(newTab);
	} else {
		document.getElementById('frame_'+makeTab.id).src = url;
		tabs.activate(makeTab);
	}
	
}
