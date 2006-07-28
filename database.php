<?
/**
 * Database.php
 *
 */
include("constants.php");
      
class MySQLDB
{
   var $connection;         //The MySQL database connection

   /* Class constructor */
   function MySQLDB(){
      /* Make connection to database */
      $this->connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die(mysql_error());
      mysql_select_db(DB_NAME, $this->connection) or die(mysql_error());
   }
   
   /**
    * confirmUserID - Checks whether or not the given
    * name is in the database, if so it checks if the
    * given userid is the same userid in the database
    * for that user. If the user doesn't exist or if the
    * userids don't match up, it returns an error code
    * (1 or 2). On success it returns 0.
    */
   function confirmUserID($query, $name, $userid){
      /* Add slashes if necessary (for query) */
      if(!get_magic_quotes_gpc()) {
	      $name = addslashes($name);
      }

      /* Verify that user is in database */
      $result = mysql_query($query, $this->connection);
      if(!$result || (mysql_numrows($result) < 1)){
         return 1; //Indicates username failure
      }

      /* Retrieve userid from result, strip slashes */
      $dbarray = mysql_fetch_array($result);
      $dbarray['userid'] = stripslashes($dbarray['userid']);
      $userid = stripslashes($userid);

      /* Validate that userid is correct */
      if($userid == $dbarray['userid']){
         return 0; //Success! Username and userid confirmed
      }
      else{
         return 2; //Indicates userid invalid
      }
   }
   
   /**
    * getNumRows - Returns true if row is not
    * empty.
    */
   function getNumRows($field,$column,$selector){
      $q = sprintf("SELECT $field FROM ".TBL_FOO." WHERE $column = %s",$this->escape($selector));
      $result = mysql_query($q, $this->connection);
      return (mysql_numrows($result) > 0);
   }
   
     /**
    * getResultArray - Returns the result array from a mysql
    * query asking for all information stored regarding
    * the given query. If query fails, NULL is returned.
    */
   function getResultArray($query){
      $result = mysql_query($query, $this->connection);
      /* Error occurred, return given name by default */
      if(!$result || (mysql_numrows($result) < 1)){
         return NULL;
      }
      /* Return result array */
      $dbarray = mysql_fetch_array($result);
      return $dbarray;
   }
   
   
   /**
    * query - Performs the given query on the database and
    * returns the result, which may be false, true or a
    * resource identifier.
    */
   function query($query){
      return mysql_query($query, $this->connection);
   }
   
   /**
    * escape - escapes strings when entered into the database.
    * Helps prevent SQL injection. 
    *
    */
   function escape($value)
{
   // Stripslashes
   if (get_magic_quotes_gpc()) {
       $value = stripslashes($value);
   }
   // Quote if not a number or a numeric string
   if (!is_numeric($value)) {
       $value = "'" . mysql_real_escape_string($value) . "'";
   }
   return $value;
}
};

/* Create database connection */
$database = new MySQLDB;

?>