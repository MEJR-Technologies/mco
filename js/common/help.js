var helpMIF = new Ext.ux.ManagedIFrame.Window({
	defaultSrc:'help/',
	height:500,
	width:500,
	modal:false,
	loadMask:true,
	resizable:false,
	closeAction:'hide',
	title:'My Church Office Help',
	buttons: [{
	    text: 'Close',
	    handler: function(){
		helpMIF.hide();
	    }
	}]	
});
