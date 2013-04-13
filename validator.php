<?php
	class Validator {
		protected $_file_contents;

		protected $_error_message;

		protected $_accepted_request_methods = array('GET', 'POST');

		protected $_accepted_header_names    = array('Accept', 'Host', 'Referer');

		function __construct($file) {
			try {
				$this->_file_contents = file($file);
			} catch (Exception $e) {
				$this->_error_message = $e->getMessage();
			}
		}

		protected function _is_invalid($msg) {
			$this->_error_message = $msg;

			return FALSE;
		}

		protected function _stringify_array($ary) {
			return '"'.implode('", "', $ary).'"';
		}

		function execute() {
			if ($this->_error_message)
				return FALSE;

			preg_match('/(\S+)\s+(\S+)\s+(\S+)/', array_shift($this->_file_contents), $request_array);

			if (count($request_array) < 4)
				return $this->_is_invalid('Incomplete or malformed request.');

			$request_method = $request_array[1];

			if ( ! in_array($request_method, $this->_accepted_request_methods))
				return $this->_is_invalid("The request method received was \"$request_method\", but must be one of the following: ".$this->_stringify_array($this->_accepted_request_methods).'.');

			if (substr($request_array[2], 0, 1) != '/')
				return $this->_is_invalid('The request path must be relative.');

			if ($request_array[3] != 'HTTP/1.1')
				return $this->_is_invalid('The request version must be HTTP/1.1.');

			$at_body = FALSE;

			foreach ($this->_file_contents as $i => $line) {
				$clean_line = trim($line);

				if (empty($clean_line))
					break;

				preg_match('/([^:]+):(.+)/', $line, $header_array);

				if (count($header_array) < 3)
					return $this->_is_invalid('Incomplete or malformed header on line '.($i + 1));

				$header_name = trim($request_array[1]);

				if ( ! in_array($header_name, $this->_accepted_header_names))
					return $this->_is_invalid('The header name received was "'.$header_name.'", but must be one of the following: '.$this->_stringify_array($this->_accepted_header_names).'.');
			}

			return TRUE;
		}

		public function get_error_message() {
			return $this->_error_message;
		}
	}