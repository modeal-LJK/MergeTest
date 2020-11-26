<?php
$P_IMG = $_GET['P_IMG'];
$P_NAME = $_GET['P_NAME'];
    $filename = $P_NAME.'_'.$P_IMG;
    $DownloadPath = $_SERVER['DOCUMENT_ROOT'].'/_upload/P_REVIEW/'.$P_IMG; // 파일 경로

    
   

    Header("Location:/_sys_/page/main.php");



    $fp = fopen($DownloadPath, "rb"); 
    fpassthru($fp);
    fclose($fp);
?>
