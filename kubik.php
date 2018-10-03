<?php
date_default_timezone_set('Asia/Jakarta');
require_once("sdata-modules.php");
/**
 * @Author: Eka Syahwan
 * @Date:   2017-12-11 17:01:26
 * @Last Modified by:   Eka Syahwan
 * @Last Modified time: 2018-08-17 15:13:34
*/


##############################################################################################################
$config['deviceCode'] 		= 
'358870069439150';
$config['tk'] 				
= 
'ACD9fyq_wc1KXoigdCj3Jx4-uymlu32C8LZxdHRodw';
$config['token'] 			
= 
'4762DsMig1oDqatJMuSKi8ufyGngjgAmZ2XozfZ0meu0v1-dA2zgvPH7ZQb4F162onMxC9T97Y4hbg';
$config['uuid'] 			
= 
'96535a5af3eb49f3a889a51edd8b747b';
$config['sign'] 			
= 
'7de1a19629a1572dfef8a8c7523a1034';
$config['android_id'] 		= 
'b355ed402f3c9bb4';
##############################################################################################################


for ($x=0; $x <1; $x++) { 
	$url 	= array(); 
	for ($cid=0; $cid <20; $cid++) { 
		for ($page=0; $page <10; $page++) { 
			$url[] = array(
				'url' 	=> 'http://api.beritaqu.net/content/getList?cid='.$cid.'&page='.$page,
				'note' 	=> 'optional', 
			);
		}
		$ambilBerita = $sdata->sdata($url); unset($url);unset($header);
		foreach ($ambilBerita as $key => $value) {
			$jdata = json_decode($value[respons],true);
			foreach ($jdata[data][data] as $key => $dataArtikel) {
				$artikel[] = $dataArtikel[id];
			}
		}
		$artikel = array_unique($artikel);
		echo "[+] Mengambil data artikel (CID : ".$cid.") ==> ".count(array_unique($artikel))."\r\n";
	}
	while (TRUE) {
		$timeIn30Minutes = time() + 30*60;
		$rnd 	= array_rand($artikel); 
		$id 	= $artikel[$rnd];
		$url[] = array(
			'url' 	=> 'http://api.beritaqu.net/timing/read',
			'note' 	=> $rnd, 
		);
		$header[] = array(
			'post' => 'OSVersion=8.0.0&android_channel=google&android_id='.$config['android_id'].'&content_id='.$id.'&content_type=1&deviceCode='.$config['deviceCode'].'&device_brand=samsung&device_ip=114.124.239.'.rand(0,255).'&device_version=SM-A730F&dtu=001&lat=&lon=&network=wifi&pack_channel=google&time='.$timeIn30Minutes.'&tk='.$config['tk'].'&token='.$config['token'].'&uuid='.$config['uuid'].'&version=10047&versionName=1.4.7&sign='.$config['sign'], 
		);
		$respons = $sdata->sdata($url , $header); 
		unset($url);unset($header);
		foreach ($respons as $key => $value) {
			$rjson = json_decode($value[respons],true);
			echo "[+][".$id." (Live : ".count($artikel).")] Message : ".$rjson['message']." | Poin : ".$rjson['data']['amount']." | Read Second : ".$rjson['data']['current_read_second']."\r\n";
			if($rjson[code] == '-20003' || $rjson['data']['current_read_second'] == '330' || $rjson['data']['amount'] == 0){
				unset($artikel[$value[data][note]]);
			}
		}
		if(count($artikel) == 0){
			sleep(30);
			break;
		}
		sleep(5);
	}
	$x++;
}
