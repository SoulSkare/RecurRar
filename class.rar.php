<?php

################################################################
#	class.Rar class for moviebot php code by
#	kieron.welman@gmail.com
################################################################

Class Rar
{
    function pack(){
        echo "test \n";
    }
    
    function unpack($rarFile, $destFolder, $type, $delRar){
        # extract
        #   check if corrupted
        # $type can be either single or multi for deletion

        $rar_corrupt_check = exec("unrar t '$rarFile'");
        if (strlen($rar_corrupt_check) == 6) { // String(6) "All OK"

            # unrar
            echo "  # class.rar -> unraring................... \n";
            $unrar_check = exec("unrar e -o+ '$rarFile' '$destFolder/'");

            #   check if extraction was successful
            if(strlen($unrar_check) == 6) { // String(6) "All OK"
                echo "  # class.rar -> SUCCESS $unrar_check \n";

                if ($type == "multi" && $delRar == TRUE){
                    # delete rar
                    echo "  # class.rar -> remove $type rar files \n";

                    try {
                        foreach (glob(dirname("$rarFile") . "/*.r*") as $fR){
                            $path_parts = pathinfo("$fR");

                            if (preg_match('#[0-9]#',$path_parts['extension'])){
                                unlink($fR);
                                //echo 'has number';
                            }else if ($path_parts['extension'] == "rar"){
                                unlink($fR);
                                //echo 'no number';
                            }
                        }
                    } catch (Exception $e) {
                        echo '!! class.rar Caught exception: ',  $e->getMessage(), "\n";
                    }

                    try {
                        foreach (glob(dirname("$rarFile") . "/*.part*.rar") as $fR){
                            unlink($fR);
                        }
                    } catch (Exception $e) {
                        echo '!! class.rar Caught exception: ',  $e->getMessage(), "\n";
                    }

                }
                if ($type == "single" && $delRar == TRUE){
                    # delete rar
                    echo "  # class.rar -> remove rar file \n";
                    $rarFile = "$rarFile";
                    exec("rm '$rarFile'");
                }

                # save folder location to variable -- also wtf am I doing
                $final_folder_name = "$destFolder";

                return basename($final_folder_name);
            }
            else{
                echo "!! class.rar -> ERROR $unrar_check \n\n";
                exit;
            }
        }
    }

    function recursUnpack($path, $type){
        $di = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        # foreach file
        $IS_RAR = TRUE;
        while ($IS_RAR == TRUE){
            foreach($di as $name => $fio) {
                $it = glob("$path/*.rar");
                if (empty($it)){
                    $IS_RAR = FALSE;
                    echo "  # no rar files left \n";
                    break;
                }else{
                    $IS_RAR = TRUE;
                }

                if (strstr("$fio", ".rar")) {
                    $this->unpack("$fio", $fio->getPath(), $type, TRUE);
                    //echo $fio, "\r\n";
                }
            }
        }

        return basename($path);
    }

    function rescursUnpack2($path, $type){
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        $filesB = array();
        foreach ($rii as $fileB) {

            if ($fileB->isDir()){
                continue;
            }
            $path_parts = pathinfo(basename($fileB->getPathname()));

            if ($path_parts['extension'] == "rar"){
                //$this->recursUnpack($path, $type);
                $this->recursUnpack($fileB->getPath(), $type);

                echo $fileB->getPathname() . "\n";
            }
                //$filesB[] = $fileB->getPathname();
        }
    }
}
?>