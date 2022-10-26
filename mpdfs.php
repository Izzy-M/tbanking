<?php
require_once 'comp/vendor/autoload.php';
require_once 'functions.php';
function printreceipt(){

    $mpdf= new \Mpdf\Mpdf();
    $mpdf->WriteHTML('<h2>Morara</h2>');
    $mpdf->Output('saleid','D');
    return $mpdf;

}
function printbalancesheet(){

    $mpdf= new \Mpdf\Mpdf();
    $mpdf->WriteHTML('<h2>Morara</h2>');
    $mpdf->Output('saleid','D');
    echo 'test';

}
function printjournal(){

}
function printinvoice(){
    
}
