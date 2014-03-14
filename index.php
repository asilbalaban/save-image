<?php
        header('Content-Type: text/html; charset=utf-8');
        set_time_limit(0);


        function get_url_contents($url){
                $crl = curl_init();
                $timeout = 5;
                curl_setopt ($crl, CURLOPT_URL,$url);
                curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
                $ret = curl_exec($crl);
                curl_close($crl);
                return $ret;
        }
        /*
        function save_image($inPath, $outPath) {
            @mkdir(dirname($outPath), 0777, true);

            $in  = fopen($inPath,  "rb");
            $out = fopen($outPath, "wb");

            while (!feof($in)) {
                $read = fread($in, 8192);
                fwrite($out, $read);
            }

            fclose($in);
            fclose($out);
        }
        */
        function save_image($url,$saveto){
            @mkdir(dirname($saveto), 0777, true);
            $ch = curl_init ($url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
            $raw=curl_exec($ch);
            curl_close ($ch);
            if(file_exists($saveto)){
                unlink($saveto);
            }
            $fp = fopen($saveto,'x');
            fwrite($fp, $raw);
            fclose($fp);
        }


        /**
         * Resimlerin alınacağı domain adresi
         */
        $domain = 'http://www.ozparkelaminat.com/ozpar/';

        /**
         * Resimlerin alınacağı sayfa url'si
         */
        $url    = 'http://www.ozparkelaminat.com/ozpar/default2.asp?alfaCMS=151200000000';

        /**
         * Resimler hangi klasöre kayıt edilecek
         */
        $dizin  = 'laminant_parke'.DIRECTORY_SEPARATOR.'sunfloor';

        /**
         * Resim prefix
         */
        $prefix = '';


        /* URL'deki TÜM RESİMLERİ AL */

        $html = get_url_contents($url);


        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($html);
        libxml_clear_errors();
        $imageTags = $doc->getElementsByTagName('img');

        $images = array();
        foreach($imageTags as $tag) {
            $images[] = $tag->getAttribute('src');
        }


        foreach ($images as $imageUrl) {

            if(substr($imageUrl, 0, 4) != 'http') {
                $imageUrl = $domain . $imageUrl;
            }

            $filename   = $prefix. $imageUrl;
            $filename   = explode($domain, $imageUrl);
            $filename   = $filename[1];
            $filepath   = getcwd() . DIRECTORY_SEPARATOR . $dizin. DIRECTORY_SEPARATOR  . $filename;
            save_image($imageUrl, $filepath);
            echo "<font>{$filename} kaydedildi</font><br />";
        }

        echo "{$url} | {$dizin}'e kaydedildi";

