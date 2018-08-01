<?php 

    function lerPagina($pagina = 1) {
        $url = 'http://www.tradecardsonline.com/?action=searchCards&game_id=82&page='.$pagina;

        $ch = curl_init ($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $raw = curl_exec($ch);
        curl_close ($ch);

        $dom = new DOMDocument();
        @$dom->loadHTML($raw);

        $tables = $dom->getElementsByTagName('table');
        $rows = $tables->item(0)->getElementsByTagName('tr');

        if (count($rows)) {
            foreach ($rows as $row) {
                $a = $row->getElementsByTagName('a');
                if ($a->item(1) && strpos($a->item(1)->getAttribute('href'), 'findCard')) {
                    lerItem($a->item(1)->getAttribute('href'));
                }
            }

            lerPagina($pagina + 1);
        }
        
        return false;
    }

    function lerItem($url) {
        $url = 'http://www.tradecardsonline.com'.str_replace('findCard', 'selectCard', $url);

        $ch = curl_init ($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $raw = curl_exec($ch);
        curl_close ($ch);

        $dom = new DOMDocument();
        @$dom->loadHTML($raw);

        $tables = $dom->getElementsByTagName('table');
        $rows = $tables->item(0)->getElementsByTagName('tr');

        foreach ($rows as $row)
        {
            $a = $row->getElementsByTagName('a');
            $nomeCarta = $row->getElementsByTagName('strong');
            if ($a->item(0) && strpos($a->item(0)->getAttribute('href'), 'chaotic-tcg')) {
                echo $nomeCarta->item(0)->nodeValue.' '.$a->item(0)->getAttribute('href').'<br />';
                baixarImagem($a->item(0)->getAttribute('href'), $nomeCarta->item(0)->nodeValue);
            }
        }
    }

    function baixarImagem($url, $nomeCarta) {
        $url = 'http://www.tradecardsonline.com'.$url;
        $saveto = str_replace('\'', '', str_replace(' ', '_', strtolower($nomeCarta))).'.png';
    
        $ch = curl_init ($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);    
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.tradecardsonline.com/im/selectCard/card_id/177295');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $raw = curl_exec($ch);
        curl_close ($ch);
    
        if(file_exists($saveto)){
            unlink($saveto);
        }
    
        $fp = fopen($saveto,'x');
        fwrite($fp, $raw);
        fclose($fp);
    }

    lerPagina();