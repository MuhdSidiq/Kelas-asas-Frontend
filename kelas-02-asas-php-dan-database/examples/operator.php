<?php
// If-Else Example
// $income = 60000;

// if ($income > 60000) {
// 	echo "Melebihi had $income tahun. Layak dicukai.";
// } else {
// 	echo "$income tidak melebihi had tahun. Tidak layak dicukai.";
// }

// echo "<br>";

// Switch Example

function TaxCalculatorCategory($TaxableIncome) {

    $PembayarkelasA = 60000;
    $PembayarkelasB = 120000;
    $PembayarkelasC = 180000;

    $kadarCukaiKelasA = 0.06;
    $kadarCukaiKelasB = 0.12;
    $kadarCukaiKelasC = 0.18;

    if ($TaxableIncome <= $PembayarkelasA) {
        return "Kadar cukai kelas A adalah " . ($kadarCukaiKelasA * 100) . "%";
    } elseif ($TaxableIncome <= $PembayarkelasB) {
        return "Kadar cukai kelas B adalah " . ($kadarCukaiKelasB * 100) . "%";
    } elseif ($TaxableIncome <= $PembayarkelasC) {
        return "Kadar cukai kelas C adalah " . ($kadarCukaiKelasC * 100) . "%";
    } else {
        return "Kadar cukai tidak dikenali.";
    }
}

$TaxableIncome = 160000;

echo TaxCalculatorCategory($TaxableIncome);

// echo "<br>";

// // Loop Example (For)
// for ($i = 1; $i <= 5; $i++) {
// 	echo "Number: $i <br>";
// }

// // Loop Example (While)
// $count = 1;
// while ($count <= 3) {
// 	echo "Count: $count <br>";
// 	$count++;
// }

// // Loop Example (Foreach)
// $colors = array("Red", "Green", "Blue");
// foreach ($colors as $color) {
// 	echo "Color: $color <br>";
// }
// ?>
