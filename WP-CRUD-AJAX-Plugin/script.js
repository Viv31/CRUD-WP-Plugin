jQuery(document).ready(function(){

// AJAX url
  var ajax_url = plugin_ajax_object.ajax_url;

//Inserting data from form checking insert click and perform operation accordingly.
jQuery("#insert").click(function(e){
e.preventDefault();
var firstname =	jQuery("#firstname").val();
var lastname =	jQuery("#lastname").val();
var user_email =	jQuery("#user_email").val();
var user_pwd =	jQuery("#user_pwd").val();
//alert(firstname + lastname);

var data = {
        'action': 'my_Insertaction',//function name which is created by us in function.php
        "firstname":firstname,
        "lastname":lastname, 
        "user_email":user_email,
        "user_pwd":user_pwd
    };
    jQuery.ajax({
    	 		url: ajax_url, // this is the object instantiated in wp_localize_script function
			    type: 'POST',
			    data:data,
			    beforeSend: function() {
        // setting a timeout
        jQuery("#resp").html('<p>Processing..............</p>');
    },
			    success: function( res ){
			    	if(res){
			      		jQuery("#resp").html("User Inserted Successfully!!!");
			      		jQuery("#responseData").html(res);
			      		
			      }
			      if(res == 0){
			      		jQuery("#resp").html("Email is already Registered!!!");
			      		
			      }
			      
			      if(res == 4){
			      		jQuery("#resp").html("All Fields are required!!!");
			      }
			      
			    }


    });

});

//End of insert script.

}); 



//Delete User Function Starts here
  var ajax_url = plugin_ajax_object.ajax_url;
function DeleUser($user_id){

	var confirmDel = confirm("Do you want to delete this ?");
	if(confirmDel == true){
		var user_id = $user_id;
		//alert(user_id);
		var data = {
			'action':'my_DeleteUsers',
			'user_id':user_id
		};
		jQuery.ajax({
					url: ajax_url, // this is the object instantiated in wp_localize_script function
					type: 'POST',
					data:data,
					beforeSend: function() {
        // setting a timeout
        jQuery("#id"+user_id).html('<p>Processing..............</p>');
    },
					    success: function( resp ){
					      if(resp == 0){

					      		jQuery("#resp").html("Failed to delete data");
					      }
					      if(resp == 1){
					      		jQuery("#id"+user_id).hide();
					      		jQuery("#resp").html("User Deleted Successfully!!!");
					      }
					      
					    }
					});
	}else{
		return false;
	}
}
//Delete user function Ends here


//Function for getiing data for updating user starts here
function UpdateUser($user_id){
	var ajax_url = plugin_ajax_object.ajax_url;
	var user_id = $user_id;
		//alert(user_id);
		var data = {
			'action':'my_UpdateUsersDataForm',
			'user_id':user_id
		};
		jQuery.ajax({
					url: ajax_url, // this is the object instantiated in wp_localize_script function
					type: 'POST',
					data:data,
					 beforeSend: function() {
        // setting a timeout
        jQuery("#UpdateFormDiv").html('<p>Loading..............</p>');
    },
					    success: function( resp ){
					      jQuery("#UpdateFormDiv").html(resp);
					      
					    }
					});
	
}

//Function for getiing data for updating user ends here


jQuery(document).on('click','.update',function(e){
	e.preventDefault();
	let ajax_url = plugin_ajax_object.ajax_url;
	let firstname =	jQuery("#update_firstname").val();
    let lastname =	jQuery("#update_lastname").val();
   let user_email =	jQuery("#update_user_email").val();
   let user_id =	jQuery("#update_id").val();

   let data = {
   			'action':'my_UserUpdateProcessData',
   			 "firstname":firstname,
        	"lastname":lastname, 
        	"user_email":user_email,
			'user_id':user_id

   };
   jQuery.ajax({
					url: ajax_url, // this is the object instantiated in wp_localize_script function
					type: 'POST',
					data:data,
					    success: function( resp ){
					      if(resp == 0){
					      		jQuery("#resp").html("Failed to Update data");
					      }
					      if(resp){
					      		jQuery("#resp").html("User Updated Successfully!!!");
					      		jQuery("#responseData").html(resp);
					      		jQuery("#UpdateFormDiv").html("");
					      }
					      
					    }
					});
});


