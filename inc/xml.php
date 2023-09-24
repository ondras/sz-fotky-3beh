<?php
	class XML {
		private $xml;
		private $root = null;
		private $xsl = null;
		private $language = null;
		
		function __construct($documentElement, $attrs = null) {
			$this->xml = new DOMDocument();
			$this->xml->formatOutput = true;
			$this->xml->preserveWhiteSpace = false;
			$this->xml->encoding = 'utf-8';
			
			$this->root = $this->xml->createElement($documentElement);
			if ($attrs) {
				foreach ($attrs as $name=>$value) {
					$this->root->setAttribute($name, $value);
				}
			}
		}
		
		public function setXSLT($file) {
			$this->xsl = "xsl/".$file.".xsl";
		}
		
		public function setLanguage($language) {
			$this->language = $language;
		}

		public function setTitle($title) {
			$this->root->setAttribute("title", $title);
		}

		public function addElement($name, $attrs = false, $parent = null, $text = null) {
			if (!$parent) { $parent = $this->root; }
			$elm = $this->xml->createElement($name);
			$parent->appendChild($elm);
			if ($attrs) {
				foreach ($attrs as $key => $value) {
					$elm->setAttribute($key, is_null($value) ? "" : $value);
				}
			}
			if ($text) { $elm->appendChild($this->xml->createTextNode($text)); }
			return $elm;
		}
		
		public function importElement($doc) {
			$node = $this->xml->importNode($doc->documentElement, true);
			$this->root->appendChild($node);
		}
		
		public function cdata($element, $text) {
			$element->appendChild($this->xml->createCDATASection($text));
		}
		
		public function text($element, $text) {
			$element->appendChild($this->xml->createTextNode($text));
		}

		public function setError($error) {
			$this->root->setAttribute("error", $error);
		}

		public function output($manual) {
			$this->applyPOST();

			if (!$manual || !$this->xsl) {
				header("Content-type: text/xml");
			}
			
			if (!$manual && $this->xsl) {
				$xsl = $this->xml->createProcessingInstruction("xml-stylesheet", "type='text/xsl' href='/".$this->xsl."'");
				$this->xml->appendChild($xsl);
			}

			$this->xml->appendChild($this->root);
			
			if ($manual && $this->xsl) {
				$xslt = new XSLTProcessor();
				$xsl = new DOMDocument();
				$xsl->load($this->xsl, LIBXML_NOCDATA);
				$xslt->importStylesheet($xsl);
				
				if ($this->language) { $xslt->setParameter("", "language", $this->language); }
				echo $xslt->transformToXML($this->xml); 
			} else {
				echo $this->xml->saveXML();
			}
		}
		
		private function applyPOST() {
			$blacklist = array("pass1", "pass2", "gpx");
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$post = $this->xml->createElement("post");
				foreach ($_POST as $key=>$value) {
					if (in_array($key, $blacklist)) { continue; }
					$post->setAttribute($key, $value);
				}
				$this->root->appendChild($post);
			}
		}
	}
?>
