<?php
/*
 * This file is part of UyO.no.
 * 
 * UyO.no is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * UyO.no is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with UyO.no; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// General
$GLOBALS['PROJECT_BASEDIR'] = '/Users/vbg/workbench/uyo.no';	// base directory for project installation. (no trailing slash!)

// Data storage
$GLOBALS['STORAGE'] = 'dbx';	// 'dbx' for database connection, 'db4' for Berkley DB4 database file, 'ff' for flat file storage
// TODO: STORAGE and DBX_TYPE don't matter at all yet =)
//  - Database options
$GLOBALS['DBX_TYPE'] = DBX_MYSQL;	// for DBX only. see http://php.net/manual/en/ref.dbx.php for other values
$GLOBALS['DB_USERNAME'] = 'root';	// username for database connection
$GLOBALS['DB_PASSWORD'] = '';	// password for database connection
$GLOBALS['DB_DATABASE'] = 'uyo_development';	// database to connect to
$GLOBALS['DB_HOST'] = 'localhost';	// database server to connect to
//  - Flat file options
// nothing here yet
?>
