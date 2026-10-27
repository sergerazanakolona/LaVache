<?php
// Connexion MySQL
$pdo = new PDO("mysql:host=localhost;dbname=lavache;charset=utf8", "root", "");

// Récupération de l'ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("ID invalide");
}

// Lecture de l'offre
$stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = :id");
$stmt->execute([":id" => $id]);
$offer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$offer) {
    die("Offre introuvable");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Offre — <?php echo htmlspecialchars($offer['hotel_nom']); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body { font-family: system-ui, sans-serif; }
        .qr-modal {
            position: fixed; top:0; left:0; width:100%; height:100%;
            background: rgba(0,0,0,0.8);
            display:flex; align-items:center; justify-content:center;
        }
    </style>
</head>

<body class="bg-light p-4">

<div class="container">

    <div class="card shadow-sm">
        <div class="card-body">

            <h3 class="mb-3">
                <?php echo htmlspecialchars($offer['hotel_nom']); ?>
                <span class="text-warning"><?php echo htmlspecialchars($offer['hotel_cat']); ?>⭐</span>
            </h3>

            <p><strong>Chambre :</strong> <?php echo htmlspecialchars($offer['hotel_chb']); ?></p>
            <p><strong>Prix :</strong> <?php echo htmlspecialchars($offer['hotel_prix']); ?> €</p>
            <p><strong>Adresse :</strong> <?php echo htmlspecialchars($offer['adresse']); ?></p>
            <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($offer['tel']); ?></p>
            <p><strong>Email :</strong> <?php echo htmlspecialchars($offer['email']); ?></p>

            <hr>

            <!-- Boutons -->
            <div class="d-flex gap-2">

                <button class="btn btn-primary" onclick="shareOffer()">Share</button>

                <button class="btn btn-success" onclick="reserveOffer()">Réserver</button>

                <button class="btn btn-secondary" onclick="showQR()">QR Code</button>

            </div>

        </div>
    </div>

</div>

<script>
function reserveOffer() {
    const url = "<?php echo htmlspecialchars($offer['hotel_url'] ?? ''); ?>";

    if (!url) {
        alert("Aucune URL de réservation n'a été fournie.");
        return;
    }

    window.open(url, "_blank");
}

function shareOffer() {
    const text = `
Offre Hôtel : <?php echo $offer['hotel_nom']; ?>

Catégorie : <?php echo $offer['hotel_cat']; ?>⭐
Chambre : <?php echo $offer['hotel_chb']; ?>
Prix : <?php echo $offer['hotel_prix']; ?> €
Adresse : <?php echo $offer['adresse']; ?>

Téléphone : <?php echo $offer['tel']; ?>
Email : <?php echo $offer['email']; ?>

Voir l'offre : <?php echo "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>
`.trim();

    if (navigator.share) {
        navigator.share({
            title: "<?php echo $offer['hotel_nom']; ?>",
            text: text,
            url: "<?php echo "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>"
        });
        return;
    }

    navigator.clipboard.writeText(text);
    alert("Offre copiée dans le presse‑papier.");
}

function showQR() {
    const url = "<?php echo "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>";
    const qr = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" + encodeURIComponent(url);

    const modal = document.createElement("div");
    modal.className = "qr-modal";

    const img = new Image();
    img.src = qr;
    img.style.width = "250px";

    modal.appendChild(img);

    modal.onclick = () => modal.remove();

    document.body.appendChild(modal);
} 

<?php
$pdo = new PDO('mysql:host=localhost;dbname=lavache;charset=utf8', 'root', '');

$today = date('Y-m-d');

// 1. Désactiver les offres dont la date est différente d'aujourd'hui
$pdo->prepare("UPDATE offers SET active = 0 WHERE date_offre != ?")
    ->execute([$today]);

// 2. Récupérer l'offre du jour
$stmt = $pdo->prepare("SELECT * FROM offers WHERE date_offre = ? AND active = 1 LIMIT 1");
$stmt->execute([$today]);
$offer = $stmt->fetch(PDO::FETCH_ASSOC);

// 3. Si aucune offre pour aujourd'hui → demander saisie
if (!$offer) {
    echo "<div class='alert alert-warning text-center'>
            Aucune offre active aujourd'hui.<br>
            Veuillez saisir l'offre du jour.
          </div>";
    exit;
}

// 4. Affichage de l'offre du jour
echo "<h1>{$offer['titre']}</h1>";
echo "<p>Prix : {$offer['prix']} €</p>";
?>


</script>

</body>
</html>
