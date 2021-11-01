<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->map(['GET', 'POST', 'PUT', 'DELETE'], '/api/notes[/{id}]', function (Request $request, Response $response, array $args) {
    // TABLE COLUMNS
    $tableColumns = [
        "columns"           => "heading, text, private, uid, fid, type",
        "placeholders"      => ":heading, :text, :private, :uid, :fid, :type",
        "setPlaceholders"   => "heading=:heading, text=:text, private=:private, uid=:uid, fid=:fid, type=:type",
        "selectColumns"     => "notes.id, notes.heading, notes.text, notes.private, notes.uid, notes.fid, notes.type, notes.created",
    ];

    // CURRENT REQUEST HEADERS
    $requestHeaders = $request->getHeaders();

    // CURRENT REQUEST METHOD
    $requestMethod = $request->getMethod();

    // OVERRIDE POST METHOD FOR PUT REQUEST
    if( isset( $requestHeaders['X-Http-Method-Override'] ) ) {
        $requestMethod = 'PUT';
    }

    // REQUEST BODY
    $body = $request->getParsedBody();

    // REQUEST QUERY
    $query = $request->getQueryParams();

    // RESPONSE OUTPUT
    $result = [
        'success'   => false,
        'data'      => [],
        'message'   => '',
        'error'     => []
    ];

    try {
        $dbConnection = new Db();

        // AUTHORIZE USER
        $authUser = $dbConnection->userAuth($requestHeaders['Authorization']);
        if( !$authUser ) {
            throw new Exception('Unauthorized');
        }

        // CHECK URI PARAMETER AND QUERY PARAMETERS
        if( !empty( $args ) && isset( $args['id'] ) && !is_numeric( $args['id'] ) )
            throw new Exception('Invalid parameter');

        if( !empty( $query ) ) {
            // REMOVE SORT AND PAGE FOR SINGLE NOTE
            if( isset( $args['id'] ) ) {
                unset( $query['p'] );
                unset( $query['sort'] );
            }

            // VALIDATE QUERY PARAM VALUE
            if( isset( $query['p'] ) && !is_numeric( $query['p'] ) ) {
                throw new Exception('Invalid parameter');
            }
        }

        // HANDLE RESPONSE DATA
        $data = $dbConnection->handleRequestMethod($requestMethod, 'notes', $tableColumns, $authUser->id, $args, $body, $query);

        $result['data'] = $data['data'];
        $result['success'] = true;
        $result['message'] = $data['message'];

        // APPEND PAGINATION TO RESPONSE
        if( isset( $data['pagination'] ) ) {
            $result['pagination'] = $data['pagination'];
        }

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-type', 'application/json');
    } catch( PDOException $pex ) {
        // APPLY A GENERIC ERROR MESSAGE ON PRODUCTION AND SAVE THE PDO EXCEPTION MESSAGE -> $pex->getMessage() TO A LOG FILE OR DATABASE
        $result['error']['message'] = $pex->getMessage();
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-type', 'application/json');
    } catch( Exception $ex ) {
        $result['error']['message'] = $ex->getMessage();
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-type', 'application/json');
    }
});

$app->map(['GET', 'POST', 'PUT', 'DELETE'], '/api/folders[/{id}]', function (Request $request, Response $response, array $args) {
    // TABLE COLUMNS
    $tableColumns = [
        "columns"           => "uid, name, path",
        "placeholders"      => ":uid, :name, :path",
        "setPlaceholders"   => "uid=:uid, name=:name, path=:path",
        "selectColumns"     => "folders.id, folders.uid, folders.name, folders.path, folders.created",
    ];

    // CURRENT REQUEST HEADERS
    $requestHeaders = $request->getHeaders();

    // CURRENT REQUEST METHOD
    $requestMethod = $request->getMethod();

    // OVERRIDE POST METHOD FOR PUT REQUEST
    if( isset( $requestHeaders['X-Http-Method-Override'] ) ) {
        $requestMethod = 'PUT';
    }

    // REQUEST BODY
    $body = $request->getParsedBody();

    // REQUEST QUERY
    $query = $request->getQueryParams();

    // RESPONSE OUTPUT
    $result = [
        'success'   => false,
        'data'      => [],
        'message'   => '',
        'error'     => []
    ];

    try {
        $dbConnection = new Db();

        // AUTHORIZE USER
        $authUser = $dbConnection->userAuth($requestHeaders['Authorization']);
        if( !$authUser ) {
            throw new Exception('Unauthorized');
        }

        // CHECK URI PARAMETER AND QUERY PARAMETERS
        if( !empty( $args ) && isset( $args['id'] ) && !is_numeric( $args['id'] ) )
            throw new Exception('Invalid parameter');

        if( !empty( $query ) ) {
            // REMOVE SORT AND PAGE FOR SINGLE NOTE
            if( isset( $args['id'] ) ) {
                unset( $query['p'] );
                unset( $query['sort'] );
            }

            // VALIDATE QUERY PARAM VALUE
            if( isset( $query['p'] ) && !is_numeric( $query['p'] ) ) {
                throw new Exception('Invalid parameter');
            }
        }

        // HANDLE RESPONSE DATA
        $data = $dbConnection->handleRequestMethod($requestMethod, 'folders', $tableColumns, $authUser->id, $args, $body, $query);

        $result['data'] = $data['data'];
        $result['success'] = true;
        $result['message'] = $data['message'];

        // APPEND PAGINATION TO RESPONSE
        if( isset( $data['pagination'] ) ) {
            $result['pagination'] = $data['pagination'];
        }

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-type', 'application/json');
    } catch( PDOException $pex ) {
        // APPLY A GENERIC ERROR MESSAGE ON PRODUCTION AND SAVE THE PDO EXCEPTION MESSAGE -> $pex->getMessage() TO A LOG FILE OR DATABASE
        $result['error']['message'] = $pex->getMessage();
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-type', 'application/json');
    } catch( Exception $ex ) {
        $result['error']['message'] = $ex->getMessage();
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-type', 'application/json');
    }
});