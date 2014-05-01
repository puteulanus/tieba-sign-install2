<?php
/*

**功能：新建目录和文件；编辑、删除、上传文件
**2010-12-15 Tian
**相应的操作文件要有权限才能成功！
*http://hi.baidu.com/bluid
*/
if( ini_get('register_globals') ){exit('php.ini register_globals must is Off! ');}
//禁止 session.auto_start
if ( ini_get('session.auto_start') != 0 ){exit('php.ini session.auto_start must is 0 ! ');}
session_start();
//检查和注册外部提交的变量
foreach($_REQUEST as $_k=>$_v){if( strlen($_k)>0 && eregi('^(tjq_)',$_k)){exit('Request var not allow!');}}
function _RunMagicQuotes(&$svar){if(!get_magic_quotes_gpc()){if( is_array($svar) ){foreach($svar as $_k => $_v) $svar[$_k] = _RunMagicQuotes($_v);}else{$svar = addslashes($svar);}}return $svar;}
foreach(Array('_GET','_POST','_COOKIE') as $_request){foreach($$_request as $_k => $_v) ${$_k} = _RunMagicQuotes($_v);}

//$phpself = $_server[php_self]?$_server[php_self]:$_server[script_name];
//echo $phpself;exit();
//define("phpself", preg_replace("/(.{0,}?/+)/", "", $phpself));

//管理登录账户和密码
$adminname="default";
include('config.php');

//设定创建目录的权限
$dir_purview = '0777';
$db_language="UTF-8";
define('WROOT',ereg_replace("[/\\]{1,}",'/',dirname(__FILE__)));
define('PFILE',ereg_replace("[/\\]{1,}",'/',__FILE__));

/*
 * 找到web的根目录
 */
$froot=$_SERVER[PHP_SELF];
define("phpself", str_replace('/','',$froot));
if($froot){
    $froot=explode('/',$froot);
    unset($froot[count($froot)-1]);
    $FROOT=implode('/', $froot);
    $FROOT=str_replace($FROOT,'',WROOT);
}else{
    $FROOT=WROOT;
}
define('PROOT',$FROOT);

/*
 * 是否允许删除目录及目录下所有文件
 * 会把目下所有文件和子目录全部删除,建议禁用
 */
$isdirfiles=false;

/*设定可以编辑的文件  包括编辑文件内容、重命名
 * $editfiles=array('ALLFILES');//全部文件
 */
$editfiles=array('php','phtml','tpl','sql','txt','htm','html','js','css');

/*设定允许删除的文件
 * $delfiles=array('ALLFILES');//全部文件
 */
$delfiles=array('php','phtml','tpl','sql','txt','html','docx',
'jpg','gif','png','bmp',
'zip','gz','rar','zip','iso',
'doc','xls','xlsx','ppt','wps',
'wav','mp3','wmv','flv','swf','db'
);

/*设定允许新建的文件
 * $newfile=array('ALLFILES');//全部文件
 */
$newfile=array('php','phtml','tpl','sql','txt','htm','html','js','css');

/*
 * 设定允许上传的文件
 */
$upfilestr=array('txt','html','php'
'jpg','gif','png','bmp',
'zip','gz','rar','zip','iso',
'doc','xls','xlsx','ppt','wps',
'wav','mp3','wmv','flv','swf','db'
);

/*
 * 禁止上传的文件
 */
$not_allowall = "pl|cgi|asp|aspx|jsp|php3|shtm|shtml";

