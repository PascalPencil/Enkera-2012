<?php
// no direct access
defined('_EXEC') or die('Restricted access');

/**
 * Simple PHP Email Class
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

class Email {

  // set defaults
  public $EmailNewLine = "\r\n";
  public $EmailXMailer = "email.class.php,v1";
  public $DefaultCharset = "iso-8859-1";
  public $To = null;
  public $Cc = null;
  public $Bcc = null;
  public $From = null;
  public $Subject = null;
  public $Content = null;
  public $Attachments;
  public $Headers = null;
  public $TextOnly = true;
  public $Charset = null;
  
  /**
   * Constructor
   */
  public function __construct() {
      // set attachment defaults here
      $this->Attachments = array();
      $this->Attachments["text"] = null;
      $this->Attachments["html"] = null;
  }
  
  // send html & plaintext email
  public function SetMultipartAlternative($text = null, $html = null) {
      if (strlen(trim(strval($html))) == 0 || strlen(trim(strval($text))) == 0) {
          return false;
      } else {
          $this->Attachments["text"] = new EmailAttachment(null, "text/plain");
          $this->Attachments["text"]->LiteralContent = strval($text);
          $this->Attachments["html"] = new EmailAttachment(null, "text/html");
          $this->Attachments["html"]->LiteralContent = strval($html);
          return true;
      }
  }
  
  // add attachments
  public function Attach($pathtofile, $mimetype = null) {
      $attachment = new EmailAttachment($pathtofile, $mimetype);
      if (!$attachment->Exists())
          return false;
      else {
          $this->Attachments[] = $attachment;
          return true;
      }
  }
  
  // check we have a valid to and from
  // TODO: add checking for properly formed email addresses
  public function IsComplete() {
      return(strlen(trim($this->To)) > 0 && strlen(trim($this->From)) > 0);
  }
  
  // build headers and send the email
  public function Send() {
      if (!$this->IsComplete())
          return false;
      $theboundary = "-----" . md5(uniqid("EMAIL"));
      $headers = "Date: " . date("r", time()) .$this->EmailNewLine . "From: $this->From" .$this->EmailNewLine;
      if (strlen(trim(strval($this->Cc))) > 0)
          $headers .= "CC: $this->Cc" . $this->EmailNewLine;
      if (strlen(trim(strval($this->Bcc))) > 0)
          $headers .= "BCC: $this->Bcc" . $this->EmailNewLine;
      if ($this->Headers != null && strlen(trim($this->Headers)) > 0)
          $headers .= $this->Headers . $this->EmailNewLine;
      $isMultipartAlternative = ($this->Attachments["text"] != null && $this->Attachments["html"] != null);
      $baseContentType = "multipart/" . ($isMultipartAlternative ? "alternative" : "mixed");
      $headers .= "X-Mailer: " . $this->EmailXMailer . $this->EmailNewLine . "MIME-Version: 1.0" . $this->EmailNewLine . "Content-Type: $baseContentType; " . "boundary=\"$theboundary\"" . $this->EmailNewLine . $this->EmailNewLine;
      if ($isMultipartAlternative) {
          $thebody = "--$theboundary" . $this->EmailNewLine . $this->Attachments["text"]->ToHeader() . $this->EmailNewLine . "--$theboundary" . $this->EmailNewLine . $this->Attachments["html"]->ToHeader() . $this->EmailNewLine;
      } else {
          $theemailtype = "text/" . ($this->TextOnly ? "plain" : "html");
          if ($this->Charset == null)
              $this->Charset = $this->DefaultCharset;
          $thebody = "--$theboundary" . $this->EmailNewLine . "Content-Type: $theemailtype; charset=$this->Charset" . $this->EmailNewLine . "Content-Transfer-Encoding: 8bit" . $this->EmailNewLine . $this->EmailNewLine . $this->Content . $this->EmailNewLine . $this->EmailNewLine;
          foreach ($this->Attachments as $attachment) {
              if ($attachment != null) {
                  $thebody .= "--$theboundary" . $this->EmailNewLine . $attachment->ToHeader() . $this->EmailNewLine;
              }
          }
      }
      $thebody .= "--$theboundary--";
      return mail($this->To, $this->Subject, $thebody, $headers);
  }
}

// additional class to handle email attachments and multipart emails (HTML & Plaintext)
class EmailAttachment {

  // set defaults
  public $EmailNewLine = "\r\n";
  public $FilePath = null;
  public $ContentType = null;
  public $LiteralContent = null;
  
  /**
   * Constructor
   *
   * @param string|null $pathtofile
   * @param string|null $mimetype
   */
  public function __construct($pathtofile = null, $mimetype = null) {
      if ($mimetype == null || strlen(trim($mimetype)) == 0)
          $this->ContentType = "application/octet-stream";
      else
          $this->ContentType = $mimetype;
      
      $this->FilePath = $pathtofile;
  }
  
  // are we using this class to add our text/html content or some actual files?
  public function HasLiteralContent() {
      return(strlen(strval($this->LiteralContent)) > 0);
  }
  
  // get the content of our text/html or file
  public function GetContent() {
      if ($this->HasLiteralContent())
          return $this->LiteralContent;
      else {
          if (!$this->Exists())
              
              
              return null;
          else {
              $thefile = fopen($this->FilePath, "rb");
              $data = fread($thefile, filesize($this->FilePath));
              fclose($thefile);
              return $data;
          }
      }
  }
  
  // check file exists
  public function Exists() {
      if ($this->FilePath == null || strlen(trim($this->FilePath)) == 0)
          return false;
      else
          return file_exists($this->FilePath);
  }
  
  // add new mail headers
  public function ToHeader() {
      $attachmentData = $this->GetContent();
      if ($attachmentData == null)
          return null;
      $header = "Content-Type: $this->ContentType;";
      if (!$this->HasLiteralContent()) {
          $header .= " name=\"" . basename($this->FilePath) . "\"" . $this->EmailNewLine . "Content-Disposition: attachment; filename=\"" . basename($this->FilePath) . "\"";
      }
      $header .= $this->EmailNewLine;
      $header .= "Content-Transfer-Encoding: base64" . $this->EmailNewLine . $this->EmailNewLine;
      $header .= chunk_split(base64_encode($attachmentData), 76, $this->EmailNewLine) . $this->EmailNewLine;
      return $header;
  }
}
?>