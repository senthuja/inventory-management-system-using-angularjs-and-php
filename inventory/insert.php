<?php

include('database_connection.php');

$form_data = json_decode(file_get_contents("php://input"));

$error = '';
$message = '';
$validation_error = '';
$inventory_name = '';
$inventory_description = '';
$inventory_amount = '';
$inventory_available = '';

if($form_data->action == 'fetch_single_data')
{
       $query = "SELECT * FROM tbl_sample WHERE id='".$form_data->id."'";
       $statement = $connect->prepare($query);
       $statement->execute();
       $result = $statement->fetchAll();
       foreach($result as $row)
       {
              $output['inventory_name'] = $row['inventory_name'];
              $output['inventory_description'] = $row['inventory_description'];
              $output['inventory_amount'] = $row['inventory_amount'];
              $output['inventory_available'] = $row['inventory_available'];
       }
       
}
elseif($form_data->action == 'Delete')
{
       $query = "
       DELETE FROM tbl_sample WHERE id='".$form_data->id."'
       ";
       $statement = $connect->prepare($query);
       if($statement->execute())
       {
              $output['message'] = 'Data Deleted';
       }
}

else{

       if(empty($form_data->inventory_name)){
              $error[] = 'Inventory Name is Required';
       }
       else{
              $inventory_name = $form_data->inventory_name;
       }
       if(empty($form_data->inventory_description)){
              $error[] = 'Inventory Description is Required';
       }
       else{
              $inventory_description = $form_data->inventory_description;
       }
       if(empty($form_data->inventory_amount)){
              $error[] = 'Inventory Amount is Required';
       }
       else{
              $inventory_amount = $form_data->inventory_amount;
       }
       if(empty($form_data->inventory_available)){
              $error[] = 'Inventory Available is Required';
       }
       else{
              $inventory_available = $form_data->inventory_available;
       }
       if(empty($error)){
              if($form_data->action == 'Insert'){
                     $data = array(
                            ':inventory_name'  => $inventory_name,
                            ':inventory_description'  => $inventory_description,
                            ':inventory_amount'  => $inventory_amount,
                            ':inventory_available'  => $inventory_available,
                     );
                     $query = "
                     INSERT INTO tbl_sample 
                     (inventory_name, inventory_description, inventory_amount, inventory_available ) VALUES 
                     (:inventory_name, :inventory_description, :inventory_amount, :inventory_available)
                     ";
                     $statement = $connect->prepare($query);
                     if($statement->execute($data)){
                            $message = 'Data Inserted';
                     }
              }
              if($form_data->action == 'Edit')
              {
                     $data = array(
                            ':inventory_name'  => $inventory_name,
                            ':inventory_description'  => $inventory_description,
                            ':inventory_amount'  => $inventory_amount,
                            ':inventory_available'  => $inventory_available,
                            ':id'   => $form_data->id
                     );
                     $query = "
                     UPDATE tbl_sample 
                     SET inventory_name = :inventory_name, inventory_description = :inventory_description, inventory_amount = :inventory_amount,  inventory_available = :inventory_available 
                     
                     WHERE id = :id
                     ";
                     $statement = $connect->prepare($query);
                     if($statement->execute($data))
                     {
                            $message = 'Data Edited';
                     }
              
              }
       }
              
       else{
              $validation_error = implode(", ", $error);
       }
       $output = array(
              'error'  => $validation_error,
              'message' => $message 
       );
}

            
echo json_encode($output);
            

?>
