<?php 
   /**
   Plugin Name: CRUD AJAX
   Author:Vaibhav Gangrade
   Version:1.0
   Stable Version:1.0
   Description:CRUD with AJAX functionality.
   Author URI:
   Author URL:
   */
   
   //Preventing direct access to plugin files
   if (!defined('ABSPATH')) exit;
   
   
   #############     CREATING TABLE ON PLUGIN ACTIVATION    ############################
      
      global $wpdb;
      global $table_name;
      $table_name = 'ajax_crud_table';
      $charset_collate = $wpdb->get_charset_collate();
      $create_user_table = "CREATE TABLE IF NOT EXISTS $table_name(
        		id mediumint(11) NOT NULL AUTO_INCREMENT,
        		firstname varchar(100) NOT NULL,
        		lastname varchar(100) NOT NULL,
        		user_email varchar(100) NOT NULL,
        		user_pwd varchar(100) NOT NULL,
       		PRIMARY KEY  (id)
      ) $charset_collate;";
      require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($create_user_table);
      
      ############      END OF CREATING TABLE CODE ###########################################
   
   
   
   ################### Main Shortcode Function Starts Here ###################################
   
   if (!function_exists('CRUD_Ajax')){

      $loaderimg =  plugins_url( '/loader.gif', __FILE__ );
   	
   	function CRUD_Ajax(){ ?>
      <h2>CRUD in AJAX</h2>
      <form action=""  id="register_form">
         <label>First Name:</label>
         <input type="text" name="firstname" id="firstname" class="form-control" placeholder="Enter Firstname"><br><br>
         <label>last Name:</label>
         <input type="text" name="lastname" id="lastname" class="form-control" placeholder="Enter Lastname"><br><br>
         <label>Username:</label>
         <input type="email" name="user_email" id="user_email" class="form-control" placeholder="Enter Username"><br><br>
         <label>Password:</label>
         <input type="password" name="user_pwd" id="user_pwd" class="form-control" placeholder="Enter Password"><br><br>
         <input type="submit" name="insert" id="insert" class="btn btn-primary" value="Insert">
      </form>
      <div id="resp"></div>
         <table>
            <thead>
               <tr>
                  <th>Sno<?php $sno ='1'; ?></th>
                  <th>First Name</th>
                  <th >Last Name</th>
                  <th style="width: 300px;">Email</th>
                  <th colspan="2" style="width: 300px;">Action</th>
               </tr>
            </thead>
            <tbody id="responseData">
               <?php
                  global $wpdb;
                  $all_user_data = "SELECT * FROM ajax_crud_table";
                  $data = $wpdb->get_results($all_user_data);
                  if(!empty($data)){
                  foreach($data as $user_data){?>
               <tr id="id<?php echo $user_data->id;  ?>">
                  <td><?php echo $sno++; ?></td>
                  <td><?php echo $user_data->firstname; ?></td>
                  <td><?php echo $user_data->lastname; ?></td>
                  <td><?php echo $user_data->user_email; ?></td>
                  <td><button class="btn btn-primary" onclick="return UpdateUser(<?php echo $user_data->id;?>);">Edit</button></td>
                  <td >
                     <button type="button" name="delete_user" onclick="return DeleUser(<?php echo $user_data->id;  ?>);">Delete </button>
                  </td>
               </tr>
               <?php } }else{ ?> 
               <tr>
                  <td colspan="6" style="text-align: center;">No Data Found</td>
               </tr>
               <?php }?>
            </tbody>
         </table>
         <div id="UpdateFormDiv"></div>
<?php }
   /* Include CSS and Script */
   add_action('wp_enqueue_scripts','plugin_css_jsscripts');
   function plugin_css_jsscripts() {
      // CSS
      wp_enqueue_style( 'style-css', plugins_url( '/style.css', __FILE__ ));
   
      // JavaScript
      wp_enqueue_script( 'script-js', plugins_url( '/script.js', __FILE__ ),array('jquery'));
   
      // Pass ajax_url to script.js
      wp_localize_script( 'script-js', 'plugin_ajax_object',
      array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
   }
   
   //Adding ajax for inserting users
   add_action( 'wp_ajax_my_Insertaction', 'my_Insertaction' );
   
   function my_Insertaction() {
   global $wpdb; // this is how you get access to the database
   
   $firstname =  $_POST['firstname'];
   $lastname =  $_POST['lastname'];
   $user_email =  $_POST['user_email'];
   $user_pwd =  $_POST['user_pwd'];
   
   //echo "daat is". $firstname." ".$lastname;
   
   if (!empty($firstname) && !empty($lastname) && !empty($user_email) && !empty($user_pwd))
      {
          global $wpdb;
          $sql = "SELECT firstname FROM `ajax_crud_table` WHERE user_email = '".$user_email."'";
          $exist_user_data = $wpdb->get_results($sql);
          if ($exist_user_data)
          {
          	echo 0;//Email already exist
              
          }
          else
          {
              global $wpdb;
              $insert_records = $wpdb->insert('ajax_crud_table', array(
                  'firstname' => $firstname,
                  'lastname' => $lastname,
                  'user_email' => $user_email,
                  'user_pwd' => $user_pwd 
              ));
              if (is_wp_error($insert_records))
              {
   
                  echo 3;//Failed to insert 
              }
              else{ //echo 1;//Inserted Successfully
                  global $wpdb;
                  $all_user_data = "SELECT * FROM ajax_crud_table";
                  $data = $wpdb->get_results($all_user_data);
                  if(!empty($data)){
                   $sno ='1'; 
                   foreach($data as $user_data){?>
                     <tr id="id<?php echo $user_data->id;  ?>">
                        <td><?php echo $sno++; ?></td>
                        <td><?php echo $user_data->firstname; ?></td>
                        <td><?php echo $user_data->lastname; ?></td>
                        <td><?php echo $user_data->user_email; ?></td>
                        <td><button class="btn btn-primary" onclick="return UpdateUser(<?php echo $user_data->id;?>);">Edit</button></td>
                        <td >
                           <button type="button" name="delete_user" onclick="return DeleUser(<?php echo $user_data->id;  ?>);">Delete </button>
                        </td>
                     </tr>
               <?php } }else{ ?> 
      <tr>
         <td colspan="6" style="text-align: center;">No Data Found</td>
      </tr>
      <?php }?>
      <?php } } }else{
      	echo 4;//All fields are required
      }
   
   wp_die(); // this is required to terminate immediately and return a proper response
   }
   
   //End of insert ajax function here
   
   
   add_action('wp_ajax_my_DeleteUsers','my_DeleteUsers');
   function my_DeleteUsers(){
   global $wpdb; // this is how you get access to the database
   
   $user_id =  $_POST['user_id'];
   //echo "user id is ".$user_id;
   $table = 'ajax_crud_table';
   $delete_user_data = $wpdb->delete( $table, array( 'id' => $user_id ) );
   if(is_wp_error($delete_user_data)){
   	echo 0;
   }
   else{ 
   	echo 1; 
   }
   wp_die(); // this is required to terminate immediately and return a proper response
   }
   
   
   
   
   add_action('wp_ajax_my_UpdateUsersDataForm','my_UpdateUsersDataForm');
   function my_UpdateUsersDataForm(){ 
   	global $wpdb; // this is how you get access to the database
   
   		 $user_id =  $_POST['user_id'];
   
           $Update_user_data = "SELECT * FROM ajax_crud_table WHERE id = '".$user_id."'";
           $Update_data = $wpdb->get_results($Update_user_data);
   
           //print_r($Update_data);
   ?>
<form id="update_form" >
   <label>First Name:</label>
   <input type="text" name="firstname" id="update_firstname" class="form-control"  placeholder="Enter Firstname" value="<?php echo $Update_data[0]->firstname; ?>"><br><br>
   <label>last Name:</label>
   <input type="email" name="lastname" id="update_lastname" class="form-control" placeholder="Enter Lastname" value="<?php echo $Update_data[0]->lastname; ?>"><br><br>
   <label>Username:</label>
   <input type="email" name="user_email" id="update_user_email" class="form-control" placeholder="Enter Username" value="<?php echo $Update_data[0]->user_email; ?>"><br><br>
   <input type="hidden" name="update_id" id="update_id" value="<?php echo $Update_data[0]->id;?>">
   <input type="button" name="update" id="update" class="btn btn-primary update" value="Update">
</form>
<?php }
   ############################### UPDATE USER PROCESS #################################
   
   add_action('wp_ajax_my_UserUpdateProcessData','my_UserUpdateProcessData');
   function my_UserUpdateProcessData(){
   	global $wpdb; // this is how you get access to the database
   
   	$user_id =  $_POST['user_id'];
   	$firstname =  $_POST['firstname'];
   	$lastname =  $_POST['lastname'];
   	$user_email =  $_POST['user_email'];
   	//die($user_id);
   
   	if(!empty($firstname) && !empty($lastname) && !empty($user_email)){
   
   		$table = 'ajax_crud_table';
   		$update_userdata = $wpdb->update($table,
   		array(
   				'id' => $user_id,
   				'firstname'=>$firstname,
   				'lastname'=>$lastname,
   				'user_email'=>$user_email
   	),array('id'=>$user_id));
   
   	if(is_wp_error($update_userdata)){
   		echo 0;
   
   	}
   	else{
   		echo 1;
          global $wpdb;
                  $all_user_data = "SELECT * FROM ajax_crud_table";
                  $data = $wpdb->get_results($all_user_data);
                  if(!empty($data)){
                   $sno ='1'; 
                   foreach($data as $user_data){?>
                     <tr id="id<?php echo $user_data->id;  ?>">
                        <td><?php echo $sno++; ?></td>
                        <td><?php echo $user_data->firstname; ?></td>
                        <td><?php echo $user_data->lastname; ?></td>
                        <td><?php echo $user_data->user_email; ?></td>
                        <td><button class="btn btn-primary" onclick="return UpdateUser(<?php echo $user_data->id;?>);">Edit</button></td>
                        <td >
                           <button type="button" name="delete_user" onclick="return DeleUser(<?php echo $user_data->id;  ?>);">Delete </button>
                        </td>
                     </tr>
               <?php } }else{ ?> 
      <tr>
         <td colspan="6" style="text-align: center;">No Data Found</td>
      </tr>
      <?php }?>
      <?php } }
   
   wp_die(); // this is required to terminate immediately and return a proper response
   }

   
   
   
   ############################### UPDATE USER PROCESS #################################
   
   add_shortcode('AJAX_CRUD','CRUD_Ajax');
   }
   ################### Main Shortcode Function Starts Here ###################################
   
   ?>