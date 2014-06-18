<?php
/**
 * Copyright 2011 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

require 'fbsdk3/src/facebook.php';

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
  'appId'  => '1494683367413192',
  'secret' => '1dbb225d9a5550084ca13f289656150a',
));

// Get User ID
$user = $facebook->getUser();

// We may or may not have this data based on whether the user is logged in.
//
// If we have a $user id here, it means we know the user is logged into
// Facebook, but we don't know if the access token is valid. An access
// token is invalid if the user logged out of Facebook.

if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e, 3, 'err.log');
    $user = null;
  }
}

// 	n or logout url will be needed depending on current user state.
if ($user) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {
  $loginUrl = $facebook->getLoginUrl(	array(
       'scope' => 'publish_stream'
	  ));
}


?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>Post to Friend's Wall</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Example post to friend's wall with Facebook PHP SDK" />
    <style>
      body {font-family: 'Lucida Grande', Verdana, Arial, sans-serif; background-color: #f2f2f2; }
      h1 a {text-decoration: none;color: #3b5998;}
      h1 a:hover { text-decoration: underline;}
	  form {border: 1px solid #eee; padding: 20px; width: 550px;}
      textarea, select, input, label {width: 500px; border: 1px solid #ddd; 
		height: 20px; clear: both; margin: 10px;}
	  textarea {height: 100px;}
	  label {border: none; font-weight: bold;}
	  input#submit {width: 100px;}
	  #loginWrapper {position: absolute; top: 2px; left: 450px;}
    </style>
  </head>
  <body>
    <h1>Post to Friend's Wall</h1>

	<div id="loginWrapper">
    <?php if ($user): ?>
      <a href="<?php echo $logoutUrl; ?>">Logout</a>
    <?php else: ?>
        <a href="<?php echo $loginUrl; ?>">Login with Facebook</a>
    <?php endif ?>
	</div>
	

    <?php if ($user){

		$user_friends = $facebook->api('/me/friends');
		sort($user_friends['data']);
	
		if(isset($_POST['submit'])) {
			$sendTo = $_POST['friend'];
			$link = $_POST['link'];
			$message = $_POST['message'];
			
			// all options: http://stackoverflow.com/questions/691425/how-do-you-post-to-the-wall-on-a-facebook-page-not-profile
			$attachment = array('message' => $message, 'link' => $link );

			if($result = $facebook->api("/$sendTo/feed/",'post', $attachment)) {
				$feedbackMessage = "Message sent to friend $sendTo";
			} else {
				$feedbackMessage = "Oops something went wrong";
			} 
		}
    ?>
	
		<form id="selectFriend" name="selectFriend" method="post">
			<label for="Friend">Friend:</label>
			<select id="friend" name="friend">
				<?php 
				foreach($user_friends['data'] as $f){
					echo '<option value="'.$f['id'].'">'.$f['name'] .'</option>';
				} 
				?>
			</select>
			<label for="URL">URL:</label>
			<input id="link" name="link">
			<label for="Message">Message:</label>
			<textarea id="message" name="message"></textarea>
			<input type="submit" name="submit" id="submit" value="Send!">
		</form>
		
		<?php
		if(isset($feedbackMessage)) echo $feedbackMessage;
		?>
	
	<?php } ?>
	
  </body>
</html>
