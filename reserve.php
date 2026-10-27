<?php
// -------------------------------------------------------------
// RESERVE.PHP — Version propre, unique, workflow light + push
// -------------------------------------------------------------

header("Content-Type: application/json");

// Connexion MySQL
$pdo = new PDO("mysql:host=localhost;dbname=lavache;charset=utf8", "root", "");

// Récupération du JSON envoyé par fetch()
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "error" => "Aucune donnée reçue"]);
    exit;
}

// Insertion dans MySQL
$sql = "INSERT INTO reservations (hotel_nom, hotel_cat, hotel_chb, hotel_prix, adresse, tel, email, created_at)
        VALUES (:hotel_nom, :hotel_cat, :hotel_chb, :hotel_prix, :adresse, :tel, :email, NOW())";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":hotel_nom" => $data["hotel_nom"],
    ":hotel_cat" => $data["hotel_cat"],
    ":hotel_chb" => $data["hotel_chb"],
    ":hotel_prix" => $data["hotel_prix"],
    ":adresse" => $data["adresse"],
    ":tel" => $data["tel"],
    ":email" => $data["email"]
]);

// Notification push locale
$payload = [
    "title" => "Nouvelle offre publiée",
    "body" => $data["hotel_nom"] . " vient de publier une offre."
];

file_put_contents("push.json", json_encode($payload));

// Réponse JSON
echo json_encode(["success" => true]);
?>

<script>
async function sendReservation(data) {
    if (navigator.onLine) {
        // En ligne : on envoie directement au serveur
        const response = await fetch("save_reservation.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        });
        return response.ok;
    } else {
        // Offline : on stocke localement
        const queue = JSON.parse(localStorage.getItem("reservationQueue") || "[]");
        queue.push(data);
        localStorage.setItem("reservationQueue", JSON.stringify(queue));
        alert("Réservation enregistrée hors ligne. Elle sera synchronisée dès que le réseau revient.");
        return false;
    }
}

document.getElementById("reservationForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const data = {
        nom: document.getElementById("nom").value,
        email: document.getElementById("email").value,
        date_arrivee: document.getElementById("date_arrivee").value,
        date_depart: document.getElementById("date_depart").value,
        chambre: document.getElementById("chambre").value
    };

    const ok = await sendReservation(data);
    if (ok) {
        alert("Réservation enregistrée en ligne.");
    }
});
</script> 

<script>
async function syncReservations() {
    const queue = JSON.parse(localStorage.getItem("reservationQueue") || "[]");
    if (queue.length === 0) return;

    for (const data of queue) {
        try {
            const response = await fetch("save_reservation.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data)
            });
            if (!response.ok) throw new Error("Erreur serveur");
        } catch (e) {
            console.error("Sync échouée pour une réservation", e);
            return; // on arrête, on réessaiera plus tard
        }
    }

    // Si tout est OK, on vide la file
    localStorage.removeItem("reservationQueue");
    alert("Réservations hors ligne synchronisées avec le serveur.");
}

// Quand le réseau revient
window.addEventListener("online", syncReservations);

// Et au chargement de la page, au cas où
document.addEventListener("DOMContentLoaded", syncReservations);
</script>

