<?php
// no direct access
defined('_EXEC') or die('Restricted access');

/**
 * Simple PHP Form Key Class to protect your forms!
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

class formKey
{
	private $formKey;
	private $old_formKey;
	
	/**
	 * Constructor
	 */
	function __construct() {
		//We need the previous key so we store it
		if(isset($_SESSION['form_key'])) {
			$this->old_formKey = $_SESSION['form_key'];
		}
	}
	
	// Generate the form key
	private function generateKey() {
		// Get the IP-address of the user
		$ip = $_SERVER['REMOTE_ADDR'];
		
		// We use mt_rand() instead of rand() because it is better for generating random numbers.
		// We use 'true' to get a longer string.
		// See http://www.php.net/mt_rand for a precise description of the function and more examples.
		$uniqid = uniqid(mt_rand(), true);
		
		// Return the hash
		return md5($ip . $uniqid);
	}
	
	// Output the form key
	public function outputKey() {
		// Generate the key and store it inside the class
		$this->formKey = $this->generateKey();
		// Store the form key in the session
		$_SESSION['form_key'] = $this->formKey;
		
		// Output the form key
		echo "<input type='hidden' name='form_key' id='form_key' value='".$this->formKey."' />";
	}
	
	// Validated the form key POST data
	public function validate() {
		// We use the old formKey and not the new generated version
		if($_POST['form_key'] == $this->old_formKey) {
			return true;
		} else {
			return false;
		}
	}
}
?>