<?php
// no direct access
defined('_EXEC') or die('Restricted access');

/**
 * Main processing class for Dropbox Uploader
 * 
 * Does some checking for requirements first, performs dropbox upload, then 
 * sends a notification email to the account owner.
 *
 * TODO: These functions need a lot of tidying up and separating out. We'll get there!
 * 
 * Copyright (c) 2010 Mark Osborne
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author Mark Osborne
 * @version 1.0.1
 */

class Processor {
  
  var $Msg = null;
  var $Theme = "modern_blue"; // default theme, incase one isn't specified in config
  var $ThemeStyle = "";
  var $FavIcon = "";
  var $AppleTouchIcon = "";
  var $Logo = ""; 
  var $MaxUpload = "0";
  var $PageText = "";
  var $FooterText = "";
  var $SocialLinks = "";
  var $PNGFix = "";
  var $PasswordField = "";
  var $PageTitle = "Dropbox Uploader";
  
  public function __construct($post_data, $file_data) {
  
      // Get config
      $_DUConfig = new DUConfig();
      
      // Set Theme
      if(!empty($_DUConfig->Theme))
          $this->Theme = $_DUConfig->Theme;
      $this->ThemeStyle = '<link rel="stylesheet" href="themes/'.$this->Theme.'/css/style.css?v=1">';
      $this->FavIcon = '<link rel="shortcut icon" href="favicon.ico">';
      $this->AppleTouchIcon = '<link rel="apple-touch-icon" href="apple-touch-icon.png">';
      
      // Set Logo
      if(!empty($_DUConfig->Logo)) {
          if($_DUConfig->Logo=='text' && !empty($_DUConfig->LogoText)) {
              $this->Logo = '<div id="logo_div">'.$_DUConfig->LogoText.'</div>';
          }
          if($_DUConfig->Logo=='image' && !empty($_DUConfig->LogoPath)) {
              $this->Logo = '<div id="logo_div"><img src="'.$_DUConfig->LogoPath.'" id="logo" /></div>';
              if($_DUConfig->LogoPNGFix=="yes")
                  $this->PNGFix = '<!--[if lt IE 7 ]><script src="lib/js/dd_belatedpng.js?v=1"></script><script>DD_belatedPNG.fix("#logo");</script><![endif]-->';
          }
      }
              
      
      // Set Other Page Elements from Config
      if(!empty($_DUConfig->PageText)) {
          // Get max upload size (PHPInfo)
          $max_upload = (int)(ini_get('upload_max_filesize'));
          $max_post = (int)(ini_get('post_max_size'));
          $memory_limit = (int)(ini_get('memory_limit'));
          $this->MaxUpload = min($max_upload, $max_post, $memory_limit)."Mb";
          
          $_DUConfig->PageText = str_replace('%%max_upload%%', $this->MaxUpload, $_DUConfig->PageText);
          $this->PageText = $_DUConfig->PageText;
      }
      if(!empty($_DUConfig->FooterText))
          $this->FooterText = $_DUConfig->FooterText;
      if(!empty($_DUConfig->TwitterLink))
          $this->SocialLinks = '<a href="'.$_DUConfig->TwitterLink.'" target="_blank">Follow us on Twitter</a>';
      if(!empty($_DUConfig->Password))
          $this->PasswordField = '<fieldset><h2>Type in your Upload Password<em> required</em></h2><input id="password" name="password" size="30" type="password" class="input_textbox required" value="" /></fieldset>';
      if(!empty($_DUConfig->PageTitle))
          $this->PageTitle = $_DUConfig->PageTitle;
      
      // Create form key for our form
      $formKey = new formKey();
      
      // Check for POST
      if ($post_data) {
          try {
              //Form key is invalid, show an error
              if(!isset($post_data['form_key']) || !$formKey->validate())
              	  throw new Exception('Bad Form Key');
              
              if(!empty($_DUConfig->Password) && $post_data['password']!=$_DUConfig->Password)
                  throw new Exception('Incorrect Password');
              
              // Rename uploaded file to reflect original name
              if ($file_data['file']['error'] !== UPLOAD_ERR_OK)
                  throw new Exception('File was not successfully uploaded from your computer.');
      
              $tmpDir = uniqid('/tmp/DropboxUploader-');
              if (!mkdir($tmpDir))
                  throw new Exception('Cannot create temporary directory!');
      
              if ($file_data['file']['name'] === "")
                  throw new Exception('File name not supplied by the browser.');
      
              $tmpFile = $tmpDir.'/'.str_replace("/\0", '_', $file_data['file']['name']);
              if (!move_uploaded_file($file_data['file']['tmp_name'], $tmpFile))
                  throw new Exception('Cannot rename uploaded file!');
              
              // Upload the file to Dropbox (uses uploader.class.php)
              $uploader = new DropboxUploader($_DUConfig->LoginEmail, $_DUConfig->LoginPassword);
              $uploader->upload($tmpFile, $_DUConfig->LoginDestination);
              
              // Get Email Data
              $SenderName = $this->clean_data($post_data['name']);
              $SenderEmail = $this->clean_data($post_data['email']);
              $Subject = $this->clean_data($post_data['subject']);
              
              // Build Email Message
              $UserMessage = $this->clean_data($post_data['message']);
              $Destination = $_DUConfig->LoginDestination."/".$file_data['file']['name'];
              // html message
              $_DUConfig->Message = str_replace('%%user_message%%', $UserMessage, $_DUConfig->Message);
              $_DUConfig->Message = str_replace('%%destination%%', $Destination, $_DUConfig->Message);
              $_DUConfig->Message = str_replace('%%sender_name%%', $SenderName, $_DUConfig->Message);
              // optional plain text message
              $_DUConfig->MessagePt = str_replace('%%user_message%%', $UserMessage, $_DUConfig->MessagePt);
              $_DUConfig->MessagePt = str_replace('%%destination%%', $Destination, $_DUConfig->MessagePt);
              $_DUConfig->MessagePt = str_replace('%%sender_name%%', $SenderName, $_DUConfig->MessagePt);
              
              // Build and send the email (uses email.class.php)
              $mail = new Email();
              $mail->To = $_DUConfig->ToEmail;
              $mail->From = $SenderName." <".$SenderEmail.">";
              if(!empty($_DUConfig->CCEmail))
                  $mail->Cc = $_DUConfig->CCEmail;
              if(!empty($_DUConfig->BCCEmail))
                  $mail->Bcc = $_DUConfig->BCCEmail;
              $mail->Subject = $_DUConfig->SubjectPrefix . $Subject;
              if($_DUConfig->HtmlEmail==true) {
                  $mail->SetMultipartAlternative($_DUConfig->MessagePt, $_DUConfig->Message);
              } else {
                  $mail->TextOnly = true;
                  $mail->Content = $_DUConfig->MessagePt;
              }
              $mail->Send();
              
              // Success Message
              $this->Msg = '<span class="success">File successfully uploaded. Thank you!</span>';
              
          } catch(Exception $e) {
              $this->Msg = '<span class="error">Error: ' . htmlspecialchars($e->getMessage()) . '</span>';
          }
      
          // Clean up
          if (isset($tmpFile) && file_exists($tmpFile))
              unlink($tmpFile);
      
          if (isset($tmpDir) && file_exists($tmpDir))
              rmdir($tmpDir);
      }
  }
  
  // Data cleaning function
  public function clean_data($string) {
      
      // strip slashes
      if (get_magic_quotes_gpc()) {
          $string = stripslashes($string);
      }
      
      // strip html tags
      $string = strip_tags($string);
      
      // check for spammers
      $headers = array(
          "/to\:/i",
          "/from\:/i",
          "/bcc\:/i",
          "/cc\:/i",
          "/Content\-Transfer\-Encoding\:/i",
          "/Content\-Type\:/i",
          "/Mime\-Version\:/i" 
      ); 
      
      if(preg_replace($headers, '', $string) == $string) {
    	  // would normally do mysql_real_escape here too but there's no MySQL connection :(
    	  return $string;
      } else {
          die("Spammy Spammy!");
      }
  }
  
}
?>