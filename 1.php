<html>

<head>
   <title>Result</title>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>

<body>
   <?php
   $id = $_POST['id'];
   $email = $_POST['email'];
   $tel = $_POST['tel'];
   $data = $_POST['data'];

   $dbhost = "localhost";
   $dbname = "DB_info";
   $username = "root";
   $password = "";
   ?>

   <?php echo "ID:      $id<br><br>"; ?>
   <?php echo "E-mail:  $email<br><br>"; ?>
   <?php echo "Дата:    $data<br><br>"; ?>
   <?php echo "Телефон: $tel<br><br><br><br>"; ?>

   <?php
   if (!preg_match("/^([a-z0-9_\.-]+)@([a-z0-9_\.-]+)\.([a-z\.]{2,6})$/ui", $email)) {
      echo "!!!Невірний Email!!!<br><br>";
      echo "<p><a 
href=\"form.html\">";
      echo "!!!Повернутись до форми!!!</a></p>";
   } else if (!preg_match("/(\d{4})-(\d{2})-(\d{2})|(\d{2})-(\d{2})-(\d{4})/", $data)) {
      echo "!!!Невірна дата!!!<br><br>";
      echo "<p><a 
href=\"form.html\">";
      echo "!!!Повернутись до форми!!!</a></p>";
   } else if (!preg_match("/^\+380([0-9]{9})$/", $tel)) {
      echo "!!!Невірний телефон!!!<br><br>";
      echo "<p><a 
href=\"form.html\">";
      echo "!!!Повернутись до форми!!!</a></p>";
   } else if (!preg_match("/[a-zA-Z0-9]+/", $id)) {
      echo "!!!Невірний ID!!!<br><br>";
      echo "<p><a 
href=\"form.html\">";
      echo "!!!Повернутись до форми!!!</a></p>";
   } else {
      echo "!!!Всі данні введені вірно!!!<br><br>";

      try {
         $db = new PDO("mysql:host=$dbhost; dbname=$dbname", $username, $password);
         // set the PDO error mode to exception
         $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         $sql = "INSERT INTO `information`(`id`, `email`, `tel`, `data`) 
  VALUES ('$id','$email','$tel','$data')";

         // use exec() because no results are returned
         $db->exec($sql);
         echo "Інформація з форми передана в БД<br><br><br>";
      } catch (PDOException $e) {
         echo $sql . "<br>" . $e->getMessage();
      }

      $db = null;
   }
   ?>

   <?php
   echo "<br><br>Дані з БД:";
   echo "<table style='border: solid 1px black;'>";
   echo "<tr><th>Id</th><th>E-mail</th><th>Телефон</th><th>Дата</th></tr>";

   class TableRows extends RecursiveIteratorIterator
   {
      function __construct($it)
      {
         parent::__construct($it, self::LEAVES_ONLY);
      }

      function cur_rent()
      {
         return "<td style='width:150px;border:1px solid black;'>" . parent::current() . "</td>";
      }
      function begin_Children()
      {
         echo "<tr>";
      }

      function end_Children()
      {
         echo "</tr>" . "\n";
      }
   }


   try {
      $conn = new PDO("mysql:host=$dbhost; dbname=$dbname", $username, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $stmt = $conn->prepare("SELECT id, email, tel, data FROM information");
      $stmt->execute();

      // set the resulting array to associative
      $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
      foreach (new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k => $v) {
         echo $v;
      }
   } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
   }
   $conn = null;
   echo "</table><br><br><br>";
   ?>

   <?php
   echo "<br><br>Дати з БД в форматі мм.дд.рррр:";

   try {
      $conn = new PDO("mysql:host=$dbhost; dbname=$dbname", $username, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $stmt = $conn->prepare("SELECT data FROM information");
      $stmt->execute();

      // set the resulting array to associative
      $result = $stmt->setFetchMode(PDO::FETCH_COLUMN, 0);
      //Функція перестановка частин дати
      function DateEdit($Date)
      {
         $date_explode = explode("-", $Date);
         if ($date_explode[0] > 31) {
            $day = $date_explode[2];
            $year = $date_explode[0];
         } else {
            $day = $date_explode[0];
            $year = $date_explode[2];
         }
         $result_date = $date_explode[1] . '.' . $day . '.' . $year;
         return $result_date;
      }
      //Перестановка частин дати
      foreach ($stmt->fetchAll() as $k => $v) {
         $v = DateEdit($v);
         echo "<br>$v";
      }
   } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
   }
   $conn = null;
   echo "<br><br><br>";
   ?>
</body>

</html>