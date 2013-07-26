var passwordForm = new Ext.form.FormPanel({
  title:"",
  width:425,
  frame:true,
  items:[
	new Ext.form.TextField({
			id:'email',
			fieldLabel:"Email Address",
			name:'email',
			width:275,
			allowBlank:false,
			required:true,
			vtype:'email',
			emailText:'This field should be an e-mail address in the format "user@example.com"',
			blankText:"Please enter your email address."
	})],
	buttons: [{
			text:"Cancel",
			handler: function () {
				passwordWindow.hide();
			}
		},{
			text:"Reset Password",
			handler:function(){
			    passwordForm.getForm().submit({
				url:'/server/proxy/?proxy=password.php',
				success:function(f,a){
					Ext.Msg.alert("Confirm",a.result.message);
					passwordForm.getForm().reset();
					passwordWindow.hide();				
				},
				failure:function(f,a){
					Ext.Msg.alert("Error",a.result.message);
					passwordForm.getForm().reset();					
				}
			    });	    
			}
		}]
});

var passwordWindow = new Ext.Window({
	  title: 'My Church Office: Forgot Password',
	  layout: 'fit',                 
	  height: 100,
	  width: 425,                            
	  closable: true,
	  modal:true,
	  resizable: false,                              
	  draggable: false,
	  closeAction:'hide',
	  items: [passwordForm]                     
});

function forgotPassword(){
	passwordWindow.show();	
}
