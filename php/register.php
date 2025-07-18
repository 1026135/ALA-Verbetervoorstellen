<?php

require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $naam = $_POST["naam"];
    $email = $_POST["email"];
    $wachtwoord = $_POST["wachtwoord"];
    $bevestig = $_POST["wachtwoord_bevestig"];
    $rol = "gebruiker";

    //  B02: Foutieve verwerking gezinslid (veld wordt niet opgeslagen, of zelfs crasht bij fout type)
    $gezinslid = $_POST["gezinslid"] ?? null;  // niet gevalideerd

    // Tim - The ($wachtwoord !== $bevestig) is wrong. It checks if both are diffrent when they should be the same.
    if ($wachtwoord == $bevestig) {
        die("Wachtwoorden komen niet overeen.");
    }

    //  B15: Dubbele e-mail zonder unieke controle → gebruikers overschrijven elkaar
    // (normaal: check of email al bestaat, dat doen we hier expres niet)

    //  B07: Activatiemail ontbreekt of mislukt zonder melding
    $hash = password_hash($wachtwoord, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (naam, email, wachtwoord, rol) VALUES (?, ?, ?, ?)");
        $stmt->execute([$naam, $email, $hash, $rol]);

        //  B09: Mail wordt niet verzonden door ontbrekende mail()-aanroep
        // mail($email, "Activatie BeemBrug", "Welkom!");  ← uitgeschakeld

        header("Location: ../login.html");
        exit;
    } catch (PDOException $e) {
        echo "Fout bij registratie: " . $e->getMessage();
    }
} else {
    die("Ongeldige toegang.");
}
?>