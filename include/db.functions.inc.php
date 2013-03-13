<?php
/*
 * File: db.functions.inc.php
 * Author: Alfredo Ramirez
 * Date: 02/05/2013
 * Description: Database-specific functions for entire site.
 */
 
/*
 * SQL Query function using PDO prepared statement.
 *
 * @param $sql is an initialized string and valid SQL query.
 * @param $params is an initialized array of strings.
 * @returns $query is an executed PDO prepared statement.
 */
function query($sql, $parameters=false) {  
    global $dbh;
    
    $query = $dbh->prepare($sql);
    if ($parameters) {
    
        foreach ($parameters as $name => $value) {
        
            $query->bindValue($name, $value);
            
        }
    }
    
    $query->execute();
    return $query;

}

/*
 * Fetches SQL query results.
 *
 * @param $result is a PDO prepared statement.
 * @param $type is an initialized string.
 * @returns $out is a PDO query result.
 */
function fetch($result, $type = 'assoc') {

    switch ($type) {
        case 'assoc':
            $out = $result->fetch(PDO::FETCH_ASSOC);
            break;
        case 'both':
            $out = $result->fetch(PDO::FETCH_BOTH);
            break;
        case 'obj':
            $out = $result->fetch(PDO::FETCH_OBJ);
            break;
        case 'bound':
            $out = $result->fetch(PDO::FETCH_BOUND);
            break;
        case 'class':
            $out = $result->fetch(PDO::FETCH_CLASS);
            break;
        case 'into':
            $out = $result->fetch(PDO::FETCH_INTO);
            break;
        case 'num':
            $out = $result->fetch(PDO::FETCH_NUM);
            break;
        case 'count':
            $out = $result->rowCount();
            break;
        case 'all':
            $out = $result->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'allBoth':
            $out = $result->fetchAll();
            break;
    }
    
    return $out;
    
}
?>