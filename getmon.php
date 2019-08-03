<?php
            require 'vendor/autoload.php';
            $linfo = new \Linfo\Linfo;
            session_start();
            $parser = $linfo->getParser();
            $data = array();
            $cpu = $parser->getCPU();
            $net = $parser->getNet();
            $ram = $parser->getRam();
            $disk = $parser->getMounts();
            if(sizeof($cpu)>0){
            $data['cpu']['core'] = sizeof($cpu);
            $data['cpu']['model'] = $cpu[0]['Model'];
            $data['cpu']['arch'] = $parser->getCPUArchitecture();
            $data['cpu']['usage_percentage'] = $cpu[0]['usage_percentage'];
            $data['cpu']['speed'] = MtoG($cpu[0]['MHz']);
            $tmpa = [];
            $tmpa[0] = $cpu[0]['usage_percentage'];
            for($i=1;$i<10;$i++){
                $tmpa[$i] = ($_SESSION['cpuarr'][$i-1])? $_SESSION['cpuarr'][$i-1] : 0;
            }
            $_SESSION['cpuarr'] = $tmpa;
            $data['cpu']['graph']= $_SESSION['cpuarr'];
            }else{
            $data['cpu']['usage_percentage'] = 
            $cpu[0]['usage_percentage'] = 
            $data['cpu']['speed'] = 
            $data['cpu']['arch'] = 
            $data['cpu']['core'] = null;
            $data['cpu']['graph'] = [0,0,0,0,0,0,0,0,0,0];
            }

            if(sizeof($net)>0){
            $i = 0;
            foreach($net as $key=>$value){
                $data['net'][$i]['name'] = $key;
                $data['net'][$i]['type'] = $value['type'];
                $data['net'][$i]['recieved'] = BtoG($value['recieved']['bytes']);
                $data['net'][$i]['sent'] = BtoG($value['sent']['bytes']);
                $i++;
            }
            }else{
                $data['net'][0]['name'] = 
                $data['net'][0]['type'] =
                $data['net'][0]['recieved'] =
                $data['net'][0]['sent'] = null;
            }

            if(sizeof($ram)>0){
            $data['ram']['total'] = BtoG($ram['total']);
            $data['ram']['type'] = $ram['type']." memory";
            $data['ram']['free'] = BtoG($ram['free']);
            $data['ram']['use'] = round(BtoG($ram['total'])-BtoG($ram['free']),1);
            $tmpa = [];
            $tmpa[0] = round($data['ram']['use']*10);
            for($i=1;$i<10;$i++){
                $tmpa[$i] = ($_SESSION['ramarr'][$i-1])? $_SESSION['ramarr'][$i-1] : 0;
            }
            $_SESSION['ramarr'] = $tmpa;
            $data['ram']['graph']= $_SESSION['ramarr'];
            }else{
                $data['ram']['total'] = 
                $data['ram']['use'] =
                $data['ram']['type'] =
                $data['ram']['free'] = null;
                $data['ram']['graph'] = [0,0,0,0,0,0,0,0,0,0];
            }
            if(sizeof($disk)>0){
                $i = 0;
                foreach($disk as $value){
                    $data['disk'][$i]['used'] = BtoG($value['used']);
                    $data['disk'][$i]['free'] = BtoG($value['free']);
                    $data['disk'][$i]['size'] = BtoG($value['size']);
                    $data['disk'][$i]['name'] = "drive ".$value['label']."(".BtoG($value['size'])." GB)";
                    $data['disk'][$i]['used_p'] = $value['used_percent'];
                    $data['disk'][$i]['free_p'] = $value['free_percent'];
                    $i++;
                }
            }else{
                    $data['disk'][$i]['used'] = 
                    $data['disk'][$i]['free'] = 
                    $data['disk'][$i]['size'] = 
                    $data['disk'][$i]['name'] = 
                    $data['disk'][$i]['used_p'] = 
                    $data['disk'][$i]['free_p'] = null;
            }
            $data['name'] = $parser->getHostName();
            $data['uptime'] = $parser->getUpTime();
            $data['test'] = $parser->getContains();
            $data['os']['name']  = ($parser->getOS()!="")? $parser->getOS() : null;
            echo json_encode($data); // and a whole lot more


            function BtoG($bytes){
                return round(intval($bytes)/1024/1024/1024,1);
            }
            function MtoG($mhz){
                return round(intval($mhz)/1000,1);
            }
            ?>