<?php
  $dir_path = dirname(__DIR__);
  $ignore_files = ['.', '..', 'editor'];
  
  if(isset($_GET['file'])) {

    $file = $_GET['file'];
    $file_path = "../".$file;
    
    // create
    if(!file_exists($file_path)) {
      $writeFile = fopen($file_path, 'w') or die("Unable to open file!");
      fclose($writeFile); 
    }

    // save
    if(isset($_POST['file_data'])) {
      $writeFile = fopen($file_path, 'w') or die("Unable to open file!");
      fwrite($writeFile, $_POST['file_data']);
      fclose($writeFile); 
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css">
    <title>editor</title>
  </head>
  <body>
    <div class="explorer">
      <?php
        $files = array_filter(scandir($dir_path), function($val) {
          global $ignore_files;
          return !in_array($val, $ignore_files);
        });
      ?>
      <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <input type="text" name="file">
        <button type="submit">add</button>
      </form>
      <ul>
        <?php foreach($files as $file): ?>
          <li>
            <a href="index.php?file=<?php echo $file ?>">
              <?php echo $file ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php if(isset($_GET['file'])): ?>
      <div class="editor">
        <div class="tabs">
          <div class="tab">
            <?php echo $_GET['file']; ?>
          </div>
        </div>
        <form class="textarea" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?file=".$_GET['file']; ?>" method="post">
          <textarea name="file_data"><?php
              $file = $_GET['file'];
              $file_path = "../".$file;
    
              if(filesize($file_path) > 0) {
                $readFile = fopen($file_path, 'r') or die("Unable to open file!");
                echo htmlspecialchars(fread($readFile, filesize($file_path)));
                fclose($readFile); 
              }
            ?></textarea>
            <script type="text/javascript">
              var element = document.querySelector("textarea");
              element.focus();
              element.selectionStart = element.value.length;

              element.addEventListener('keydown', function(e) {
                if (e.key == 'Tab') {
                  e.preventDefault();
                  var start = this.selectionStart;
                  var end = this.selectionEnd;

                  // set textarea value to: text before caret + tab + text after caret
                  this.value = this.value.substring(0, start) +
                    "  " + this.value.substring(end);

                  // put caret at right position again
                  this.selectionStart =
                    this.selectionEnd = start + 2;
                }
              });

              // submit form
              window.addEventListener("keydown", (e) => {
                if(e.ctrlKey && e.key === "s") {
                  e.preventDefault()
                  document.querySelector(".textarea").submit();
                }
              })
            </script>
        </form>
      </div>
    <?php endif; ?>
  </body>
</html>