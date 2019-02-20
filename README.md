# imageResizer
Use this Class to simply resize and save images. Best usage is to scale and save images that are submited via a form.

## Installation
It's just a simple class. Include it wherever you need it.

## Usage
Really simple, just call the Function `resizeImage` and pass the parameters, `source`, `output`, `n_width`, and `n_height` (new width, and new height).
If you set `force_proportions` to true, the image will not scale proportionaly but be forced to the width and height given.
```php
  $ir = new coreImageResizer();
  if($ir->resizeImage($_FILES['image']['tmp_name'][0], '/path/to/save/file.jpg' 300, 500, false, 'jpg', false)){
    echo "Yay!";
  }else{
    echo "Something went wrong";
  }
```
