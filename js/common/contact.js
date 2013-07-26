	storeFeedbackCat = new Ext.data.SimpleStore({
		id:'storeFeedbackCat',
		remoteSort : false,
		fields: ['id','category'],
		data : [
			['Help','Help'],
			['Suggestion','Suggestion'],
			['New Feature','New Feature',],
			['New Application','New Application',],
			['Bug','Bug',]
		]
	});	
						
   var contactForm = new Ext.form.FormPanel({
	  title:"",
	  width:425,
	  frame:true,
	  items: [
			new Ext.form.TextField({
					id:"subject",
					fieldLabel:"Subject",
					width:275,
					allowBlank:false,
					required:true,						
					blankText:"Please enter a subject address"
			})
			,new Ext.form.ComboBox({
					fieldLabel: 'Category',
					hiddenName:'category',
					store: storeFeedbackCat,
					valueField:'id',
					displayField:'category',
					allowBlank: false,
					required:true,						
					typeAhead: true,
					mode: 'local',
					triggerAction: 'all',
					emptyText:'Select a Category...',
					selectOnFocus:true,
					width:275
			})			 
			 ,new Ext.form.TextArea({
					id:"message",
					fieldLabel:"Message",
					required:true,					
					width:275,
					height:75
			}),
			{
				xtype:'hidden',
				name:'action',
				id:'action',
				value:''			
			}			 
		],
		buttons: [
			{
				text:"Cancel",
				handler: function () {
					contactWindow.hide();
				}
			},{
				text:"Save",
				handler:function(){
				    contactForm.getForm().submit({
					url:'/server/proxy/?proxy=contact.php',
					success:function(f,a){
						Ext.Msg.alert("Confirm",a.result.message);
						contactForm.getForm().reset();
						contactWindow.hide();				
					}
				    });	    
				}
			}
		]
	});
	
	var contactWindow = new Ext.Window({
		  title: 'My Church Office: Feedback',
		  layout: 'fit',                 
		  height: 225,
		  width: 425,                            
		  closable: true,
		  modal:true,
		  resizable: false,                              
		  draggable: false,
		  closeAction:'hide',
		  items: [contactForm]                     
	});	
