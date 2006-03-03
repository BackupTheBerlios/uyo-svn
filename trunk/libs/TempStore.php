<?php
  /**
   * Data storage handler pseudo-abstract class
   * @author Vegar Berg Guldal <mail@funky-m.com>
   */
  class TempStore {
  
    /**
     * TempStore constructor
     */
    function TempStore(){
      if (get_class($this)=='TempStore' || !is_subclass_of($this,'TempStore')) {
        trigger_error('This class is abstract. It cannot be instantiated!', E_USER_ERROR); // use PEAR::ErrorStack here instead?
      }
    }
    
    /**
     * Write string data to storage medium
     */
    function write() {}
    
    /**
     * Read string data from storage medium
     */
    function read() {}
    
    /**
     * Close and reset connections to storage medium
     */
    function destroy() {}
  }
  
  /**
   * Flat file storage handler class
   */
  class FlatFileStore extends TempStore {
    var $handle;
    var $filename;
    
    /**
     * FlatFile constructor
     */
    function FlatFileStore($filename) {
      // Open a file connection for 'r+'
      $this->handle = fopen($filename, 'r+');
      $this->filename = $filename;
      // Set a shared lock
      flock($this->handle, LOCK_SH);
    }
    
    /**
     * Write string data to storage medium
     */
    function write($data) {
      // Ignore user aborts, so the file won't be hosed
      ignore_user_abort(true);
      // Set an exclusive lock
      flock($this->handle, LOCK_EX);
      // Reset file pointer
      rewind($this->handle);
      // Truncate file to 0 bytes
      ftruncate($this->handle, 0);
      // Write data
      fwrite($this->handle, $data);
      // Set a shared lock
      flock($this->handle, LOCK_SH);
      // Allow user aborts again
      ignore_user_abort(false);
    }
    
    /**
     * Read string data from storage medium
     */
    function read() {
      // Reset file pointer
      rewind($this->handle);
      // Read the whole file length
      $data = fread($this->handle, filesize($this->filename));
      return $data;
    }
    
    /**
     * Close and reset connections to storage medium
     */
    function destroy() {
      // Unlock file lock
      flock($this->handle, LOCK_UN);
      // Close file connection
      fclose($this->handle);
    }
  }
  
  /**
   * Database storage handler class
   */
  class DBXStore extends TempStore {
    var $handle;
  
    /**
     * DBXStore constructor
     */
    function DBXStore($server, $user, $password, $database) {
      // Open a database connection
      if (!is_object($this->handle = dbx_connect(DBX_MYSQL, $server, $database, $user, $password))) {
        // An error has occurred
        die("Could not open connection to MySQL database!\n");
      }
    }
    
    /**
     * Write string data to database
     */
    function write($table, $field, $id, $data) {
      // Update data field in a table
      if (!isset($id) || empty($id)) {
        die("Variable ID is not set! Cannot perform WRITE query!\n");
      } else {
        $query = 'UPDATE `'.dbx_escape_string($this->handle, $table).'` SET `'.dbx_escape_string($this->handle, $field).'`=\''.dbx_escape_string($this->handle, $data).'\' WHERE `id`=\''.dbx_escape_string($this->handle, $id).'\' LIMIT 1;';
    //  echo "[QUERY]: $query\n";
        $result = dbx_query($this->handle, $query);
        if (PEAR::isError($result)) {
          die("Error performing WRITE query.<br />\nError returned: ".dbx_error($this->handle)."\n");
        }
      }
    }
    
    /**
     * Read string data from database
     */
    function read($table, $fields, $where='') {
      if (count($fields) > 0) {
      		// Query table for data
      		if (isset($where)) {
      			// Fetch some
      			$query = 'SELECT `'.dbx_escape_string($this->handle, implode('`,`', $fields)).'` FROM `'.dbx_escape_string($this->handle, $table).'` WHERE '.dbx_escape_string($this->handle, $where).';';
      		} else {
        		// Fetch all
        		$query = 'SELECT `'.dbx_escape_string($this->handle, implode('`,`', $fields)).'` FROM `'.dbx_escape_string($this->handle, $table).'`;';
      		}
    //  echo "[QUERY]: $query\n";
        $result = dbx_query($this->handle, $query);
        if (!is_object($result)) {
          die("Error performing READ query.<br />\nError returned: ".dbx_error($this->handle)."\n");
        }
      } else {
        die("Please select at least one field to read!");
      }
      return $result;
    }
    
    /**
     * Close and reset connections to database
     */
    function destroy() {
      // Close database connection
      dbx_close($this->handle);
    }
  }
?>