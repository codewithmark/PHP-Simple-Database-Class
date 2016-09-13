<?php

class SimpleDBClass
{
  public $isConn; 
  
  //To show query error messages set on or to hide then set to off
  //For trouble shooting only
  public $ShowQryErrors = 'on'; //on or off


  //--->Connect to database - Start
  public function __construct($host="localhost", $user="root", $pass="",$database="")
  {
    
    // Create connection
    $connection = mysqli_connect($host, $user, $pass,$database);

    // Check connection
    if (!$connection) 
    {
      //echo ("conasfasdfas");
      die("Connection failed: " . mysqli_connect_error());
      return false;
    }
    if($connection)
    {
      //echo ("conneted");
      $this->isConn = $connection;
    }
  }
  //--->Connect to database - End


  /*
    Different 5 types of queries
    - Select: all rows
    - Insert: add row(s)
    - Update: update field(s)
    - Delete: delete row(s)
    - Qry: general purpose query
  */

  //--->Select - Start
  function Select($SQLStatement)
  {
    /*
    * This will get all of the rows from the table.  
    * Call it like - $Qry = Select( "SELECT * FROM Users WHERE site='codewithmark'")  
    */
    
    // Create connection
    $con =  $this->isConn;

    // Check connection
    if (!$con) 
    {
      die("Connection failed in Select function - " . mysqli_connect_error());
    }

    // Connection is made
    if ($con) 
    {
      //$SQLStatement = "SELECT * FROM UserProfile WHERE user_id='markkumar'"; 

      $q = $con->query($SQLStatement);

      //Fail to run query.
      if(!$q)
      {
        //show error message
        if($this->ShowQryErrors == 'on')
        {
          die( mysqli_error($con) );  
        }        
      }

      $row = $q->num_rows;

      //no rows found
      if($row <1)
      {
        $result = $row;  
      }
      //only one row of data
      else if($row == 1)
      {
        $result = array($q->fetch_assoc());
      }
      //multiple rows
      else if( $row >1)
      {
        $d1 = array( $q->fetch_assoc());
        
        $d2= array();
        while ($row = $q->fetch_assoc()) 
        {
          $d2[] = $row;
        }
        //merger array to get all rows
        $result = array_merge($d1 , $d2); 
      }
      //Will return a row data
      return $result;
      }
  }
  //--->Select - End


  //--->Insert - Start
  function Insert($TableName,$row_arrays = array() ) 
  { 
    /*
      $insert_arrays[] = array
      (
        'user_id' => "codemaster",
        'email_id' => 'mk@codewithmark.com',
        'user_name'=> 'codewithmark'
      );
      
      Call it like this:
      Insert('table',$insert_arrays);

      If ran successfully, it will return the insert id else 0

    */

    // Setup arrays for Actual Values, and Placeholders
    $values = array();
    $place_holders = array();
    $query = "";
    $query_columns = "";
    
    $query .= "INSERT INTO {$TableName} (";
    
      foreach($row_arrays as $count => $row_array)
      {

          foreach($row_array as $key => $value) 
          {

              if($count == 0) 
              {
                  if($query_columns) 
                  {
                    $query_columns .= ",".$key."";                          
                  } 
                  else 
                  {
                    $query_columns .= "".$key."";
                  }
              }

              $values[] =  $value;
        
              if(is_numeric($value)) 
              {
                  if(isset($place_holders[$count])) 
                  {
                    $place_holders[$count] .= ", '$value'";
                  } 
                  else 
                  {
                    $place_holders[$count] = "( '$value'";
                  }
              } 
              else 
              {
                  if(isset($place_holders[$count])) 
                  {
                    $place_holders[$count] .= ", '$value'";
                  } 
                  else 
                  {
                    $place_holders[$count] = "( '$value'";
                  }
              }
              
          }
              // mind closing the GAP
              $place_holders[$count] .= ")";
      }
    
    $query .= " $query_columns ) VALUES ";
    
    $query .= implode(', ', $place_holders);

    $sql = $query; 

    $con =  $this->isConn;

    // Check connection
    if (!$con) 
    {
      die("Connection failed in query function - " . mysqli_connect_error());
    }

    if($con)
    {
      $q = $con->query($sql);
      if(!$q)
      {  
        //show error message
        if($this->ShowQryErrors == 'on')
        {
          die( mysqli_error($con) );  
        }  
        $result =  0;
      }
      if($q)
      {
        //Will give the last inserted id
        $result =  $con->insert_id;      
      }
      
      //Will return a row data
      return $result; 
    }
  }
  //--->Insert - End

