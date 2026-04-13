<?php  
$nod_jump = true;
include("includes/common.php");
$id=$_GET['id'];
$res = $DB->get_row("select * from dwz_url where id='$id' limit 1");
$urls=$res['url'];  //被编码原网站
if ($res) {
    if ($res['state'] == 0) {
        //include ROOT . 'template/page/fj.php';
         header("Location:  https://c.pc.qq.com/middleb.html?pfurl=%E9%93%BE%E6%8E%A5%E5%B7%B2%E8%A2%AB%E5%B0%81%E7%A6%81");
    }


    if ($res['deltime'] == '') {
        //exit(base64_decode($urls));
header("Location: ".base64_decode($urls));
        
    } else {

       header("Location:  https://c.pc.qq.com/middleb.html?pfurl=404");
        exit();
    }
} else {

   header("Location:  https://c.pc.qq.com/middleb.html?pfurl=404");
    exit();
}
?>
