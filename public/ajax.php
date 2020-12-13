<?php

$data = $_POST;

$keys = array_keys( $_POST );

// echo json_encode( [
//     'success'   =>  false,
//     'data'      =>  'example data',
//     'errors'    =>  $keys
// ] );

echo json_encode( [
    'success'   =>  true,
    'data'      =>  'example data',
] );