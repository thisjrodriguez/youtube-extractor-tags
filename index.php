<?php 

    //keywords
    $keywords = array();
    $videos = array();

    //Selección de keywords
    $keyword = 'Seguridad Wordpress';
    $keyword = urlencode($keyword);

    //realizamos el curl
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, 'https://www.youtube.com/results?search_query='.$keyword); 
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $content = curl_exec($ch); 
    curl_close($ch);      

    //remove new lines
     $content = preg_replace("/[\n\r]/", "", $content);
  

    //preg match
    preg_match_all('/ytInitialData"] = (.*)ytInitialPlayerResponse"]/i', $content, $resp);
    if(isset($resp[1]) AND is_array($resp[1])) {
        
        //guardo en una variable el contenido
        $content = $resp[1][0];
        
        //marranada
        $content = preg_replace('/}};(.*)/', '}}', $content);
        
        //parse to array
        $content = json_decode($content, true);
        
        //Loop vídeos
        foreach($content['contents']['twoColumnSearchResultsRenderer']['primaryContents']['sectionListRenderer']['contents'][0]['itemSectionRenderer']['contents'] as $video) {
            if(!isset($video['videoRenderer'])) { continue; }
            
            $videos[] = trim($video['videoRenderer']['videoId']);
        }
        
        foreach($videos as $code) {
            $content = file_get_contents('http://youtube.com/get_video_info?video_id='.$code);
            $content = parse_str($content, $info);
            
            //foreach keywords
            $info['keywords'] = explode(",", $info['keywords']);
            foreach($info['keywords'] as $k) {
                
                //trim
                $k = trim($k);
                
                //pasar a minusculas
                $k = strtolower($k);
                
                //check if exists                
                if(!isset($keywords[$k])) {
                    $keywords[$k] = 1;
                } else {
                    $keywords[$k]++;
                }
            }
            
     
     
        }
        
        arsort($keywords);
            print_r($keywords);   
        print_r($videos);
        
    }

?>