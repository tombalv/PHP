<?php
// // Generuojame pradinį laiką
// $hour = rand(0, 23);
// $minute = rand(0, 59);
// $second = rand(0, 59);
// $time = date("H:i:s", mktime($hour, $minute, $second));
// echo "Pradinis laikas: " . $time . "\n";

 

// // Generuojame atsitiktinį sekundžių skaičių
// $random_seconds = rand(0, 300);
// echo "Papildomai pridėtos sekundės: " . $random_seconds . "\n";

 

// // Pridedame sekundes prie pradinio laiko
// $new_time = date("H:i:s", strtotime("+".$random_seconds." seconds", strtotime($time)));
// echo "Laikas po sekundžių pridėjimo: " . $new_time . "\n";

 


// // Generuojame pradinį laiką ir pridėtas sekundes
// $time = date("H:i:s", mktime(rand(0, 23), rand(0, 59), rand(0, 59)));
// $random_seconds = rand(0, 300);

// echo "Pradinis laikas: $time\n";
// echo "Papildomai pridėtos sekundės: $random_seconds\n";
// echo "Laikas po sekundžių pridėjimo: " . date("H:i:s", strtotime("+$random_seconds seconds", strtotime($time))) . "\n";

// // Generuojame 6 atsitiktinius skaičius
// $num1 = rand(1000, 9999);
// $num2 = rand(1000, 9999);
// $num3 = rand(1000, 9999);
// $num4 = rand(1000, 9999);
// $num5 = rand(1000, 9999);
// $num6 = rand(1000, 9999);

// // Sukuriame strigą, kuriame skaičiai yra išdėstyti nuo didžiausio iki mažiausio
// $string = implode(" ", array($num1, $num2, $num3, $num4, $num5, $num6));
// $string = implode(" ", array_unique(explode(" ", $string)));
// rsort($string, SORT_NUMERIC);
// $string = implode(" ", $string);

// echo $string;


// // Generuojame pradinį laiką ir pridėtas sekundes
// $time = date("H:i:s", mktime(rand(0, 23), rand(0, 59), rand(0, 59)));
// $random_seconds = rand(0, 300);

// echo "Pradinis laikas: $time\n";
// echo "Papildomai pridėtos sekundės: $random_seconds\n";
// echo "Laikas po sekundžių pridėjimo: " . date("H:i:s", strtotime("+$random_seconds seconds", strtotime($time))) . "\n";

// // Generuojame 6 atsitiktinius skaičius
// $num1 = rand(1000, 9999);
// $num2 = rand(1000, 9999);
// $num3 = rand(1000, 9999);
// $num4 = rand(1000, 9999);
// $num5 = rand(1000, 9999);
// $num6 = rand(1000, 9999);

// echo "Atsitiktiniai skaičiai: $num1 $num2 $num3 $num4 $num5 $num6\n";



// $array = array();
// $length = 200;
// $letters = array('A', 'B', 'C', 'D');

// for ($i = 0; $i < $length; $i++) {
//     $randomIndex = array_rand($letters);
//     $array[] = $letters[$randomIndex];
// }

// $counts = array_count_values($array);

// foreach ($counts as $letter => $count) {
//     echo "Raidė $letter pasikartoja $count kartų.<br>";
// }
// 


$array1 = array();
while (count($array1) < 100) {
    $randomNumber = rand(100, 999);
    
    if (!in_array($randomNumber, $array1)) {
        $array1[] = $randomNumber;
    }
}
$array2 = array();
while (count($array2) < 100) {
    $randomNumber = rand(100, 999);
    
    if (!in_array($randomNumber, $array2) && !in_array($randomNumber, $array1)) {
        $array2[] = $randomNumber;
    }
}
echo "Pirmas masyvas:<br>";
print_r($array1);
echo "<br><br>Antras masyvas:<br>";
print_r($array2);
?>
