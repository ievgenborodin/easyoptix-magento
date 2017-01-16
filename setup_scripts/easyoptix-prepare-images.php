<?php
/*
    v.0.0.2
*/

// no time limit
set_time_limit(0);

$base_path = __DIR__;
$base_folder = "db_photos";

// entering base folder
$curr_path = $base_path . "/" . $base_folder;
if (chdir($curr_path))
{
  exec("ls", $files_list, $result);
  foreach ($files_list as $f) 
  {
    $old_file_name = $curr_path . '/' . $f;
    $new_f = strtolower($f);
    rename($old_file_name, $curr_path . '/' . $new_f);
    find_images($curr_path, $new_f);
  }
}
else 
{
  echo "couldn't find base folder\n";
}


function find_images($path, $file)
{
  // new curr_path
  $curr_path = trim($path . "/" . $file);

  // check if folder
  if (chdir($curr_path))
  {
    // check if there are images
    exec("ls *.JPG", $images_list, $return); 
    
    if (!$images_list)
    {
      exec("ls *.jpg", $images_list, $return); 
    }

    if ($images_list)
    { 
      // small_image folder
      $small_image_dir = $curr_path . "/small_image";
      mkdir($small_image_dir, 0777);

      // thumbnail folder
      $thumbnail_dir = $curr_path . "/thumbnail";
      mkdir($thumbnail_dir, 0777);

      // init diff root array
      $diff_arr = array();
      foreach ($images_list as $img) 
      {
        // change images names to lower case
        $old_image_name = trim($curr_path . '/' . $img);
        $new_img = strtolower($img);
        rename($old_image_name, $curr_path . '/' . $new_img);

        // get root
        $diff_root = substr($new_img, 0, -6);
        // check if root unique
        if (!in_array($diff_root, $diff_arr))
        {
          // add unique root
          $diff_arr[] = $diff_root;
          // cut number + add back .jpg
          $tmp_name = $diff_root . ".jpg";

          // copy small image and thumbnail
          copy($curr_path . "/" . $new_img, $small_image_dir . "/" . $tmp_name);
          copy($curr_path . "/" . $new_img, $thumbnail_dir . "/" . $tmp_name);
        }
      }

      // resizing 
      shell_exec("sips -Z 408 $small_image_dir/*.jpg");
      shell_exec("sips -Z 140 $thumbnail_dir/*.jpg");
      shell_exec("mogrify -resize 1200x1200 -gravity center -extent 1200x1200 *.jpg");

      //shell_exec("mogrify -resize 408x408 -gravity center -extent 408x408 $small_image_dir/*.jpg");
      //shell_exec("sips -Z 1200 *.jpg");
    }
    else 
    { 
      exec("ls", $files_list, $return);
      // if any files
      if (count($files_list))
      { 
        foreach ($files_list as $f) 
        {
          // change folder name to lower case
          $old_file_name = $curr_path . '/' . $f;
          $new_f = strtolower($f);
          rename($old_file_name, $curr_path . '/' . $new_f);

          // ignore etc folder
          if ($f !== '_etc')
          {
            find_images($curr_path, $new_f);  
          }
        }
      }
      
    }
  } 
}

?>