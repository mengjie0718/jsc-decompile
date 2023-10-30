<?php
// define( 'WP_MEMORY_LIMIT', '20480M' );

require_once __DIR__ . '/src/Decompile.php';
function decopliejsc($jscpath){
    echo $jscpath .CLIENT_EOL;
    $decompile = new Irelance\Mozjs34\Decompile($jscpath);
    $decompile->run();
    // $decompile->runResult();
    $contexts = $decompile->getContexts();

    $result = "";
    foreach ($contexts as $index => $context) {
        $result=$result.'__FUNC_' . $index . '__('.implode(',', $context->argvs).'){'.CLIENT_EOL;
        $result=$result. implode(CLIENT_EOL,$context->getContent());
        $result=$result.CLIENT_EOL.'}'.CLIENT_EOL.'//__FUNC_'. $index .'_END' . CLIENT_EOL;
    }
    unset($decompile);
    $decompile = null;
    $jscpath =str_replace(".jsc",".js",$jscpath);
    echo $jscpath ."\n";
    $myfile = fopen($jscpath, "w");
    fwrite($myfile, $result);
    fclose($myfile);
    
}
function list_file($date){
    //1、首先先读取文件夹
    if(!is_dir($date)){
        decopliejsc($date);
        return;
    }
    $temp=scandir($date);
    //遍历文件夹
    foreach($temp as $v){
        $a=$date.'/'.$v;
       if(is_dir($a)){//如果是文件夹则执行
      
           if($v=='.' || $v=='..'){//判断是否为系统隐藏的文件.和..  如果是则跳过否则就继续往下走，防止无限循环再这里。
               continue;
           }
           list_file($a);//因为是文件夹所以再次调用自己这个函数，把这个文件夹下的文件遍历出来
       }else{
        if(strstr($a,".jsc") >-1 ){

            if(strstr($a, "object.jsc") || strstr($a, "script.jsc") || strstr($a, "utils.jsc")){
                echo $a;
            }else{
                
                try {
                    decopliejsc($a) ;
                } catch (Exception $e) {
                    echo "Error:".$a;
                }
            }
            
            
        }
        
       }
      
    }
}
    list_file($argv[1]);

 ?>