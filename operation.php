<?php 
    require_once('config.php');
    $data =json_decode(file_get_contents('php://input'));
    switch($data->option){
        case 'getdbs':
            $conn = new mysqli($servername, $username, $password, $dbname);
            // Check connection
            if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
            }
            $arr = array();
            $sql = "SELECT * FROM SCHEMATA";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    array_push($arr,$row["SCHEMA_NAME"]);
                }
            }

            echo json_encode(array('dblist'=>$arr));
            break;
        
            case 'generatemodel':
                $conn = new mysqli($servername, $username, $password, $dbname);
                // Check connection
                if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
                }
                $json = array('appname'=>$data->dbname,'db'=>$data->dbname);
                $json['tables'] = array();
                
                $sql = "SELECT * FROM tables where TABLE_SCHEMA='".$data->dbname."' and table_type='BASE TABLE'";
                // echo $data->dbname; exit;
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // output data of each row
                    while($row = $result->fetch_assoc()) {
                        $data1 = array();
                    $data1["tablename"] = $row["TABLE_NAME"];
                    $data1["cols"] = array();
                    $sql1 = "SELECT * FROM columns where TABLE_SCHEMA='".$data->dbname."' and TABLE_NAME='".$row["TABLE_NAME"]."'";
                    $result1 = $conn->query($sql1);
                    
                    if ($result1->num_rows > 0) {
                    // output data of each row
                    while($row1 = $result1->fetch_assoc()) {
                        $obj = array('columnname'=>$row1["COLUMN_NAME"],'datatype'=>$row1['DATA_TYPE'],'label'=>$row1['COLUMN_NAME']);
                        if($row1["COLUMN_KEY"] == "PRI"){
                            $obj["key"] = true;
                            $obj["hide"] = true;
                        }else{
                            $obj["key"] = false;
                            $obj["hide"] = false;
                        }
                        
                        array_push($data1["cols"],$obj);
                    }
                    }
                    array_push($json['tables'],$data1);
                }
                }
                $jsondata = json_encode($json);
                if(file_exists("modal.json")){
                $str = "renamed-".Date('Y-m-d-H-i-s');
                    rename("modal.json","modal-".$str.".json");
                }

                $myfile = fopen("modal.json", "w") or die("Unable to open file!");
                fwrite($myfile, $jsondata);
                fclose($myfile);
                $conn->close();

                echo json_encode(array('jsondata'=>$jsondata));
            break;
        
            default:

            break;
    }
?>