remove_shortcode('gallery');
add_shortcode('gallery', 'custom_gallery');

require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-includes/media.php');
function custom_gallery($attr) {
	//if(is_page('Our Musical Family')) {
	$html = '<style>#loading {display: flex; z-index:1000; position: absolute; width: 100%; height: 100%; background-color: black; align-content: center; justify-content: center;} body footer {bottom:-100px;} #musical-family-gallery img {width: 200px; padding: 3px;} #musical-family-gallery {position: relative;} #gallery-pages {position: absolute; bottom: -100px; width: 100%;} #gallery-pages #prevButton {float: left; opacity: 0.3;}
	#gallery-pages #page-num {margin-left: 39%; float: left; font-size: 24px;}
	#gallery-pages #nextButton {float: right;}
	
	</style><div id="loading"><img style="position: absolute;" src="http://classicurbanharmony.net/wp-content/themes/muse/2.gif" /></div><div id="musical-family-gallery" data-featherlight-gallery data-featherlight-filter="a">';
	//$post_id = $attr['id'];
	$attachment_ids = $attr['ids'];
	file_put_contents(get_home_path() . '/attachments.txt', $attachment_ids);
	//$page = end(explode('/', $url));
	$attachment_ids_array = explode(',', $attachment_ids);
	$images_per_page = 50;
	//$pages = ceil(count($attachment_ids_array)/$images_per_page);
	//$columns = 8;
	$index = 0;
 /* echo $attachment_ids;
	foreach($attachment_ids_array as $id) {
		echo $id;
	} */
	while ($index+1 < $images_per_page) {
		if($index+1 > count($attachment_ids_array)) {break;}
			$html = $html . '<div class="gallery-image"><a href="'. wp_get_attachment_url($attachment_ids_array[$index]) . '"><img alt="' . wp_get_attachment_caption($attachment_ids_array[$index]) .'" src="' . wp_get_attachment_url($attachment_ids_array[$index]) . '" /></a></div>';
			$index++;	
	}
	if(count($attachment_ids_array) > 50) {
		$html = $html . "<div id='gallery-pages'><button id='prevButton'>Previous Page</button><p id='page-num'>1</p><button id='nextButton'>Next Page</button></div></div><style>.post-share {margin-top: 90px!important;}</style>";
	}
	else {
		$html=$html . "</div>";
	}
	$html=$html . "<script>
  function reload() {
	  var container = jQuery('#musical-family-gallery');
	  container.imagesLoaded( function(){
		jQuery('#loading').css('display', 'none');
		//jQuery('.gallery-image').css('opacity', 1.0);
		container.masonry('layout');
		
	  });
	  
    jQuery(\".gallery-image a\").click(function(event) {
        var caption = jQuery(this).find('img').attr('alt');
		setTimeout(function() {jQuery('<div style=\"font-weight: bold;\" class=\"caption\">').text(caption).appendTo( jQuery('.featherlight-content'));
		jQuery(\".featherlight-next\").click(function(event) {
		setTimeout(function() {
			var src = jQuery('.featherlight-content').find('img').attr('src');
			var caption = '';
			 jQuery(\".gallery-image img\").each(function() {if(jQuery(this).attr('src') == src) {caption = jQuery(this).attr('alt')}})
			jQuery('<div style=\"font-weight: bold;\" class=\"caption\">').text(caption).appendTo( jQuery('.featherlight-content'));
		}, 800)
	}); jQuery(\".featherlight-previous\").click(function(event) {
		setTimeout(function() {
			var src = jQuery('.featherlight-content').find('img').attr('src');
			var caption = '';
			 jQuery(\".gallery-image img\").each(function() {if(jQuery(this).attr('src') == src) {caption = jQuery(this).attr('alt')}})
			jQuery('<div style=\"font-weight: bold;\" class=\"caption\">').text(caption).appendTo( jQuery('.featherlight-content'));
		}, 800)
	});
		}, 100);
		
	});
  }
  jQuery(function(){
 	//jQuery('#loading').css('display', 'flex');
    var container = jQuery('#musical-family-gallery');
  
    container.imagesLoaded( function(){
	jQuery('#loading').css('display', 'none');
      container.masonry({
        itemSelector : '.gallery-image',
		columnWidth : 200
      });
    });
  });
  jQuery(document).ready(function(jQuery) {
	
	var ajax_url = '" . admin_url('admin-ajax.php') . "';  
	jQuery('#nextButton').click(function () {
		
		var currentPage=parseInt(jQuery('#page-num').text())+1;

		var data = {
			'action': 'change_page',
			'page': currentPage
			
		};
		jQuery.post(ajax_url, data, function(html) {
			if(html.match(/<img/) != null) {
				//jQuery('.gallery-image').css('display', 'none');
				var respHtml = jQuery(html);
				var container = jQuery('#musical-family-gallery');
				var item = jQuery('.gallery-image');
				container.masonry('remove', item);
				container.masonry('layout');
				container.append(respHtml).masonry('appended', respHtml);
				container.masonry('reloadItems');
				setTimeout(reload, 1000);
				//jQuery('.gallery-image').css('opacity', 0);
				jQuery('#loading').css('display', 'flex');
				jQuery('#page-num').empty();
				jQuery('#page-num').text(currentPage);
			} else {
				jQuery('#nextButton').css('opacity', 0.3);
			}
		});
		if(currentPage>1) {
			jQuery('#prevButton').css('opacity', 1.0);
		}
		
	});
	jQuery('#prevButton').click(function () {
		
		var currentPage=parseInt(jQuery('#page-num').text())-1;
		if(currentPage > 0) {
			var data = {
				'action': 'change_page',
				'page': currentPage
				
			};
			jQuery.post(ajax_url, data, function(html) {
				var container = jQuery('#musical-family-gallery');
				var item = jQuery('.gallery-image');
				container.masonry('remove', item);
				container.masonry('layout');
				var respHtml = jQuery(html);
				container.append(respHtml).masonry('appended', respHtml);
				container.masonry('reloadItems');
				setTimeout(reload, 1000);
				jQuery('#page-num').empty();
				jQuery('#page-num').text(currentPage);
				jQuery('#nextButton').css('opacity', 1.0);
			});
			if(currentPage<2) {
				jQuery('#prevButton').css('opacity', 0.3);
			}
		}
	});
  });
