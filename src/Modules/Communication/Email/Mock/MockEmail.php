<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 6/8/18
 * Time: 12:35 PM
 */

namespace Sm\Modules\Communication\Email\Mock;


use Sm\Modules\Communication\Email\Email;

class MockEmail extends Email {
	public function send(array $to): Email {
		echo "<pre>TO:\n\t" . $this->toEmailStr(...$to) . '</pre><hr>';
		echo "<pre>Having Content:\n\t</pre>--></br>".$this->content.'</br><--<hr>';
		echo "<pre>In Plaintext:\n\t</pre>-->".$this->plaintext_content.'<--<hr>';

		return $this;
	}

	public function initialize(array $from, array $reply_to = null): Email {
		echo "<pre>FROM:\n\t" . $this->toEmailStr($from) . '</pre><hr>';
		echo "<pre>REPLY-TO:\n\t" . $this->toEmailStr($reply_to) . '</pre><hr>';
		return $this;
	}

	/**
	 * @param array $to
	 * @return string
	 */
	protected function toEmailStr(...$to): string {
		$to_arr = [];
		foreach ($to as $person_record_arr) {
			$str = '';
			if ($person_record_arr[1] ?? false) {
				$str .= $person_record_arr[1] . ' ';
			}
			$str      .= '<' . $person_record_arr[0] . '>';
			$to_arr[] = $str;
		}
		$impl = implode($to_arr, ', ');
		return htmlentities($impl);
	}
}