if($_FILES){
    $keyarr = array('name','type','tmp_name','size');
    foreach($_FILES as $_key=>$_value){
        foreach($keyarr as $k)
        {
            if(!isset($_FILES[$_key][$k]))
            {
                exit('Request Error!');
            }
        }
        $$_key = $_FILES[$_key]['tmp_name'] = str_replace("\\\\","\\",$_FILES[$_key]['tmp_name']);
        ${$_key.'_name'} = $_FILES[$_key]['name'];
        ${$_key.'_type'} = $_FILES[$_key]['type'];
        ${$_key.'_size'} = $_FILES[$_key]['size'];
        if(eregi("\.(".$not_allowall.")$",${$_key.'_name'}))
        {
            exit('Request var not allow for uploadsafe!');
        }
        if(empty(${$_key.'_size'}))
        {
            ${$_key.'_size'} = @filesize($$_key);
        }
    }
}
$isf=ereg_replace("[/\\]{1,}",'/',dirname(__FILE__));
if(empty($fis)){
    $fis=trim(str_replace(PROOT, '', $isf),'/');
}
$fis=ereg_replace("[/\\]{1,}",'/',$fis);
if($fis=='/'){
    $f=PROOT.'/';
    $fis='';
}else{
    $f=PROOT.'/'.$fis;
    $fis=$fis.'/';
}
$ufdir=str_replace(PROOT,'',$f);
if($a=='login'){
    if($username==$adminname&&$passwd==$password){
        $_SESSION['ADMIN']=$adminname;
        $_SESSION['PASS']=$password;
    }
    echo '<script>location.href="?"</script>';exit();
}
if($a=='out'){$_SESSION['ADMIN']='';$_SESSION['PASS']='';echo '<script>window.close();</script>';}
echo '<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html;charset='.$db_language.'"><title>Index of '.$ufdir.'</title><script>function formsub(fid,ta,tars){fnm=document.getElementById(fid);fnm.target=tars;fnm.action=ta;fnm.submit();};function opwindow(url){wfname=Math.random()+"";wfname=wfname.replace(".","");window.open(url,\'wf\'+wfname,\'height=600,width=800,top=50,left=50,scrollbars=auto, resizable=yes\');}</script></head><body>';
if($_SESSION['ADMIN']!==$adminname||$_SESSION['PASS']!==$password){
    echo '<form action="?a=login" method="post"><p>Username: <input name="username" type="text" value="" style="width:120px" /></p><p>Password: <input name="passwd" type="password" value="" style="width:120px" /></p><p><input type="submit" value="Submit"></p></form>';
    exit();
}

if($a=='phpinfo'){echo phpinfo();exit();}

//服务器信息
if($a=='servinfo'){

}




//上传文件
if($a=='uploadfiles'){
    $ufname=$uploadnewfile_name;
    $file_snames = explode('.', $ufname);
    $file_sname = strtolower(trim($file_snames[count($file_snames)-1]));
    if($upfilestr[0]!=='ALLFILES'&&!in_array($file_sname,$upfilestr)){
        echo '不能上传此文件！';exit();
    }
    $truepath=rtrim(PROOT.$ufdir,'/').'/'.$ufname;
    if(file_exists($truepath)){
        echo 'error 文件已经存在.';exit();
    }
    if(move_uploaded_file($uploadnewfile, $truepath)){
        echo '<script>alert(\'返回查看是否上传成功\');opener.location.reload();window.close();</script>';
    }
    exit();
}
//创建目录
if($a=='newdir'){
    if(!empty($fdirname)){
        $truepath=rtrim(PROOT.$ufdir,'/').'/'.$fdirname;
        if(is_dir($truepath)){
            echo '<p>目录已经存在</p>';
        }else{
            mkdir($truepath,$dir_purview);
            chmod($truepath,$dir_purview);
        }
    }
}

//创建文件
if($a=='newfiles'){
    if($newfname==''){exit(1);}
    echo '<form method="post" action="?a=newfiles&c=save">
    <input name="fis" type="hidden" value="'.($fis?trim($fis,'/'):'/').'" />';
    if(empty($fnamehs)){
        if($newfile[0]!=='ALLFILES'&&!in_array($fnamehs,$newfile)){
            exit('2');
        }
    }
    $truepath=rtrim(PROOT.$ufdir,'/').'/'.$newfname.$fnamehs;
    if(file_exists($truepath)){
        echo '已经存在此文件';exit();
    }
    if($c=='save'){
        $wf=fopen($truepath,'w');
        $editcontent=stripslashes($filescontent);
        fwrite($wf,$filescontent);
        fclose($wf);
        echo rtrim($ufdir,'/').'/'.$newfname.$fnamehs;
        echo '<p>新建文件成功</p>';
        echo '<a href="javascript:window.close()">关闭</a>';
        exit();
    }
    echo '<input name="newfname" type="hidden" value="'.$newfname.'" />';
    echo '<input name="fnamehs" type="hidden" value="'.$fnamehs.'" />';
    echo '<p><textarea name="filescontent" cols="96" rows="20"></textarea></p>';
    echo '<input type="submit" value="保存"></form>';
    exit();
}

