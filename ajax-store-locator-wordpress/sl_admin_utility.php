<?php 
function random_string( )
  {
    $character_set_array = array( );
    $character_set_array[ ] = array( 'count' => 7, 'characters' => 'abcdefghijklmnopqrstuvwxyz' );
    $character_set_array[ ] = array( 'count' => 1, 'characters' => '0123456789' );
    $temp_array = array( );
    foreach ( $character_set_array as $character_set )
    {
      for ( $i = 0; $i < $character_set[ 'count' ]; $i++ )
      {
        $temp_array[ ] = $character_set[ 'characters' ][ rand( 0, strlen( $character_set[ 'characters' ] ) - 1 ) ];
      }
    }
    shuffle( $temp_array );
    return implode( '', $temp_array ). date("Y"). date("m"). date("d"). date("H");
  }
function random_strings($alphaCount, $numberCount )
  {
    $character_set_array = array( );
    $character_set_array[ ] = array( 'count' => $alphaCount, 'characters' => 'abcdefghijklmnopqrstuvwxyz' );
    $character_set_array[ ] = array( 'count' => $numberCount, 'characters' => '0123456789' );
    $temp_array = array( );
    foreach ( $character_set_array as $character_set )
    {
      for ( $i = 0; $i < $character_set[ 'count' ]; $i++ )
      {
        $temp_array[ ] = $character_set[ 'characters' ][ rand( 0, strlen( $character_set[ 'characters' ] ) - 1 ) ];
      }
    }
    shuffle( $temp_array );
    return implode( '', $temp_array );
  }
   function findexts ($filename) { 
	 $filename = strtolower($filename) ; 
	 $exts = pathinfo($filename, PATHINFO_EXTENSION);
	 return $exts; 
 }
 
function rrmFile($filePath) {
	if(file_exists($filePath)){
		 unlink($filePath);
	}
}
?>
