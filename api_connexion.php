<?php 
    ini_set("display_errors", 0);

    // Get Credentials
    $password = $_POST['password'];
    $email = $_POST['email'];

     // Create connection
     $conn = new mysqli("localhost", "root", "root", "profile", 3300);
    
     //echo $conn->host_info . "\n";
     // Check connection
     if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
     } 
     $sql = "SELECT * FROM user WHERE email = '$email'";
     $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if(password_verify($password, $row['password'])){
                $response = array("status" => "CONNECTION_SUCCESS","user_id"=> $row['id'],"user_email"=> $row['email'],"user_name"=> $row['prenom']." ".$row['nom'], "message" => "User connected succesfully.");
                echo json_encode($response);
            }else{
                $response = array("status" => "ERROR_PASSWORD_VERIFICATION", "message" => "Wrong password, please verify your password.");
                echo json_encode($response);
            }
        }
    }else{
        $response = array("status" => "USER_NOT_FOUND", "message" => "User is not found, please verify your email.");
        echo json_encode($response);
    }
?>