//重命名
if($b=='rename'){
    echo '<b>重命名文件：'.$ufdir.'</b>';
    $ufdirarr=explode('.', $ufdir);
    $rcstr=strtolower($ufdirarr[count($ufdirarr)-1]);
    if($editfiles[0]!=='ALLFILES'&&!in_array($rcstr,$editfiles)){
        echo '- 不能修改此文件。';
        exit();
    }
    if($newfilename!==''){
        $newfilename=str_replace('/', '',str_replace('\\', '',$newfilename));
    }
    $newfilename.='.'.$rcstr;
    
    $dirarr2=explode('/', $ufdir);
    $rcstr2=strtolower($dirarr2[count($dirarr2)-1]);
    
    $newfrename=substr($f,0,strlen($f)-strlen($rcstr2)).$newfilename;
    if(!file_exists($f)){echo '文件不存在.';exit();}
    
    if($c=='save'){
        if(file_exists($newfrename)){echo '文件已存在.';exit();}
        //$oldnames=str_replace(PROOT, '', $f);
        //$newnames=str_replace(PROOT, '', $newfrename);
        //rename($oldnames, $newnames);
        rename($f, $newfrename);
        echo '<script>alert(\'返回查看是否修改成功\');opener.location.reload();window.close();</script>';
        exit();
    }
    
    echo '<form method="post" action="?b=rename&c=save">';
    echo '<p>输入文件名称: <input name="newfilename" type="text"  /> 不包括扩展名 <input type="submit" value="Submit" /></p>';
    echo '<input name="fis" type="hidden" value="'.trim($ufdir,'/').'" />';
    echo '</form>';
    exit();
}

//修改文件
if($b=='edit'){
    echo '<b>修改：'.$ufdir.'</b>';
    $ufdirarr=explode('.', $ufdir);
    $rcstr=strtolower($ufdirarr[count($ufdirarr)-1]);
    if($editfiles[0]!=='ALLFILES'&&!in_array($rcstr,$editfiles)){
        echo '- 不能修改此文件。';
        exit();
    }
    if(file_exists($f)){
        if($c=='save'){
            $wf=fopen($f,'w');
            $editcontent=stripslashes($editcontent);
            fwrite($wf, $editcontent);
            fclose($wf);
            echo '<p>修改成功</p>';
        }
        $ef=fopen($f, 'r');
        $fc='';
        if(filesize($f)>0){
            $fc=fread($ef, filesize($f));
        }
        fclose($ef);
        echo '<form method="post" action="?b=edit&fis='.trim($ufdir,'/').'&c=save"><p><textarea name="editcontent" cols="92" rows="26">'.htmlspecialchars($fc).'</textarea></p>';
        echo '<input type="submit" value="保存"></form>';
    }
}

//删除单个文件
if($b=='del'){
    $ufdirarr=explode('.', $ufdir);
    $rcstr=strtolower($ufdirarr[count($ufdirarr)-1]);
    if($delfiles[0]!=='ALLFILES'&&!in_array($rcstr,$delfiles)){
        echo '<script>alert("不能删除此文件！");window.close();</script>';exit();
    }
    if(file_exists($f)){unlink($f);echo '<script>alert("返回查看是否删除成功！");opener.location.reload();window.close();</script>';exit();}
}

