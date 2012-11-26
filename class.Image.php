<?php
	/*
	 * Easy PHP image manager class
	 * @author Emilio Cobos (http://emiliocobos.net) <ecoal95@gmail.com>
	 * @version 1.0
	 */
	class Image {
		// The image filename
		public $filename;

		// The image GD resource
		public $image;

		// The image type (int from exif_imagetype)
		public $imagetype;

		// The dimensions of the image
		public $width;
		public $height;

		/*
		 * Create the image resource
		 */
		private function createImage() {
			switch ($this->imagetype) {
				case 1 :
					$image = imagecreatefromgif($this->filename);
					break;
				case 2 :
					$image = imagecreatefromjpeg($this->filename);
					break;
				case 3 :
					$image = imagecreatefrompng($this->filename);
					break;
				case 6 :
					$image = imagecreatefrombmp($this->filename);
					break;
			}
    		return $image;
		}

		/*
		 * The constructor
		 * @param string|gd $filename the file to be modified (filename or gd resource). GD resource is used for resizing. In case of GD resource we need to specify imagetype, width and height
		 * @param int $imagetype the exif_imagetype of the image
		 * @param int $width the width of the GD resource
		 * @param int $height the height of the GD resource
		 */
		public function __construct($filename, $imagetype = 0, $width = 0, $height = 0) {
			// If $filename isn't a string we assume its a GD resource
			if( ! is_string($filename)) {
				$this->image = $filename;
				$this->imagetype = $imagetype;
				$this->width = $width;
				$this->height = $height;
			} else {
				$allowedTypes = array(
					1,  // gif
					2,  // jpg
					3,  // png
					6   // bmp
				);

				$this->filename = $filename;
				$this->imagetype = exif_imagetype($filename);

				list($this->width, $this->height) = getimagesize($this->filename);

				if (!in_array($this->imagetype, $allowedTypes)) {
					return false;
				}
				$this->image = $this->createImage();
			}
			return $this;
		}

		/*
		 * The abbreviated constructor
		 * Allows us to do `Image::from('image.jpg');` instead of `new Image('image.jpg');`
		 */
		public function from($filename, $imagetype = null, $width = 0, $height = 0) {
			return new static($filename, $imagetype, $width, $height);
		}

		/*
		 * Resize the image to a width and height, specifying if you want to crop it
		 * @param int $final_width the with to resize to
		 * @param int $final_height the height to resize to
		 * @return Image
		 */
		public function resize_to($final_width, $final_height, $crop = false) {
			// imagen de destino
			$final_img = imagecreatetruecolor($final_width, $final_height);

			imagecopyresampled($final_img, $this->image, 0, 0, 0, 0, $final_width, $final_height, $crop ? $final_width : $this->width, $crop ? $final_height : $this->height);

			return Image::from($final_img, $this->imagetype, $final_width, $final_height);
		}

		/*
		 * Save the image to a file
		 * @param string $new_filename
		 */
		public function save($new_filename) {
			switch ($this->imagetype) {
				case 1:
					imagegif($this->image, $new_filename);
					break;
				case 2:
					imagejpeg($this->image, $new_filename);
					break;
				case 3:
					imagepng($this->image, $new_filename);
					break;
				case 6:
					imagewbmp($this->image, $new_filename);
					break;
			}
			$this->filename = $new_filename;

			return $this;
		}

		/*
		 * Echoes the image
		 * @param bool $set_header If you want me to set the Content-Type header for you
		 */
		public function output($set_header = true) {
			if( $set_header ) {
				$content_type = null;
				switch ($this->imagetype) {
					case 1:
						$content_type = 'gif';
						break;
					case 2:
						$content_type = 'jpeg';
						break;
					case 3:
						$content_type = 'png';
						break;
					case 6:
						$content_type = 'bmp';
						break;
				}
				header('Content-Type: image/' . $content_type);
			}
			return $this->save(null);
		}
	}