  //--->Update - Start
  function Update($strTableName, $array_fields, $array_where)
  { 
    /*
      This will update the row values
      If it ran successfully, it will return 1 else 0
     
      $strTableName = "TableName";

      //It would be in your best interested to run your values through CleanDBData($Data) function 
      //to prevent any sql injections which potentially cause problems in your database.

      $array_fields = array(
        'FieldName1' => CleanDBData(FieldValue1),
        'FieldName2' => CleanDBData(FieldValue2),
        'FieldName3' => CleanDBData(FieldValue3),
      );

      $array_where = array(    
        'rec_id' => 2,
        'rec_dt' => date("Y-m-d"),    
      );
      Call it like this:  Update($strTableName, $array_fields, $array_where)
    * 
    */

    //Get the update fields and value
    foreach($array_fields as $key=>$value) 
    {
      if($key) 
      {
        $field_update[] = " $key='$value'";
      }
    }
    $fields_update = implode( ',', $field_update );

    //Get where fields and value
    foreach($array_where as $key=>$value) 
    {
      if($key) 
      {
        $field_where[] = " $key='$value'";
      }
    }
    $fields_where = implode( ' and ', $field_where );

    $SQLStatement = "UPDATE $strTableName  SET $fields_update WHERE $fields_where ";

    $con =  $this->isConn;

    // Check connection
    if (!$con) 
    {
      die("Connection failed in query function - " . mysqli_connect_error());
    }

    if($con)
    {
      $q = $con->query($SQLStatement);
      if(!$q)
      { 
        //show error message
        if($this->ShowQryErrors == 'on')
        {
          die( mysqli_error($con) );  
        } 

        $result =  0;
      }
      if($q)
      {  
        $result = 1;
      }
      
      //Will return a row data
      return $result; 
    }
  }
  //--->Update - End

  //--->Delete - Start
  function Delete($strTableName,$strFieldName,$strFieldDeleteValueEqualTo)
  {
    /*
      This will delete all rows where field name equals delete value. 
      If it ran successfully, it will return 1 else 0

      Call it like this:  Delete("users","user_id","codewithmark");

    */
    
    // Create connection
    $con =  $this->isConn;
    
    // Check connection
    if (!$con) 
    {
      die("Connection failed in query function - " . mysqli_connect_error());
    }
    //check to see if the record exist
    $QFindRec = "SELECT * FROM $strTableName WHERE $strFieldName='$strFieldDeleteValueEqualTo'";
    
    if($con)
    {
      $q = $con->query($QFindRec);

      if($q->num_rows > 0 )
      {
        //found the record(s) and now delete it
        $QDeleteRec = "DELETE FROM $strTableName WHERE $strFieldName='$strFieldDeleteValueEqualTo'";
        $con->query($QDeleteRec);

        $result = 1;
      }
      if($q->num_rows < 1)
      {   
        $result = 0;
      }
      
      //Will return a row data
      return $result;
    }
  }
  //--->Delete - End




  function Qry($SQLStatement)
  {
    /*
     This is for general purpose query. 
     If it ran successfully, it will return 1 else 0.

     Call it like this:  Qry('select * from user where id=100');

    */
    // Create connection
    $con =  $this->isConn;
    
    // Check connection
    if (!$con) 
    {
      die("Connection failed in query function - " . mysqli_connect_error());
    }
    
    if($con)
    {
      $q = $con->query($SQLStatement);
      
      if(!$q)
      {
        //show error message
        if($this->ShowQryErrors == 'on')
        {
          die( mysqli_error($con) );  
        } 
        $result = 0;
      }
      if($q)
      {       
        //$result = true;
        $result = 1;
      }
      
      //Will return a row data
      return $result;
    }
  }


  function CleanDBData($Data)
  {
    /*
      This will help in preventing sql injections
    */
    // Create connection
    $con =  $this->isConn;
    $str = mysqli_real_escape_string($con,$Data); 
    return $str;
  } 

  function CleanHTMLData($Data)
  {
    /*
      This will remove all HTML tags
    */
    
    // Create connection
    $con =  $this->isConn; 
    $str = mysqli_real_escape_string($con,$Data);
    
    $result = preg_replace('/(?:<|&lt;)\/?([a-zA-Z]+) *[^<\/]*?(?:>|&gt;)/', '', $str);
    
    return $result;
  } 
}

?>