//删除目录及子目录文件
if($b=='deldir'){
    if($isdirfiles){
        //echo $f;
        if(is_dir($f)){deltree($f);echo '<script>alert("返回查看是否删除成功！");opener.location.reload();window.close();</script>';exit();}
    }
}
if(!is_dir($f)){exit();}
$dir=opendir($f);
echo '<h1>Index of '.$ufdir.'</h1>';
//echo ($fis?trim($fis,'/'):'/');
echo '<form id="formds" name="formds" method="post" enctype="multipart/form-data"><input name="fis" type="hidden" value="'.($fis?trim($fis,'/'):'/').'" />';
echo '<p>#<a href="?a=out">Out</a> #<a href="?a=phpinfo" target="_blank">PHPINFO</a> #Create directory: <input type="text" name="fdirname" size="10" /> <input onclick="formsub(\'formds\',\'?a=newdir\')" type="button" value="Submit" >&nbsp;&nbsp;#New File: <input type="text" name="newfname" size="10" />';
if($newfile&&$newfile[0]!=='ALLFILES'){
    echo ' <select name="fnamehs" size="1">';
    foreach ($newfile as $v){
        echo '<option value=".'.$v.'">.'.$v.'</option>';
    }
    echo '</select>';
}


echo ' <input onclick="formsub(\'formds\',\'?a=newfiles\',\'_blank\')" type="button" value="Submit" >';
echo ' #Upload: <input size="10" type="file" name="uploadnewfile" id="uploadnewfile" /> <input onclick="formsub(\'formds\',\'?a=uploadfiles\',\'_blank\')" type="button" value="Submit" >';
echo '</p></form>';
echo '<ul>';
if(rtrim($f,'/')!=PROOT){
    echo '<li>';
    $brurl=trim(str_replace(PROOT,'',dirname($f)),'/');
    $brurl=$brurl==''?'/':$brurl;
    echo '<a href="?a=msfile&fis='.$brurl.'">Parent Directory</a>';
    echo '</li>';
}
while (false!==$file=readdir($dir)){
    if($file=='..' || $file=='.'){
        continue;
    }else{
        if(is_dir($f.'/'.$file)){
            $dname=trim(str_replace(PROOT, '', $f.'/'.$file),'/');
            echo '<li><a href="?fis='.$dname.'" style="color:#ED9D08">'.$file.'</a>';
            if($isdirfiles){
                echo ' - <a href="javascript:opwindow(\'?b=deldir&fis='.$fis.$file.'\')">Del</a>';
            }
            echo '</li>';
        }else{
            echo '<li>'.$file;
            $rsarr=explode('.', $file);
            $rcstr=strtolower($rsarr[count($rsarr)-1]);
            if($editfiles[0]=='ALLFILES' || in_array($rcstr,$editfiles)){
                echo ' - <a href="javascript:opwindow(\'?b=edit&fis='.urlencode($fis.$file).'\')">Edit</a>';
                echo ' - <a href="javascript:opwindow(\'?b=rename&fis='.urlencode($fis.$file).'\')">Rename</a>';
            }
            if($delfiles[0]=='ALLFILES' || in_array($rcstr,$delfiles)){
                echo ' - <a href="javascript:opwindow(\'?b=del&fis='.urlencode($fis.$file).'\')">Del</a>';
            }
            echo '</li>';
        }
        
    }
}
echo '</ul></body></html>';


function deltree($pathdir){
    global $isdirfiles;
    if(!$isdirfiles){return 0;exit();}
    if(is_empty_dir($pathdir)){rmdir($pathdir);}
    else{
        $d=dir($pathdir);
        while($a=$d->read()) {
            if(is_file($pathdir.'/'.$a) && ($a!='.') && ($a!='..')){unlink($pathdir.'/'.$a);}
            if(is_dir($pathdir.'/'.$a) && ($a!='.') && ($a!='..')){
                if(is_empty_dir($pathdir.'/'.$a)){
                    //rmdir($pathdir.'/'.$a);
                }else{
                    deltree($pathdir.'/'.$a);
                }
                @rmdir($pathdir.'/'.$a);
            }
        }
        $d->close();
    }
    if(is_empty_dir($pathdir)){rmdir($pathdir);}
}

//判断目录是否为空
function is_empty_dir($pathdir){
    $d=opendir($pathdir);
    $i=0;
    while($a=readdir($d)){$i++;}
    closedir($d);
    if($i>2){return false;}
    else{return true;}
}
