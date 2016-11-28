A SIMPLE MYSQL PDO CLASS


// EXAMPLE CONNECTION


 $db = new DB('host', 'username', 'password', 'database');

// EXAMPLE QUERY


$query = "SELECT * FROM comments WHERE comment_id = :comment_id";

$db->query($query)
   ->bind(':comment_id', 1)
   ->execute();

while($row = $db->row()) {
    print_r($row);
}

// INSERT UPDATE


$data = array('column1', 'column2');
//
$db->table('table_name')->insertupdate($data);

while($row = $db->row()) {
   print_r($row);
}
