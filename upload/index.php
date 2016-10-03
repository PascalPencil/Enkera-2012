<?php
/**
 * Dropbox Uploader Main Index File
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
 * @version 1.0
 */
 
// Allow safe execution of PHP files in lib
define('_EXEC', 1);
// Start the session
session_start();
// Class Autoloader
function __autoload($class_name) {
    if($class_name=='DUConfig') {
        require 'lib/config.php';
    } else {
        require 'lib/'.strtolower($class_name).'/'.strtolower($class_name).'.class.php';
    }
}
// Start DropboxUpload Processor
$DropboxUploader = new Processor($_POST, $_FILES);
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <!--[if IE]><![endif]-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    
    <title><?php echo $DropboxUploader->PageTitle; ?></title>
    
    <meta name="description" content="Advanced Dropbox Uploader">
    <meta name="keywords" content="">
    <meta name="author" content="">
    
    <?php echo $DropboxUploader->FavIcon; ?>
    
    <?php echo $DropboxUploader->AppleTouchIcon; ?>
    
    <?php echo $DropboxUploader->ThemeStyle; ?>
    
    <script src="lib/js/modernizr-1.6.min.js"></script>
</head>

<body>
  <div id="container">
    <header>
    	
    </header>
    
    <div class="main">
        <div id="top">
            <?php echo $DropboxUploader->Logo; ?>
            <div class="right">
                <?php echo $DropboxUploader->Msg; ?>
            </div>
        </div>
        <div class="left">
            <?php echo $DropboxUploader->PageText; ?>
        </div>
        <div class="right">
            <form method="POST" enctype="multipart/form-data" id="UploadForm">
                <fieldset>
                    <h2>Browse for your file:<em> required</em></h2>
                    <div class="file_input_div">
                      <input type="button" value="Browse&nbsp;&nbsp;»" class="file_input_button" />
                      <input type="file" name="file" class="file_input_hidden" onchange="javascript: document.getElementById('fileName').value = this.value" />
                    </div>
                    <input type="text" id="fileName" class="file_input_textbox" readonly="readonly" />
                </fieldset>
                <?php echo $DropboxUploader->PasswordField; ?>
                <fieldset>
                    <h2>Tell us who you are:<em> required</em></h2>
                    <input id="name" name="name" size="30" type="text" class="input_textbox required" value="Your Name..." />
                    <input id="email" name="email" size="30" type="text" class="input_textbox required email" value="Your Email..." />
                </fieldset>
                <fieldset>
                    <h2>Write a message about your file:</h2>
                    <input id="subject" name="subject" size="30" type="text" class="input_textbox" value="Subject..." />
                    <textarea cols="28" id="message" name="message" class="input_textarea"></textarea>
                </fieldset>
                <fieldset class="buttons">
                  <input type="hidden" name="form_key" id="form_key" value="<?php echo $_SESSION['form_key']; ?>" />
                  <button id="upload_button" name="upload_button" type="submit" value="Upload Now&nbsp;&nbsp;»">Upload Now&nbsp;&nbsp;»</button>
                </fieldset>
            </form>
        </div>
        <div class="clearfix"></div>
    </div>
    
    <footer>
        <div class="main">
            <?php echo $DropboxUploader->FooterText; ?>
            <?php echo $DropboxUploader->SocialLinks; ?>
        </div>
    </footer>
  </div>
  <?php echo $DropboxUploader->PNGFix; ?>

  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
  <script>!window.jQuery && document.write('<script src="lib/js/jquery-1.4.2.min.js"><\/script>')</script>
  <script src="lib/js/plugins.js?v=1"></script>
  <script src="lib/js/site.js?v=1"></script>
</body>
</html>