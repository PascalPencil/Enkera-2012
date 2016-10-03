<?php
// no direct access
defined('_EXEC') or die('Restricted access');

class DUConfig {
    
    // dropbox details
    var $LoginEmail = "youremail@yourdomain.com"; // your dropbox login email
    var $LoginPassword = "yourpassword"; // your dropbox login password
    var $LoginDestination = "thisfolder/thatfolder"; // destination in your dropbox root for the uploaded files. Directories will be automatically created if they don't already exist....awesome!
    var $Password = ""; // password protect your upload form to stop the upload hooligans! Leave blank for no password protection (default)
    
    // email
    var $ToEmail = "youremail@yourdomain.com"; // email address to send notifications to
    var $BCCEmail = ""; // bcc email address - can be string separated by comma, leave blank if not needed
    var $CCEmail = ""; // cc email address - can be string separated by comma, leave blank if not needed
    var $SubjectPrefix = "New Dropbox Upload: "; // a standard prefix for notification email subject (usually just "New Dropbox Upload: " )
    var $HtmlEmail = true; // true to send notification email as html or false for plaintext only
    
    // select your theme
    var $Theme = "modern_blue"; // type the name of the folder inside "themes" folder to use for your uploader CSS theme
    
    // logo settings
    var $Logo = "none"; // Accepted values:  none, image, text
    var $LogoText = "Your Company"; // use if logo (above) is set to text - this is the text that appears
    var $LogoPath = "images/logo_modernblue.png"; // use if logo (above) is set to image - this is the path to your logo, relative to the index file of this site
    var $LogoPNGFix = "no"; /* apply dd_belated IE6 PNG Fix to your logo if it has alpha channel transparency
                                Accepted values:  yes, no
                                Only use if if you use logo type: image  (above)
                                You only really need this if you're concerned about IE6 support and use PNG for logo */
    
    /* page elements
       These are mostly bits of text that appear on the uploader page.
       HTML tags ok but escape double quotes if needed
       Additional vars:  %%max_upload%%   (replaced with a calculated server maximum upload value - this is only an approximation but should give you a good idea what the limit is)
    */
    var $PageTitle = "Dropbox Uploader"; // change your upload sites page title here
    var $PageText = "<h1>Upload your assets here!</h1>
                     <p>Use the form to the right to upload files and assets related to your project with us.</p>
                     <p>The file uploader takes only one file at a time so if there are many files to send through, just archive up files into a single zip!
                     <h4>The maximum file size you can upload is: %%max_upload%%</h4>
                     <p>If you have any problems with the uploader then don't hesitate to contact us on the details below:</p>
                     <p>Phone: 0800 YOUR PHONE<br />
                        Email: <a href=\"mailto:youremail@youremail.com\">youremail@youremail.com</a>
                     </p>";
                     
    var $FooterText = "<p>Copyright © 2010 Donec quis ante eu nisl pretium vehicula id sit amet erat.
                       <br />Nulla quis tortor sit amet sapien dignissim mollis.</p>";
    var $TwitterLink = "http://www.twitter.com/yourtwittername";  // leave blank for no twitter link
    
    /* create your notification email message here.
       HTML tags ok but escape double quotes if needed
       Additional vars:  %%user_message%%   (replaced with user supplied message field)
                         %%destination%%    (replaced with location of uploaded file)
                         %%sender_name%%
    */
    var $Message = "<html>
                    <p>A new file has been uploaded to your dropbox from %%sender_name%%.</p>
                    <p><strong>Their Message was:</strong></p>
                    <p style='padding-left:20px;'><em>%%user_message%%</em></p>
                    <p><strong>Their file was uploaded to:</strong></p>
                    <p style='padding-left:20px;'><em>%%destination%%</em></p>
                    <p>Regards<br />
                    <strong>Dropbox Uploader</strong></p>
                    </html>";
               
    /* Plain Text message
       same additional vars as above. No HTML.
       user \r or \r\n for line/paragraph breaks
       left align this content so you don't get large indents in your email
    */
    var $MessagePt = "A new file has been uploaded to your dropbox from %%sender_name%%. \r\n
Their Message was: \r
%%user_message%% \r\n
Their file was uploaded to: \r
%%destination%% \r\n
Regards \r
Dropbox Uploader";

}
?>