<?php

function makeFBTable($wall, $title = "Facebook", $type = "Posts") {	
	global $prefs;
	
	if ( $type ) echo "<h2>".$title."</h2>";
	echo '<table class="table">
			<thead>
				<tr>
					<th>'.$type.'</th>
				</tr>
			</thead><tbody>';
			
	foreach ( $wall as $post ) {		
		if ( $type == "Notifications" ) {
			if ( $post['application']['name'] == "Feed Comments" || $post['application']['name'] == "Photos" || $post['application']['name'] == "Likes" ) {
				echo '<tr><td><img src="http://graph.facebook.com/'.$post['from']['id'].'/picture" /></td>';
				if ( !empty($post['from']) ) echo '<td><a style="color: #999;" onclick="ajax(\'/lib/facebook/index.php?call=/'.$post['from']['id'].'\',\'span3\');">'. $post['from']['name'] .'</a>:<br />';
				if ( !empty($post['message']) ) echo $post['message'].'<br />';
				if ( $post['application']['name'] == "Likes" ) echo $post['title'].'<br />';
				
				if ( $post['application']['name'] == "Feed Comments" || $post['application']['name'] == "Likes" ) {
					$cid = explode("posts/",$post['link']);
					$cid = $cid[1];
					$qm = explode("?",$cid);
					$cid = $qm[0];
					$qm = "?".$qm[1];
				}
				
				if ( $post['application']['name'] == "Photos" ) {
					$cid = explode("fbid=",$post['link']);
					$cid = explode("&",$cid[1]);
					$cid = $cid[0];
				}
				
				echo '<a onclick="ajax(\'/lib/facebook/index.php?call=/'.$cid.'/comments'.$qm.'\', \'span3\');">&raquo; See comment</a><br />'; 
				echo '</td></tr>';
			
			}
			else echo '<tr><td><img src="/thumb/'.base64_encode('http://graph.facebook.com/'.$post['application']['id'].'/picture').'/50" /></td><td>'.$post['title'].'<br /><a href="'.$post['link'].'" target="_BLANK">&raquo; Go to application</a></td></tr>';
			continue;
		}
		
		if ( empty($post['message']) ) continue;
		
		if ( isset($post['to']['data'][0]['name']) ) {
			echo '<tr><td><img src="http://graph.facebook.com/'.$post['from']['id'].'/picture" /></td><td class="msgRow"><a style="color: #999;" onclick="ajax(\'/lib/facebook/index.php?call=/'.$post['from']['id'].'\',\'span3\');">'. $post['from']['name'] .'</a> <i class="icon-chevron-right"></i> <a style="color: #999;" onclick="ajax(\'/lib/facebook/index.php?call=/'.$post['to']['data'][0]['id'].'\',\'span3\');">'. $post['to']['data'][0]['name'] .'</a>';
			$cnt = count($post['to']['data']) - 1;
			$users = '';
			
			foreach ( $post['to']['data'] as $user ) {
				$users .= $user['name'].', ';
			}
			
			$users = substr($users, 0, -2);
			$users = preg_replace("/\, ([a-zA-Z ]*)$/i"," and $1", $users);
			
			if ( isset($post['to']['data'][1]['name']) ) echo ' and <a style="color: #999;" href="#" class="tt" rel="tooltip" title="'.$users.'">'. $cnt .' others</a>:<br />'.linkify($post).'<br />';
			else echo ':<br />'.linkify($post).'<br />';
		}
		else echo '<tr><td><img src="http://graph.facebook.com/'.$post['from']['id'].'/picture" /></td><td class="msgRow"><a style="color: #999;" onclick="ajax(\'/lib/facebook/index.php?call=/'.$post['from']['id'].'\',\'span3\');">'. $post['from']['name'] .'</a>:<br />'.linkify($post).'<br />';
			
		if ( preg_match("/youtube\.com\/watch/", $post['link']) ) {
			$url = explode("v=",$post['link']);
			if ( preg_match("/&/", $url[1]) ) {
				$url = explode("&",$url[1]);
				$url = $url[0];
			}
			else $url = $url[1];
			echo '<a onclick="loadYT(\''. $url .'\');" data-toggle="modal" href="#mdl"><img src="http://img.youtube.com/vi/'.$url.'/2.jpg" /></a><br />';
		}
		elseif ( preg_match("/youtu\.be\//",$post['link']) ) {
			$url = preg_replace("/https?\:\/\//","",$post['link']);
			$url = explode("/",$url);
			if ( preg_match("/&/", $url[1]) ) {
				$url = explode("&",$url[1]);
				$url = $url[0];
			}
			else $url = $url[1];
			echo '<a onclick="loadYT(\''. $url .'\');" data-toggle="modal" href="#mdl"><img src="http://img.youtube.com/vi/'.$url.'/2.jpg" /></a><br />';
		}
		elseif ( !empty($post['picture']) ) echo '<a href="'.$post['link'].'" target="_BLANK"><img src="/thumb/'.base64_encode($post['picture']).'" /></a><br />';
		//elseif ( !empty($post['picture']) ) echo '<a href="'.$post['link'].'" target="_BLANK"><img src="'.$post['picture'].'" /></a><br />';
		
		
		if ( empty($post['likes']['count']) ) $likes = 0;
		else $likes = $post['likes']['count'];
		
		if ( $type !== "Notifications" ) {
			echo '<span class="twToolBox">
				<a onclick="fbLike(\''.$post['id'].'\');"><img src="/lib/layout/img/like.png" /></a> '.$likes.' ';
			if ( is_numeric($post['comments']['count']) ) echo '<a onclick="fbShowComments(\''.$post['id'].'\');"><img src="/lib/layout/img/comment.png" /></a> '.$post['comments']['count'];
			
			if ( $prefs['display_time'] == "relative" ) echo '<span class="pull-right">'.timetostr(strtotime($post['created_time'])).'</span>';
			if ( $prefs['display_time'] == "absolute" ) echo '<span class="pull-right">'.date("d-m H:i:s O",strtotime($post['created_time'])).'</span>';
			if ( $prefs['display_time'] !== "relative" && $prefs['display_time'] !== "absolute" ) echo '<span class="pull-right">'.date($prefs['display_time'],strtotime($post['created_time'])).'</span>';
			
			echo '</span>';
		}
		
		echo '</td></tr>';
	}
		
	echo "</tbody>
				</table>";
	echo "<script>$('#tt').tooltip();</script>";
	if ( $_SESSION['debug'] ) print_r($wall);
}

function linkify($post) {
	$wordwrap = explode(" ",$post['message']);
	$message = '';
	
	foreach ( $wordwrap as $word ) {
		if ( strlen($word) > 40 && !preg_match("/https?\:\/\//",$word) ) $message .= wordwrap($word, 40, '<br>', true)." ";
		else $message .= $word." ";
	}
	
	if ( !empty($post['link']) && !empty($post['picture']) ) $message = str_replace($post['link'], "", $message);
	else $message = str_replace($post['link'],'<a onclick="loadIFrame(\''. str_replace("https","http",$post['link']) .'\');" data-toggle="modal" href="#mdl">'. $post['link'] .'</a>', $message);
	
	return $message;
}

?>