</script></div>";
	return $html;
		
	//} else {
		//$result = gallery_shortcode($attr);
		//return $result;
	//}
} 
add_action( 'wp_ajax_change_page', 'change_page' );
add_action( 'wp_ajax_nopriv_change_page', 'change_page' );

function change_page() {
	$page = $_POST['page'];
	$html = '';
	//$post_id = $attr['id'];
	$attachment_ids = file_get_contents(get_home_path() . 'attachments.txt');
	//$page = end(explode('/', $url));
	$attachment_ids_array = explode(',', $attachment_ids);
	$images_per_page = 50;
	//$pages = ceil(count($attachment_ids_array)/$images_per_page);
	//$columns = 8;
	$index = ($images_per_page-1)*($page-1);
	$max_index = $page*$images_per_page;
	//$html = $html . $index;
 /* echo $attachment_ids;
	foreach($attachment_ids_array as $id) {
		echo $id;
	} */
	while ($index+1 < $max_index) {
		if($index+1 > count($attachment_ids_array)) {break;}
			$html = $html . '<div class="gallery-image"><a href="'. wp_get_attachment_url($attachment_ids_array[$index]) . '"><img alt="' . wp_get_attachment_caption($attachment_ids_array[$index]) . '" src="' . wp_get_attachment_url($attachment_ids_array[$index]) . '" /></a></div>';
			$index++;	
	}
	echo $html;
	wp_die();
}