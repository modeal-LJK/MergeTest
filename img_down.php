<?php
$P_IMG = $_GET['P_IMG'];
$P_NAME = $_GET['P_NAME'];
    $filename = $P_NAME.'_'.$P_IMG;
    $DownloadPath = $_SERVER['DOCUMENT_ROOT'].'/_upload/P_REVIEW/'.$P_IMG; // 파일 경로

    
    header("Content-type: application/octet-stream"); 
    header("Content-Length: ".filesize("$DownloadPath"));
    header("Content-Disposition: attachment; filename=$filename"); // 다운로드되는 파일명 (실제 파일명과 별개로 지정 가능)
    header("Content-Transfer-Encoding: binary"); 
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Pragma: public"); 
    header("Expires: 0"); 
    $fp = fopen($DownloadPath, "rb"); 
    fpassthru($fp);
    fclose($fp);
?>
