<?php 

//http://www.techjahid.com/test/all_books.php?link=https://www.abebooks.com/servlet/SearchResults?bi=0&bsi=0&bx=off&ds=100&kn=aa&n=200000080+100121501&pt=book&recentlyadded=all&sortby=5&cm_sp=pan-_-srp-_-new

$url = $_SERVER['REQUEST_URI'];

$url = explode("=", $url);

$q_str = $_SERVER['QUERY_STRING'];


$q_str = substr($q_str, 5);
//$i=0;

//$q_str = str_replace("bsi=0","bsi".$i,$q_str);

// echo $q_str;

// exit;


function file_get_contents_curl($url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

$list = array
(
"Name;/;Author;/;Publisher;/;Image",

);



for($i=0;$i<=2100;$i+=100){

$html = file_get_contents_curl(str_replace('bsi=0','bsi='.$i,$q_str));

//parsing begins here:
$doc = new DOMDocument();
@$doc->loadHTML($html);
$divs = $doc->getElementsByTagName( 'div' );

foreach( $divs as $div ){
    if( $div->getAttribute( 'class' ) === 'cf result' ){
        
        $metas = $div->getElementsByTagName( 'meta' );



        $meta = $metas->item(1);
        if($meta->getAttribute('itemprop') == 'name')
         $name = $meta->getAttribute('content');
        //echo '<br>';

        $meta = $metas->item(2);
        if($meta->getAttribute('itemprop') == 'author')
         $author = $meta->getAttribute('content');
        //echo '<br>';

        $meta = $metas->item(4);
        if($meta->getAttribute('itemprop') == 'publisher')
         $publisher = $meta->getAttribute('content');
        //echo '<br>';
        $imgs = $div->getElementsByTagName( 'img' );
        $img = $imgs->item(0);
         if($img->getAttribute('class') == 'srp-item-image')
         $image_link = $img->getAttribute('src');
      
        //echo '<hr>';
        if($name!=''){
            $key =$name.";/;".$author.";/;".$publisher.";/;".$image_link;
        array_push($list,$key );
        }
        
    }
}

    
}


$file = fopen("booklist.csv","w");

foreach ($list as $line)
  {
  fputcsv($file,explode(';/;',$line));
  }

fclose($file);




?>

<a href="booklist.csv"> Download The Book List</a>