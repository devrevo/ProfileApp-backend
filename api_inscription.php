<?php 
    
    ini_set("display_errors", 0);

    // Profile Informations
    $nom = isset($_POST['nom']) ? $_POST['nom'] : "" ;
    $prenom = isset($_POST['prenom']) ? $_POST['prenom'] : "" ;
    $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : "" ;
    $email = isset($_POST['email']) ? $_POST['email'] : "" ;
    $civilite = isset($_POST['civilite']) ? $_POST['civilite'] : "" ;
    $ville = isset($_POST['ville']) ? $_POST['ville'] : "" ;
    $adresse = isset($_POST['adresse']) ? $_POST['adresse'] : "" ;

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
        $response = array(
                        "status" => "ERROR_CREATING_USER", 
                        "message" => "Email already exist"
                    );
        echo json_encode($response);
    }else{
        $sql = "INSERT INTO user (nom, prenom, email, password, civilite, ville, adresse)VALUES ('$nom', '$prenom', '$email', '$password', '$civilite', '$ville', '$adresse')";
    
        
        if ($conn->query($sql) === TRUE) {
    
            $last_id = $conn->insert_id;
            if(isset($_FILES["photo"])) {
                $check = getimagesize($_FILES["photo"]["tmp_name"]);
                if($check !== false) {
                    $image_name = $last_id.'.jpg';
                    $target_dir = "photos/";
                    $target_file = $target_dir . $image_name;
                    if(!move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                        $image_name = null;
                    }
                } else {
                    $image_name = null;
                }
            }else{
                $image_name = null;
            }
            if($image_name == null){
                $response = array(
                                "user_id"=> $last_id,
                                "status" => "REGISTRATION_SUCCESS", 
                                "email" => $email,"name" => $prenom." ".$nom,
                                "message" => "Invalid image"
                            );
                echo json_encode($response);
            }else{
                $sql = "UPDATE USER SET photo = '$image_name' WHERE id = $last_id";
        
                if ($conn->query($sql) === TRUE) {
                    $response = array(
                                    "user_id"=> $last_id,
                                    "status" => "REGISTRATION_SUCCESS", 
                                    "email" => $email,
                                    "name" => $prenom." ".$nom
                                );
                    echo json_encode($response);
                }else{
                    $response = array(
                                    "user_id"=> $last_id,
                                    "status" => "REGISTRATION_SUCCESS", 
                                    "email" => $email,
                                    "name" => $prenom." ".$nom, 
                                    "message" => "Error Updatinng Image"
                                );
                    echo json_encode($response);
                }
            }
        } else {
            $response = array(
                            "status" => "ERROR_CREATING_USER", 
                            "message" => "Error creating account, please retry later."
                        );
            echo json_encode($response);
        }
    }



?>