<?php
//file_put_contents('prova', '');
//$response = new HttpResponse();
//
//$response->setData('Corpo risposta');
//$response->send();

//echo 'Ciao' . NULL;
//header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
//header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
//echo 'ciao';

//class a {
//    protected function __construct() {
//        echo 'Construct a';
//        echo '<br/>';
//    }
//}
//
//class b extends a {
//    function __construct() {
//        parent::__construct();
//        
//        echo 'Construct b';
//        echo '<br/>';
//    }
//}
//
//new b();

//$date = date("Y-m-d H:i:s:") . substr(microtime(), 2, 8);
////$date = ':' . trim(microtime());
//echo $date;

//header('Cioa come stai?', false, 4560);
//http_response_code(404);

//$dir = sys_get_temp_dir();
//echo $dir;
//file_put_contents($dir . '/Test/' . 'prova.ini', '');

//echo session_save_path();
//echo '<br/>';
//echo session_save_path('C:\xampp\tmp\prova');
//echo '<br/>';
//echo session_save_path();

/*
 * Prova dipo assegnazione tra array
 */
//$ary1 = array('primo', 'secondo');
//$ary2 = &$ary1;
//$ary2[0] = 'terzo';
//
//var_dump($ary1);

//$_GET['ciao'] = 'yyyyy';
//$_GET[] = 'qqqqqqqqqqq';
//echo $_GET['ciao'];
//echo '<br/>';
//echo $_GET[0];

//$var = 1;
//echo empty($var);
//
//echo phpversion();

//$flags = 1|4;
//
//echo $flags . '<br/>';
//if (($flags & 3) == 3)
//    echo 'Esiste';

//ob_start();
//include 'test2.php';
//$contents = ob_get_contents();
//ob_end_clean();
//echo $contents;

$groupName = 'Role->Administrators';
$roleName = substr($groupName,
                -(strlen($groupName) - strlen('Role->')),
                strlen($groupName) - strlen('Role->'));
echo $roleName;
?>
