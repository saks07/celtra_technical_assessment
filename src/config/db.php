<?php

class Db {
    private $dbHost = 'localhost';
    private $dbUsername = 'root';
    private $dbPassword = '';
    private $dbName = 'notes_db';
    private $limits = [
        'notes'     => 4,
        'folders'   => 4
    ];

    private $dataMessages = [
        'successfully read',
        'No results found',
        'successfully updated',
        'successfully deleted',
        'successfully created',
        'missing data',
        'insert failed',
        'update failed',
        'delete failed',
        'Invalid parameter'
    ];

    public function connectToDatabase() {
        $mySqlString = 'mysql:dbname=' . $this->dbName . ';host=' . $this->dbHost;
        $connection = new PDO($mySqlString, $this->dbUsername, $this->dbPassword);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connection;
    }

    private function returnQueryMessage($name = '', $code = 0) {
        if( $code === 1 ) return $this->dataMessages[$code]; 
        return $name . ' ' . $this->dataMessages[$code];
    }

    public function handleRequestMethod($method = 'GET', $tableName = 'notes', $tableColumns = [], $userId = 0, $args = [], $body = null, $query = []) {
        // HANDLE POST REQUEST
        if( $method === 'POST' ) {
            // THROW EXCEPTION IF REQUEST BODY IS EMPTY
            if( is_null( $body ) )
                return [ 'data' => [], 'message' => $this->returnQueryMessage($tableName, 5) ];

            $sql = "INSERT INTO " . $tableName . " (" . $tableColumns['columns'] . ") VALUES (" . $tableColumns['placeholders'] . ")";
            return $this->create($sql, $body, $tableName);
        }

        // HANDLE PUT REQUEST
        if( $method === 'PUT' ) {
             // THROW EXCEPTION IF NOTE ID IS MISSING OR REQUEST BODY IS EMPTY
             if( empty($args) || !isset( $args['id'] ) || is_null( $body ) )
                return [ 'data' => [], 'message' => $this->returnQueryMessage($tableName, 5) ];
            
            $sql = "UPDATE " . $tableName . " SET " . $tableColumns['setPlaceholders'] . " WHERE id=" . intval( $args['id'] ) . " AND uid=" . $userId;
            return $this->update($sql, $body, $tableName);
        }

        // HANDLE DELETE REQUEST
        if( $method === 'DELETE' ) {
             // THROW EXCEPTION IF NOTE ID IS MISSING
             if( empty($args) || !isset( $args['id'] ) )
                return [ 'data' => [], 'message' => $this->returnQueryMessage($tableName, 5) ];

            $sql = "DELETE FROM " . $tableName . " WHERE id=" . intval( $args['id'] ) . " AND uid=" . $userId;

            return $this->delete($sql, $body, $tableName);
        }

        // DEFAULT GET REQUEST
        $sql = "SELECT " . $tableColumns['selectColumns'] . " FROM " . $tableName . " WHERE uid=" . $userId;
        if( isset( $args['id'] ) ) {
            $sql .= " AND id=" . intval( $args['id'] );
        }

        if( !empty( $query ) ) {
            // FILTER QUERY PARAMETER
            if( isset( $query['filter'] ) ) {
                $sql .= $this->handleFilterQuery($query['filter'], $tableName);
            }

            // SORT QUERY PARAMETER
            if( isset( $query['sort'] ) ) {
                $sortParams = explode('|', $query['sort']);
                $column = $sortParams[0];
                $order = $sortParams[1];
                $sql .= " ORDER BY " . $column . " " . $order;
            }

            // PAGINATION QUERY PARAMETER
            if( isset( $query['p'] ) ) {
                $calculateOffset = ( intval( $query['p'] ) - 1 ) * $this->limits[$tableName];
                $sql .= " LIMIT " . $this->limits[$tableName] . " OFFSET " . $calculateOffset;
            }
        }

        return $this->read($sql, $args, $tableName, $query);
    }

    private function handleFilterQuery($queryFilter = '', $tableName = 'notes') {
        $filterQuery = explode('|', $queryFilter);
        $column =  $filterQuery[0];
        $value =  $filterQuery[1];

        // HANDLE FOLDERS FILTER
        if( $column === 'folders' ) {
            return " INNER JOIN " . $column . " ON " . $tableName . ".fid=" . $column . ".id WHERE " . $tableName . ".fid=" . $value;
        }

        $columnCheckText = $column === 'text';
        // USE LIKE OPERATOR FOR NOTE TEXT SEARCH
        $operator = $columnCheckText ? ' LIKE ' : '=';
        // FORMAT SEARCH VALUE BASED ON OPERATOR
        $formatValue = $columnCheckText ? "'%" . $value . "%'" : "'" . $value . "'";

        return " WHERE " . $column . $operator . $formatValue;
    }

