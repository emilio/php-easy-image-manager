# Easy PHP image manager

This script allows you to save, resize, crop and output your images.

## Usage
### Include the class
```
include 'class.Image.php';
```
### You're done!
Now you can use any of its methods

## Examples
### 1- Resize image.jpg to 400x300 (cropping it), and save it to image.400x300.jpg
```
Image::from('image.jpg')
	// width, height, crop
	->resize_to(400, 300, true)
	->save('image.400x300.jpg');
```

### 2- Resize image.png to the half of its width and height, and save it to image.half.png
```
$image = Image::from('image.png');

$image
	->resize_to($image->width / 2, $image->height / 2)
	->save('image.half.png');
```

### 3- Output a resized image without saving it
```
Image::from('image.jpg')->resize_to(100, 100, true)->output();
```
If you want to manually set the `Content-Type` header, just pass false as argument to $output:
```
header("Content-Type: image/jpeg");
Image::from('imagen.jpg')->output(false);
```

## About the author
The author of this little script is [Emilio Cobos](http://emiliocobos.net), a 17 year old web designer and web developer from Salamanca (Spain).