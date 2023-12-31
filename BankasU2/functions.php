<?php
// Funkcija, kuri nuskaito duomenis iš JSON failo ir gražina masyvą
function readDataFromJson()
{
    $file = '/Applications/XAMPP/xamppfiles/htdocs/PHP/BankasU2/data/accounts.json';
    if (!file_exists($file)) {
        file_put_contents($file, json_encode(['accounts' => []]));
    }
    $data = file_get_contents($file);
    return json_decode($data, true);
}

function saveDataToJson($data)
{
    $file = '/Applications/XAMPP/xamppfiles/htdocs/PHP/BankasU2/data/accounts.json';
  // Konvertuojame masyvą į JSON formatą
  $jsonData = json_encode($data, JSON_PRETTY_PRINT);
  // Įrašome JSON duomenis į failą
  $result = file_put_contents($file, $jsonData);

  
}
// Funkcija, kuri sugeneruoja unikalų sąskaitos numerį (IBAN formatu)
// Funkcija, kuri sugeneruoja unikalų sąskaitos numerį (IBAN formatu)
function generateAccountNumber()
{
    $data = readDataFromJson();
    $usedAccountNumbers = [];
    foreach ($data['accounts'] as $account) {
        $usedAccountNumbers[] = $account['account_number'];
    }

    $prefix = 'LT21';
    do {
        $accountNumber = $prefix . str_pad(mt_rand(0, 999999999), 10, '0', STR_PAD_LEFT);
    } while (in_array($accountNumber, $usedAccountNumbers));

    return $accountNumber;
}