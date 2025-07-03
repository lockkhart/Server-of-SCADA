<?php
$host = '192.168.28.3';
$port = 1023;
$db = 'PLC_1211C';
$username = 'Scada_Server';
$password = '000000';
$charset = 'utf8mb4';

class Start_server {
    private $host;
    private $port;
    private $socket;
    private $Data;

    public function __construct($host, $port) {
         $this->host = $host;
         $this->port = $port;
        }
   public function run () {
    $address = "tcp://{$this->host}:{$this->port}";
    $this->socket=stream_socket_server($address);
    while ($client = stream_socket_accept($this->socket)) {
    $this->Data = fread ($client, 1024);
}
   }
   public function setData ($Data) {
    $this->Data = $Data;
   }
   public function getData () {
    return $this->Data;
   }
}
 class Digital_input { 
    private $input_1;    
    public function __construct(Start_server $Data) {
        $data = $Data->getData();
        $data = ord($data[0]);
    if ($data == 1) {
         $this -> input_1 = 1;
    } else {
        $this -> input_1 = 0;
    }
 }
 public function setData ($input_1) {
    $this -> input_1 = $input_1;
 }
 public function getData () {
    return $this -> input_1;
 }
}
class PLC_1211C {
    private $host;
    private $username;
    private $password;
    private $db;
    private $conn;
    private $Stop;
    public function __construct ($host, $username, $password, $db) {
        $this ->host = $host;
        $this -> username = $username;
        $this -> password = $password;
        $this -> db = $db;
                $this -> conn = mysqli_connect ($this ->host, $this -> username, $this -> password, $this -> db);

    } 
    public function insertIntoDigital_Input ($input_1) {
        $stop = $input_1 ->getData ();
        $table = 'Digital_Input';
        $columns = ['Input', 'Status'];
        $cols = implode (',', $columns);
        $values = ['Stop button', $stop];
        $sql = "INSERT INTO $table ($cols) VALUES (?, ?)";
        $stmt = $this -> conn -> prepare($sql);
        $input_name = "Stop button";
        $status = $stop;
        $stmt -> bind_param('ss', $input_name, $status);
        $stmt -> execute();
    }
}
?>