    private function executeQuery($sql, $args, $function = 'read', $tableName, $query = []) {
        $nonFetchFunctions = ['create', 'update', 'delete'];
        $connection = $this->connectToDatabase();
        $stmt = $connection->prepare($sql);
        $stmt->setFetchMode($connection::FETCH_OBJ);
        $executeQuery = $stmt->execute($args);

        // RETURN RESULT FOR POST, PUT, DELETE METHODS
        if( in_array($function, $nonFetchFunctions) ) {
            return $executeQuery;
        }

        // GET TOTAL RESULTS COUNT FOR PAGINATION
        if( isset( $query['p'] ) ) {
            $sqlTotal = "SELECT COUNT(*) FROM " . $tableName;

            // APPLY FILTERS TO TOTAL RESULTS COUNT
            if( isset( $query['filter'] ) ) {
                $sqlTotal .= $this->handleFilterQuery($query['filter'], $tableName);
            }
            $total = $connection->query($sqlTotal);

            return [ 'results' => $stmt->fetchAll(), 'total' => $total->fetchColumn() ];
        }

        return [ 'results' => $stmt->fetchAll() ];
    }

    private function create($sql = '', $args = [], $tableName) {
        if( empty( $sql ) ) return [];

        $create = $this->executeQuery($sql, $args, __FUNCTION__, $tableName);
        $checkMessage = $create ? $this->returnQueryMessage($tableName, 4) : $this->returnQueryMessage($tableName, 6);

        return [ 'data' => [], 'message' => $checkMessage ];
    }

    private function read($sql = '', $args = [], $tableName, $query = []) {
        if( empty( $sql ) ) return [];
        $data = [];

        $results = $this->executeQuery($sql, $args, __FUNCTION__, $tableName, $query);
            
        // NO RESULTS FOUND
        if( empty( $results['results'] ) )
            return [ 'data' => [], 'message' => $this->returnQueryMessage('', 1) ];

        // RESULTS FOUND 
        $data = [ 'data' => $results['results'], 'message' => $this->returnQueryMessage($tableName) ];

        // APPEND PAGINATION
        if( isset( $results['total'] ) )
            $data['pagination'] = $this->pagination( intval( $query['p'] ), intval( $results['total'] ));

        return $data;
    }

    public function update($sql = '', $args = [], $tableName) {
        if( empty( $sql ) ) return [];

        $update = $this->executeQuery($sql, $args, __FUNCTION__, $tableName);
        $checkMessage = $update ? $this->returnQueryMessage($tableName, 2) : $this->returnQueryMessage($tableName, 7);

        return [ 'data' => [], 'message' => $checkMessage ];
    }

    public function delete($sql = '', $args = [], $tableName) {
        if( empty( $sql ) ) return [];

        $delete = $this->executeQuery($sql, $args, __FUNCTION__, $tableName);
        $checkMessage = $delete ? $this->returnQueryMessage($tableName, 3) : $this->returnQueryMessage($tableName, 8);

        return [ 'data' => [], 'message' => $checkMessage ];
    }

    private function pagination($currentPage = 1, $all = 0) {
        $previousPage = $currentPage - 1;
        $nextPage = $currentPage + 1;
        $totalPages = ceil( $all / $this->limit );
        return [
            'first_page'    => 1,
            'previous_page' => $previousPage <= 0 ? 1 : $previousPage,
            'current_page'  => $currentPage,
            'next_page'     => $nextPage > $totalPages ? $totalPages : $nextPage,
            'last_page'     => $totalPages,
            'total_pages'   => $totalPages,
            'total_results' => $all
        ];
    }

    public function userAuth($authHeader = NULL) {
        $userCredentials = $this->decodeUserAuth($authHeader);
        $connection = $this->connectToDatabase();
        $sql = 'SELECT id FROM users WHERE username=:username AND password=:password';
        $stmt = $connection->prepare($sql);
        $stmt->setFetchMode($connection::FETCH_OBJ);
        $stmt->execute([ ':username' => $userCredentials[0], ':password' => sha1($userCredentials[1]) ]);
        $user = $stmt->fetch();
        return $user;
    }

    private function decodeUserAuth($authHeader) {
        $authHeaderCredentials = str_replace('Basic ', '', $authHeader[0]);
        $decodeCredentials = base64_decode($authHeaderCredentials);
        return explode(':', $decodeCredentials);
    }
}