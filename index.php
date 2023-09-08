<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vanilla PHP - SQL || Dawson Maldonado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css'>
    <link href="css.css" rel="stylesheet">
  </head>
<body>
<?php
require_once ("db_conn.php"); 
/* ****************************************************************** */
// FETCH_NUM = QUERY Category
$stmt = $conn->prepare("SELECT * FROM ps_category limit 2");
$stmt->setFetchMode(PDO::FETCH_NUM);
$stmt->execute();

/* ****************************************************************** */
// FETCH_NUM = QUERY Product-Category
$stmt1 = $conn->prepare("SELECT cp.id_category, cl.name, cc.id_parent, cc.active, count( distinct pp.id_product ), pp.active
FROM ps_category_product cp
LEFT JOIN ps_category cc ON cc.id_category = cp.id_category
LEFT JOIN ps_category_lang cl ON cc.id_category = cl.id_category
LEFT JOIN ps_product pp on ( pp.id_category_default = cp.id_category)
WHERE 
        cp.id_category IS not NULL 
    AND cl.id_lang = 1
    AND cp.id_category > 0
    and cc.active = 1
    and cc.id_parent <3
    and pp.active = 1
group by cp.id_category
order by cc.id_parent, cl.name, cp.id_category");
$stmt1->setFetchMode(PDO::FETCH_NUM);
$stmt1->execute();

/* ****************************************************************** */
// Show QUERY Data
echo "<section class='container'>
      <h2>   Categories</h2>";
echo "<div class='accordion' id='accordionExample'>";      

    while ($row1 = $stmt1->fetch()){
      echo "<div class='accordion-item'>
            <h5 class='accordion-header '>
        <button class='accordion-button bg-light text-dark collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#collapse$i' aria-expanded='false' aria-controls='collapse$i'>
                      <b> $row1[1] </b> - ( $row1[4] Products )
        </button>
      </h5>
      <div id='collapse$i' class='accordion-collapse collapse' data-bs-parent='#accordionExample'>
        <div class='accordion-body'>
        <h5> <b>{$row1[1]}</b> -> Products: </h5>";

/* ****************************************************************** */
// FETCH_NUM = QUERY Quantity-Product-Category
$stmt2 = $conn->prepare("SELECT pp.id_product, count(pf.id_product) as q , pl.name, ps.quantity, id_manufacturer, id_category_default, pp.quantity, pp.price, pp.active, pp.state 
FROM ps_product pp
left join ps_stock_available ps on (pp.id_product = ps.id_product)
left join ps_feature_product pf on (pp.id_product = pf.id_product) 
left join ps_product_lang pl on (pp.id_product = pl.id_product)
/******** pp.id_category_default   *********/
where pp.id_category_default= $row1[0]
  and pp.active=1
  and pp.state=1
  and ps.quantity>-1  
group by pp.id_product  
order by pp.id_product");
$stmt2->setFetchMode(PDO::FETCH_NUM);
$stmt2->execute();
$k=0;

/* ****************************************************************** */
// Show QUERY Data
echo "<div class='accordion ' id='accordionExample$i-$j'>";
while ($row2 = $stmt2->fetch()){ 
  $k++;
echo "<div class='accordion-item'>
      <h3 class='accordion-header' id='sub-heading$i-$j'>
      <button class='accordion-button bg-light text-dark collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#sub-collapse$i-$j' aria-expanded='' aria-controls='collapse$i-$j'>
      <div >$k - <b>$row2[2]</b> - ($row2[1] Features)</div>
      </button>
      </h3>
<div id='sub-collapse$i-$j' class='accordion-collapse collapse' aria-labelledby='sub-heading$i-$j' data-bs-parent='#sub-accordionExample'>
<div class='accordion-body'>";
     echo "<h5> <b>{$row2[2]}</b> -> Features:</h5>";

  /* ****************************************************************** */
// FETCH_NUM = QUERY Features-Product 
$stmt3 = $conn->prepare("SELECT pp.id_product, fl.id_feature, fl.name, vl.value,  pp.active, pp.state 
FROM ps_product pp
left join ps_feature_product pf on (pp.id_product = pf.id_product) 
left join ps_feature_lang fl on (pf.id_feature = fl.id_feature) 
left join ps_feature_value_lang vl on (pf.id_feature_value = vl.id_feature_value )
where pp.id_product=$row2[0]
  and pp.active=1
  and pp.state=1  
order by fl.name, pp.id_product");
$stmt3->setFetchMode(PDO::FETCH_NUM);
$stmt3->execute();

/* ****************************************************************** */
unset($af,$ad,$feat);
$feat='';
$af=$ad=array();

while ($row3 = $stmt3->fetch()){
// MAKE ARRAY FEATURES
// CONCAT Featues similar
if(array_key_exists($row3[1], $af)){
  $af[$row3[1]]=$af[$row3[1]].", ".$row3[3];  
} else $af[$row3[1]]=$row3[3];

$ad[$row3[1]]=$row3[2];

if(!empty($feat))$feat= $feat.", ".$row3[3];
 else $feat= $row3[3];

}
/* ****************************************************************** */
// Show QUERY Data
echo "<div class='bg-white text-secondary'> ";
if($row2[1]>0) {
foreach($af as $key => $value){
  echo " - <u>".$ad[$key]."</u> : ".$value."<br>";
}

/* ****************************************************************** */
// Show NOTE 
echo "<br><br><sub>==> <u>$row2[1] Features</u>: ".$feat." <==</sub> ";
echo "<br><sub><u>Nota:</u> Tomar en cuenta que en ocasiones la característica tiene varios valores para una misma opción, por lo tanto se concatenan.  Ej. <b>Color</b>: Black, White, Brown.. </sub>";
} else echo "<center><h3> *** Not features *** </h3></center>";

echo "</div>";  /******************************************************/
        echo "</div>
      </div>
    </div>";
    $j++;
      } 
      echo "</div>
      </div>
    </div></div>";
    $i++;
  }  // ************************     WHILE Category  *************************
  echo "</div>";   // DIV MAIN
echo "</section>";  ?>
<br><br>
 <script src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.min.js'></script>
</body>
</html>