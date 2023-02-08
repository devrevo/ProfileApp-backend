<?php 
    ini_set("display_errors", 1);

    $conn = new mysqli("localhost", "root", "root", "profile", 3300);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    if(isset($_POST['scope']) && $_POST['scope'] == 'getUser'){
        $id = $_POST['user_id'];

        $sql = "SELECT adresse, ville, civilite FROM user WHERE id = '$id'";

        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $response = array("status" => "DATA_FOUND","adresse"=> $row['adresse'],"ville"=> $row['ville'],"civilite"=> $row['civilite']);
                echo json_encode($response);
            }
        }else{
            $response = array("status" => "DATA_NOT_FOUND","message" => "Error getting user informations");
            echo json_encode($response);
        }

    }

    if(isset($_POST['scope']) && $_POST['scope'] == 'updateUser'){

        if(isset($_FILES["photo"])) {
            $check = getimagesize($_FILES["photo"]["tmp_name"]);
            if($check !== false) {
                $image_name = $_POST['user_id'].'.jpg';
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
        $updates = array();
        if($image_name != null){
            array_push($updates, "photo = '$image_name'");
        }
        if($_POST['password'] != ''){
            array_push($updates, "password = '".password_hash($_POST['password'], PASSWORD_DEFAULT)."'");
        }
        if($_POST['adresse'] != ''){
            array_push($updates, "adresse = '".$_POST['adresse']."'");
        }
        if($_POST['civilite'] != ''){
            array_push($updates, "civilite = '".$_POST['civilite']."'");
        }
        if($_POST['ville'] != ''){
            array_push($updates, "ville = '".$_POST['ville']."'");
        }
        $update_string = join(",", $updates);
        $sql = "UPDATE user SET $update_string WHERE id = ".$_POST['user_id'];

        if ($conn->query($sql) === TRUE) {
            $response = array(
                            "status" => "UPDATE_SUCCESS",
                        );
            echo json_encode($response);
        }else{
            $response = array(
                            "status" => "UPDATE_ERROR", 
                        );
            echo json_encode($response);
        }
    }

?>